<?php


namespace lunity\network;


use lunity\LunitySof;

class SessionManager {
    /** @var LunitySof $main */
    public $main;
    /** @var raklib\UDPSocket $socket */
    public $socket;

    protected $sessions = [];

    public function __construct(LunitySof $main) {
        $this->main = $main;
        $this->socket = $main->socket;
    }

    public function init() {
        if ($this->socket->recive($buffer, $address, $port) > 0) {
            $this->main->readPackets->readPackets($buffer, $address, $port);
        }
    }

}