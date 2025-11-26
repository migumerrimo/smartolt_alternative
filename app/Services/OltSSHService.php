<?php

namespace App\Services;

use phpseclib3\Net\SSH2;
use phpseclib3\Exception\UnableToConnectException;
use Exception;
use Illuminate\Support\Facades\Log;

class OltSSHService
{
    protected $ssh = null;
    protected $isConnected = false;
    protected $maxRetries = 2;
    protected $retryDelay = 3;

    public function connect()
    {
        // Si ya estamos conectados, no hacer nada
        if ($this->isConnected && $this->ssh !== null) {
            return true;
        }

        $host = env('OLT_HOST');
        $user = env('OLT_USERNAME');
        $pass = env('OLT_PASSWORD');

        // Validar configuración básica
        if (!$host || !$user || !$pass) {
            Log::warning('OLT: Configuración SSH incompleta');
            return false;
        }

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                Log::info("OLT: Intentando conexión {$attempt}/{$this->maxRetries} a {$host}");

                $this->ssh = new SSH2($host);
                $this->ssh->setTimeout(15);

                // Configuración de algoritmos
                $this->ssh->setPreferredAlgorithms([
                    'hostkey' => ['ssh-rsa'],
                    'publickey' => ['ssh-rsa'],
                ]);

                if (!$this->ssh->login($user, $pass)) {
                    throw new Exception("Autenticación fallida");
                }

                $this->isConnected = true;
                Log::info("OLT: Conexión SSH establecida exitosamente");
                return true;

            } catch (UnableToConnectException $e) {
                Log::warning("OLT: Error de conexión (attempt {$attempt}): " . $e->getMessage());
                
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            } catch (Exception $e) {
                Log::error("OLT: Error inesperado (attempt {$attempt}): " . $e->getMessage());
                
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }

            // Limpiar para el siguiente intento
            $this->ssh = null;
            $this->isConnected = false;
        }

        Log::error("OLT: No se pudo establecer conexión después de {$this->maxRetries} intentos");
        return false;
    }

    public function exec($command)
    {
        try {
            // Intentar conectar si no estamos conectados
            if (!$this->isConnected && !$this->connect()) {
                throw new Exception("No hay conexión disponible con la OLT");
            }

            $output = $this->ssh->exec($command);
            
            // Verificar si la conexión sigue activa
            if ($output === false || !$this->ssh->isConnected()) {
                $this->isConnected = false;
                throw new Exception("Conexión perdida durante la ejecución");
            }

            return $output;

        } catch (Exception $e) {
            Log::error("OLT: Error ejecutando comando '{$command}': " . $e->getMessage());
            $this->isConnected = false;
            throw new Exception("Error en comunicación con OLT: " . $e->getMessage());
        }
    }

    public function isConnected()
    {
        return $this->isConnected && $this->ssh !== null;
    }

    public function disconnect()
    {
        if ($this->ssh) {
            try {
                $this->ssh->disconnect();
            } catch (Exception $e) {
                // Ignorar errores en desconexión
            }
        }
        
        $this->ssh = null;
        $this->isConnected = false;
        Log::info("OLT: Desconectado");
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Método seguro para ejecutar comandos que no rompe la aplicación
     */
    public function safeExec($command, $default = '')
    {
        try {
            return $this->exec($command);
        } catch (Exception $e) {
            Log::warning("OLT: Comando falló '{$command}': " . $e->getMessage());
            return $default;
        }
    }
}