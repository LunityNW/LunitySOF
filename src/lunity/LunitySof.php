<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity;

use lunity\network\packet\ReadPackets;
use lunity\network\raklib\UDPSocket;
use lunity\network\SessionManager;
use lunity\utils\Logger;


class LunitySof {

    public $work = true;
    public $logger;
    public $socket;
    public $sessionManager;
    public $serverID;
  
    public function __construct(Logger $logger) {
        $this->logger = new Logger();
        $this->socket = new UDPSocket($this, "0.0.0.0", 19132);
        $this->sessionManager = new SessionManager($this);
        $this->readPackets = new ReadPackets($this, $this->socket);
        $this->logger->info("Iniciando LunitySof...");
        $this->init();
    }


    public function init() {
        while($this->work) {
            $this->sessionManager->init();
        }
    }

    public function getServerID() {
        return is_numeric($this->serverID) ? $this->serverID : $this->serverID = mt_rand(0, PHP_INT_MAX);
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
