<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity;

use lunity\network\raklib\UDPSocket;
use lunity\network\raklib\SessionManager;
use lunity\utils\Logger;


class LunitySof {

    public $work = true;
    public $logger;
    public $socket;
    public $sessionManager;
  
    public function __construct(Logger $logger) {
        $this->logger = new Logger();
        $this->socket = new UDPSocket($this, "0.0.0.0", 19132);
        $this->sessionManager = new SessionManager($this, $this->socket, "0.0.0.0", 19132);
        $this->logger->info("corriendo");
        $this->init();
    }


    public function init() {

        while($this->work){
           $this->sessionManager->init();
        }
        
    }

    public function isWorking() {
        return $this->work;
    }

    public function close() {
        $this->work = false;
    }

    public function getLogger(): Logger {
        return $this->logger;
    }
  
}

?>
