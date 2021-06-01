<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity\network\raklib;

use lunity\LunitySof;
use lunity\network\raklib\UDPSocket;
use lunity\network\raklib\packet\UNCONNECTED_PING;
use lunity\network\raklib\packet\UNCONNECTED_PONG;

class SessionManager {

    protected $main;
    protected $socket;
    protected $addres;
    protected $port;
    protected $serverID;
    /** Array Session $sessions */
    public $sessions = [];

    public function __construct(LunitySof $main, UDPSocket $socket, $interfice = "0.0.0.0", int $port = 19132) {
        $this->main = $main;
        $this->socket = $socket;
        $this->address = $interfice;
        $this->port = $port;
    }

    public function init() {
        $socket = $this->socket->recive($buffer, $address, $port);
        while($this->main->isWorking()) {
            $this->readPackets($buffer, $address, $port);
        }
    }

    public function readPackets($buffer, $address, $port) {
        if ($buffer !== null) {
            $id = ord($buffer{0});

            switch($id) {
                case UNCONNECTED_PING::$ID: 
                $packet = new UNCONNECTED_PING();
                $packet->buffer = $buffer;
                $packet->decode();

                $pk = new UNCONNECTED_PONG();
                $pk->pingID = $packet->pingID;
                $pk->serverID = $this->getServerID();
                $pk->serverName = "MCPE;LunitySOF;408;1.16.40;0;60;survival;lunity";
                $pk->encode();
                $this->sendPacket($pk->buffer, $address, $port);
                
            }
        }
    }

    public function sendPacket($buff, $address, $port) {
        $this->socket->send($buff, $address, $port);
    }

    public function getServerID() {
        return (is_numeric($this->serverID)) ? $this->serverID : $this->serverID = rand(0, PHP_INT_MAX);
    }

    public function getServerIP() {
        return $this->addres;
    }

    public function getServerPort(): int {
        return $this->addres;
    }
}
?>
