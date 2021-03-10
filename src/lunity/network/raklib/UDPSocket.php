<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity\network\raklib;

use lunity\LunitySof;

class UDPSocket {


    /** Socket $socket */
    public $socket;
    /** LunitySof $main */
    public $main;

    public function __construct(LunitySof $main ,$address, $port) {
        $this->main = $main;
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);


       
        if (@socket_bind($this->socket, $address, $port)) {
             $this->main->getLogger()->info("socket creado correctamente");
        }

        
    }

    public function recive(&$buffer, &$address, &$port) {
        return socket_recvfrom($this->socket, $buffer, 65535, 0, $address, $port);
    }

    public function send($buffer, $address, $port) {
        return socket_sendto($this->socket, $buffer, strlen($buffer), 0, $address, $port);
    }

    public function close() {
        socket_close($this->socket);
        $this->main->getLogger()->debug("socket cerrado");
    }

}


?>