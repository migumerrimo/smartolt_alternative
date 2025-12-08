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
        // Ensure we are on a fresh prompt
        $this->ssh->write("\r\n");
        usleep(200000);

        // Some Huawei MA5680T devices require entering enable and a specific
        // variant of the command. Use 'enable' then 'display vlan all smart'.
        $this->ssh->write("enable\r\n");
        usleep(150000);
        // Request all VLANs of type smart (adjust if you need other types)
        $this->ssh->write("display vlan all smart\r\n");
        // exit to close session (avoid save prompts)
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
        // Ensure prompt
        $this->ssh->write("\r\n");
        usleep(200000);

        $output = '';

        $isArray = is_array($cmd);
        $commands = $isArray ? $cmd : [$cmd];

        foreach ($commands as $c) {
            $this->ssh->write($c . "\r\n");
            // Give device some time to respond
            usleep(200000);
            // Read whatever the device returned after this command
            $partial = $this->ssh->read();
            $output .= $partial;
        }

        // If the last command was not an exit/quit, send an exit to close session
        $last = trim(end($commands));
        if (!in_array(strtolower($last), ['exit', 'quit'])) {
            $this->ssh->write("exit\r\n");
            usleep(200000);
            $output .= $this->ssh->read();
        }

        // Drain any remaining output (read until stable)
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
        // Comando genérico; ajusta según el fabricante/modelo si es necesario
        return $this->exec('display alarm active');
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
