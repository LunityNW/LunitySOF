<?php


namespace lunity\network\server;


use lunity\network\packet\raknet\BatchPacket;
use lunity\network\packet\raknet\FramesData;
use lunity\network\raklib\SessionManager;

class Session {

    public $sessionManager;
    public const Disconnected = 0;
    public const OpeningConection = 1;
    public const Connected = 2;
    public static $state;
    public $address, $port;

    public function __construct(SessionManager $sessionManager, $address, $port) {
        $this->sessionManager = $sessionManager;
        $this->address = $address;
        $this->port = $port;
    }

    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }

    /**
     * @param int $int
     */
    public function setState(int $int) {
        self::$state = $int;
    }

    /**
     * @return mixed
     */
    public function getState() {
        return self::$state;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getPort() {
        return $this->port;
    }

    public function handle($buff) {
        $frame = new FramesData();
        $frame->buffer = $buff;
        $frame->decode();

        foreach ($frame->packets as $pack) {
            $packet = $pack[0];
            $settings = $pack[1];
            $id = ord($packet{0}); //packet ID

            switch ($id) {
                case BatchPacket::$id:
                    //continue
                break;
            }
        }

    }

    public function sendPacket($buff) {
        $this->getSessionManager()->getSocket()->send($buff, $this->getAddress(), $this->getPort());
    }

}