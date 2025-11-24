<?php

namespace App\Services;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class OltSSHService
{
    protected $ssh;

    public function connect()
    {
        $host = env('OLT_HOST');
        $user = env('OLT_USERNAME');
        $pass = env('OLT_PASSWORD');

        $ssh = new SSH2($host);

        // Forzar algoritmos RSA como en tu comando
        $ssh->setPreferredAlgorithms([
            'hostkey' => ['ssh-rsa'],
            'publickey' => ['ssh-rsa'],
        ]);

        if (!$ssh->login($user, $pass)) {
            throw new \Exception("No se pudo autenticar vía SSH con la OLT");
        }

        $this->ssh = $ssh;
        return $ssh;
    }

    public function exec($command)
    {
        if (!$this->ssh) {
            $this->connect();
        }

        return $this->ssh->exec($command);
    }
}
