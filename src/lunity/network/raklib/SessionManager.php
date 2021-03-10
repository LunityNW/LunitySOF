<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity\network\raklib;

use lunity\LunitySof;

class SessionManager {



    public $main;
    public $socket;
    public $addres;
    public $port;
    /** Array Session $sessions */
    public $sessions = [];

    public function __construct(LunitySof $main, UDPSocket $socket, $interfice = "0.0.0.0", int $port = 19132) {
        $this->main = $main;
        $this->socket = $socket;
        $this->address = $interfice;
        $this->port = $port;

    }

    

    public function getServerIP() {
        return $this->addres;
    }

    public function getServerPort(): int {
        return $this->addres;
    }


}
?>
