<?php

namespace App\Services;

use phpseclib3\Net\SSH2;

class OltSshService
{
    public function connect($host, $username, $password, $port = 22)
    {
        $ssh = new SSH2($host, $port);

        if (!$ssh->login($username, $password)) {
            return [
                'success' => false,
                'error'   => 'No se pudo iniciar sesión en la OLT'
            ];
        }

        return $ssh;
    }

    public function run($ssh, $command)
    {
        $ssh->write($command . "\n");
        return $ssh->read();
    }
}
