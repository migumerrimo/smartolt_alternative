<?php

namespace App\Services;

use phpseclib3\Net\SSH2;
use Exception;

class OltSshService
{
    protected $ssh;

    public function __construct($ip, $port, $user, $pass)
    {
        $this->ssh = new SSH2($ip, $port);

        if (!$this->ssh->login($user, $pass)) {
            throw new Exception("Error al conectar por SSH");
        }
    }

    public function getVlans()
    {
        // Asegurar que estamos en un prompt limpio
        $this->ssh->write("\r\n");
        usleep(200000);

        // Algunos dispositivos Huawei MA5680T requieren entrar en enable y una
        // variante específica del comando. Usar 'enable' y luego 'display vlan all smart'.
        $this->ssh->write("enable\r\n");
        usleep(150000);
        // Solicitar todas las VLANs de tipo smart (ajustar si se necesitan otros tipos)
        $this->ssh->write("display vlan all smart\r\n");
        // exit para cerrar sesión (evitar prompts de guardado)
        $this->ssh->write("exit\r\n");
        usleep(300000);

        $output = $this->ssh->read();

        return [
            "status" => "success",
            "raw" => $output
        ];
    }

    /**
     * Ejecuta un comando (o arreglo de comandos) y devuelve la salida cruda.
     * @param string|array $cmd
     * @return array
     */
    public function exec($cmd)
    {
        // Permitir que las operaciones SSH de larga duración finalicen sin tiempo de espera de PHP
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        // Asegurar prompt
        $this->ssh->write("\r\n");
        usleep(200000);

        $output = '';

        $isArray = is_array($cmd);
        $commands = $isArray ? $cmd : [$cmd];

        foreach ($commands as $c) {
            $this->ssh->write($c . "\r\n");
            // Dar tiempo al dispositivo para responder
            usleep(200000);
            // Leer lo que el dispositivo devolvió después de este comando
            $partial = $this->ssh->read();
            $output .= $partial;
        }

        // Si el último comando no fue exit/quit, enviar exit para cerrar la sesión
        $last = trim(end($commands));
        if (!in_array(strtolower($last), ['exit', 'quit'])) {
            $this->ssh->write("exit\r\n");
            usleep(200000);
            $output .= $this->ssh->read();
        }

        // Drenar cualquier salida restante (leer hasta que sea estable)
        $prevLen = 0;
        $tries = 0;
        while ($tries < 5) {
            usleep(150000);
            $chunk = $this->ssh->read();
            if ($chunk === null || $chunk === '') {
                $tries++;
                continue;
            }
            $output .= $chunk;
            if (strlen($output) === $prevLen) {
                $tries++;
            } else {
                $prevLen = strlen($output);
                $tries = 0;
            }
        }

        return [
            'status' => 'success',
            'raw' => $output
        ];
    }

    public function getAlarms()
    {
        // Comando simple; mantenemos para compatibilidad
        return $this->exec('display alarm active');
    }

    /**
     * Obtiene el historial/completo de alarmas en Huawei MA5680T.
     * Usa enable y desactiva el paginado para evitar prompts.
     */
    public function getAlarmHistory()
    {
        $this->ssh->write("\r\n");
        usleep(200000);
        $this->ssh->write("enable\r\n");
        usleep(150000);
        // Evita paginación interactiva
        $this->ssh->write("screen-length 0 temporary\r\n");
        usleep(150000);
        $this->ssh->write("display alarm history all\r\n");
        usleep(400000);
        $this->ssh->write("exit\r\n");
        usleep(300000);
        $output = $this->ssh->read();
        return [
            'status' => 'success',
            'raw' => $output
        ];
    }

    /**
     * Obtiene porcentaje de uso de memoria (10 minutos) en Huawei MA5680T.
     * Formato esperado: "Average usage rate of system memory in 10 minutes: 75%"
     */
    public function getMemoryUsage()
    {
        $this->ssh->write("\r\n");
        usleep(200000);
        $this->ssh->write("enable\r\n");
        usleep(150000);
        $this->ssh->write("screen-length 0 temporary\r\n");
        usleep(150000);
        $this->ssh->write("display resource occupancy mem\r\n");
        usleep(300000);
        $this->ssh->write("exit\r\n");
        usleep(300000);
        $output = $this->ssh->read();

        $percent = null;
        if (preg_match('/Average\s+usage\s+rate\s+of\s+system\s+memory\s+in\s+10\s+minutes:\s*(\d{1,3})%/i', $output, $m)) {
            $percent = min(100, max(0, (int)$m[1]));
        }

        return [
            'status' => 'success',
            'raw' => $output,
            'percent' => $percent
        ];
    }

    /**
     * Obtiene porcentaje de uso de CPU (10 minutos) en Huawei MA5680T.
     * Formato esperado: "Average usage rate of system cpu in 10 minutes: 11%"
     */
    public function getCpuOccupancy()
    {
        $this->ssh->write("\r\n");
        usleep(200000);
        $this->ssh->write("enable\r\n");
        usleep(150000);
        $this->ssh->write("screen-length 0 temporary\r\n");
        usleep(150000);
        $this->ssh->write("display resource occupancy cpu\r\n");
        usleep(300000);
        $this->ssh->write("exit\r\n");
        usleep(300000);
        $output = $this->ssh->read();

        $percent = null;
        if (preg_match('/Average\s+usage\s+rate\s+of\s+system\s+cpu\s+in\s+10\s+minutes:\s*(\d{1,3})%/i', $output, $m)) {
            $percent = min(100, max(0, (int)$m[1]));
        }

        return [
            'status' => 'success',
            'raw' => $output,
            'percent' => $percent
        ];
    }

    /**
     * Obtiene porcentaje de uso de almacenamiento FLASH en Huawei MA5680T.
     * Usa total size e idle size para calcular el porcentaje utilizado.
     */
    public function getStorageOccupancy()
    {
        $this->ssh->write("\r\n");
        usleep(200000);
        $this->ssh->write("enable\r\n");
        usleep(150000);
        $this->ssh->write("screen-length 0 temporary\r\n");
        usleep(150000);
        $this->ssh->write("display file\r\n");
        usleep(300000);
        $this->ssh->write("exit\r\n");
        usleep(300000);
        $output = $this->ssh->read();

        $percent = null;
        $totalSize = null;
        $idleSize = null;

        // Parsea "FLASH VFS total size: 253952(K)"
        if (preg_match('/FLASH\s+VFS\s+total\s+size:\s*(\d+)\s*\(K\)/i', $output, $m)) {
            $totalSize = (int)$m[1];
        }

        // Parsea "FLASH VFS idle size: 10861(K)"
        if (preg_match('/FLASH\s+VFS\s+idle\s+size:\s*(\d+)\s*\(K\)/i', $output, $m)) {
            $idleSize = (int)$m[1];
        }

        // Calcula porcentaje utilizado: ((total - idle) / total) * 100
        if ($totalSize !== null && $idleSize !== null && $totalSize > 0) {
            $usedSize = $totalSize - $idleSize;
            $percent = (int)round(($usedSize / $totalSize) * 100);
            $percent = min(100, max(0, $percent));
        }

        return [
            'status' => 'success',
            'raw' => $output,
            'percent' => $percent,
            'total_size' => $totalSize,
            'idle_size' => $idleSize
        ];
    }

    public function listOnus()
    {
        return $this->exec('display onu all');
    }

    public function getSystem()
    {
        return $this->exec('display version');
    }

    /**
     * Obtiene los Service Profiles de la OLT (MA5680T)
     * Ejecuta: enable + display ont-srvprofile gpon all
     */
    public function getServiceProfiles()
    {
        $this->ssh->write("\r\n");
        usleep(200000);
        $this->ssh->write("enable\r\n");
        usleep(150000);
        $this->ssh->write("display ont-srvprofile gpon all\r\n");
        usleep(300000);
        $this->ssh->write("exit\r\n");
        usleep(300000);
        $output = $this->ssh->read();
        return [
            'status' => 'success',
            'raw' => $output
        ];
    }

    /**
     * Obtiene los DBA Profiles de la OLT (MA5680T)
     * Ejecuta: enable + display dba-profile all
     */
    public function getDbaProfiles()
    {
        $this->ssh->write("\r\n");
        usleep(200000);
        $this->ssh->write("enable\r\n");
        usleep(150000);
        $this->ssh->write("display dba-profile all\r\n");
        usleep(300000);
        $this->ssh->write("exit\r\n");
        usleep(300000);
        $output = $this->ssh->read();
        return [
            'status' => 'success',
            'raw' => $output
        ];
    }
}
