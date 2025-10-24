<?php

namespace App\Services;

use phpseclib3\Net\Telnet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class OltTelnetService
{
    protected ?Telnet $telnet = null;
    protected string $host;
    protected int $port;
    protected string $user;
    protected string $pass;
    protected int $timeout;
    protected int $retries;

    public function __construct()
    {
        $this->host = env('OLT_HOST', '192.168.10.10');
        $this->port = (int) env('OLT_PORT', 23);
        $this->user = env('OLT_USER', 'admin');
        $this->pass = env('OLT_PASS', 'Huawei123');
        $this->timeout = (int) env('OLT_TELNET_TIMEOUT', 10);
        $this->retries = (int) env('OLT_TELNET_RETRIES', 2);
    }

    /**
     * Conecta al dispositivo Huawei por Telnet y autentica.
     */
    public function connect(): void
    {
        $attempt = 0;
        while ($attempt <= $this->retries) {
            try {
                $attempt++;
                Log::info("Conectando a OLT Huawei MA5680T vía Telnet: {$this->host}:{$this->port}");
                $this->telnet = new Telnet($this->host, $this->port, $this->timeout);

                $this->readUntil(['Username:', 'Login:']);
                $this->write($this->user);

                $this->readUntil('Password:');
                $this->write($this->pass);

                // Espera prompt de usuario
                $output = $this->readUntil(['>', '#', ']']);

                if (strpos($output, '>') === false && strpos($output, ']') === false) {
                    throw new Exception('Error de autenticación Telnet.');
                }

                Log::info("Conexión Telnet establecida con {$this->host}");
                return;
            } catch (Exception $e) {
                Log::warning("Error Telnet intento {$attempt}: " . $e->getMessage());
                if ($attempt >= $this->retries) {
                    throw new Exception("No se pudo conectar a la OLT Huawei tras {$this->retries} intentos.");
                }
                sleep(1);
            }
        }
    }

    /**
     * Cierra la conexión.
     */
    public function disconnect(): void
    {
        if ($this->telnet) {
            try {
                $this->telnet->disconnect();
            } catch (Exception $e) {
                Log::warning("Error al cerrar Telnet: " . $e->getMessage());
            }
        }
        $this->telnet = null;
    }

    /**
     * Ejecuta un comando en la OLT (modo lectura o configuración).
     * Detecta automáticamente si requiere 'system-view'.
     */
    public function exec(string $command, bool $configMode = false): string
    {
        $this->connect();

        $lockKey = 'olt_telnet_exec_lock_' . md5($this->host);
        $acquired = Cache::add($lockKey, true, 5);
        if (!$acquired) {
            throw new Exception('La OLT está ejecutando otro comando, intenta más tarde.');
        }

        try {
            if ($configMode) {
                $this->enterConfigMode();
            }

            Log::info("Ejecutando comando Telnet: {$command}");
            $this->write($command);
            $response = $this->readUntil(['>', ']', '#', 'More']);
            $response = $this->normalizeOutput($response);

            // Confirmaciones (por ejemplo "Are you sure? [Y/N]")
            if (preg_match('/(Are you sure|confirm|Confirm)/i', $response)) {
                $this->write('y');
                $response .= "\n" . $this->readUntil(['>', ']', '#']);
            }

            return trim($response);
        } finally {
            Cache::forget($lockKey);
            $this->disconnect();
        }
    }

    /**
     * Ejecuta una secuencia de comandos (ej: agregar ONU, crear VLAN)
     */
    public function execSequence(array $commands): string
    {
        $this->connect();
        $this->enterConfigMode();

        $output = '';
        foreach ($commands as $cmd) {
            $this->write($cmd);
            $out = $this->readUntil(['>', ']', '#', 'More']);
            $output .= "\n" . $this->normalizeOutput($out);
            usleep(200000);
        }

        $this->write('quit');
        $output .= "\n" . $this->readUntil(['>']);
        $this->disconnect();

        return trim($output);
    }

    /**
     * Entra en modo configuración (system-view).
     */
    protected function enterConfigMode(): void
    {
        $this->write('system-view');
        $output = $this->readUntil([']', '#']);
        if (strpos($output, ']') === false) {
            throw new Exception('No se pudo entrar a system-view (verifica privilegios del usuario).');
        }
    }

    /**
     * Obtiene información del sistema (display version).
     */
    public function getSystemStatus(): array
    {
        $raw = $this->exec('display version');
        $data = [
            'model' => null,
            'version' => null,
            'uptime' => null,
            'raw' => $raw,
        ];

        foreach (explode("\n", $raw) as $line) {
            if (preg_match('/Huawei\s+(\S+)/', $line, $m)) {
                $data['model'] = $m[1];
            }
            if (preg_match('/Version\s*:\s*(\S+)/', $line, $m)) {
                $data['version'] = $m[1];
            }
            if (preg_match('/Uptime\s*:\s*(.+)$/i', $line, $m)) {
                $data['uptime'] = trim($m[1]);
            }
        }

        return $data;
    }

    /**
     * Lista ONUs registradas.
     */
    public function listOnus(): array
    {
        $raw = $this->exec('display ont info 0 9 0 all');
        $onus = [];
        foreach (explode("\n", $raw) as $line) {
            if (preg_match('/ONT\s+(\d+).*SN\s*:\s*([A-Z0-9]+).*State\s*:\s*(\w+)/i', $line, $m)) {
                $onus[] = [
                    'onu_id' => (int)$m[1],
                    'serial' => $m[2],
                    'state' => strtolower($m[3]),
                ];
            }
        }
        return count($onus) ? $onus : ['raw' => $raw];
    }

    /**
     * Agrega una nueva ONU.
     */
    public function addOnu(int $slot, int $port, string $sn, int $lineProfile, int $srvProfile, string $desc = ''): array
    {
        $commands = [
            "interface gpon 0/{$slot}",
            sprintf('ont add %d sn-auth "%s" omci ont-lineprofile-id %d ont-srvprofile-id %d desc "%s"', $port, $sn, $lineProfile, $srvProfile, $desc),
            "quit",
            "save",
        ];

        $out = $this->execSequence($commands);
        return ['success' => true, 'output' => $out];
    }

    /**
     * Crea una VLAN.
     */
    public function createVlan(int $vlanId, string $desc = ''): array
    {
        $commands = [
            "vlan {$vlanId}",
            $desc ? "description {$desc}" : '',
            "quit",
            "save"
        ];
        $commands = array_filter($commands);
        $out = $this->execSequence($commands);
        return ['success' => true, 'output' => $out];
    }

    /**
     * Obtiene alarmas activas.
     */
    public function getActiveAlarms(): array
    {
        $raw = $this->exec('display alarm active all');
        $alarms = [];
        foreach (explode("\n", $raw) as $line) {
            if (preg_match('/(critical|major|minor|warning|info)\s+(.+)/i', $line, $m)) {
                $alarms[] = [
                    'severity' => strtolower($m[1]),
                    'message' => trim($m[2]),
                ];
            }
        }
        return count($alarms) ? $alarms : ['raw' => $raw];
    }

    /**
     * Ejecuta comando libre (display o config).
     */
    public function runCommand(string $command, bool $config = false): array
    {
        $out = $this->exec($command, $config);
        return ['command' => $command, 'output' => $out];
    }

    /* --------------------------------------------------------------
     * Helpers privados
     * -------------------------------------------------------------- */

    protected function write(string $cmd): void
    {
        $this->telnet->write(trim($cmd) . "\n");
    }

    protected function readUntil(array|string $patterns): string
    {
        if (is_string($patterns)) $patterns = [$patterns];
        $buffer = '';
        $start = time();

        while ((time() - $start) < $this->timeout) {
            $buffer .= $this->telnet->read();
            foreach ($patterns as $pattern) {
                if (str_contains($buffer, $pattern)) {
                    return $buffer;
                }
            }
            usleep(100000);
        }

        throw new Exception('Timeout de lectura Telnet.');
    }

    protected function normalizeOutput(string $output): string
    {
        $output = preg_replace("/(\x1B\[|\x1B)[^mK]*[mK]/", '', $output); // limpia caracteres ANSI
        return trim(str_replace(["\r\n", "\r"], "\n", $output));
    }
}
