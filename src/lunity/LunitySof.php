<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity;

use lunity\network\raklib\UDPSocket;
use lunity\utils\Logger;

class LunitySof {

    public $work;
    public $logger;
    public $socket;
  
    public function __construct(Logger $logger) {
        $this->logger = new Logger();
        $this->socket = new UDPSocket($this, "0.0.0.0", 19132);
        $this->work = true;
        $this->logger->info("corriendo");
        $this->init();
    }

    public function init() {
        $this->logger->info("Corriendo Work");
        while($this->work){
            $soket = $this->socket->recive($buffer, $address, $port);
            $this->logger->info("se recivio paquete con el ID: " . ord($buffer{0}));
            
        }
    }

    public function getLogger(): Logger {
        return $this->logger;
    }
  
}

?>
