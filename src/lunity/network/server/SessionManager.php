<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity\network\server;

use lunity\LunitySof;
use lunity\network\raklib\UDPSocket;
use lunity\network\raklib\packet\UNCONNECTED_PING;
use lunity\network\raklib\packet\UNCONNECTED_PONG;
use lunity\network\server\Session;

class SessionManager {

    /** @var LunitySof $main */
    protected $main;
    /** @var \lunity\network\raklib\UDPSocket  $socket */
    protected $socket;
    /** @Array Session $sessions */
    public $sessions = [];

    public function __construct(LunitySof $main) {
        $this->main = $main;
        $this->socket = $main->socket;

    }

    public function init() {
        $socket = $this->socket->recive($buffer, $address, $port);
        $this->main->readPackets->readPackets($buffer, $address, $port);
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

    /**
     * @param $address
     * @param $port
     * @return Session
     */
    public function getSession($address, $port) {
        return $this->sessions["$address:$port"];
    }

    /**
     * @param $address
     * @param $port
     * @return bool
     */
    public function isSession($address, $port) {
        return isset($this->sessions["$address:$port"]);
    }

    /**
     * @param $address
     * @param $port
     */
    public function addSession($address, $port) {
        $this->sessions["$address:$port"] = new Session($this, $address, $port);
    }

    /**
     * @return LunitySof
     */
    public function getMain(): LunitySof {
        return $this->main;
    }

    /**
     *
     * @return \lunity\network\raklib\UDPSocket
     */
    public function getSocket(): \lunity\network\raklib\UDPSocket {
        return $this->socket;
    }

}
?>
