<?php


namespace lunity\network\server;


use lunity\LunitySof;
use lunity\network\packet\raknet\BatchPacket;
use lunity\network\packet\raknet\FramesData;
use lunity\network\packet\raknet\OpenConnectionReply1;
use lunity\network\packet\raknet\OpenConnectionReply2;
use lunity\network\packet\raknet\OpenConnectionRequest1;
use lunity\network\packet\raknet\OpenConnectionRequest2;
use lunity\network\server\SessionManager;

class Session {

    public $sessionManager;
    public const Disconnected = 0;
    public const OpeningConnection = 1;
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
            $this->getMain()->getLogger()->debug("se recibio un FramePacket con id: " . $id );

            switch ($id) {
                case BatchPacket::$id:
                    $this->batchHandler($packet);
                break;
            }
        }

    }

    public function procesConnection($buffer) {
        $id = ord($buffer{0});

        switch ($id) {
            case OpenConnectionRequest1::$id:
                $requiest1 = new OpenConnectionRequest1();
                $requiest1->buffer = $buffer;
                $requiest1->decode();

                $reply1 = new OpenConnectionReply1();
                $reply1->serverID = $this->getMain()->getServerID();
                $reply1->mtu = $requiest1->mtu;
                $reply1->encode();

                $this->sendPacket($reply1->buffer);
                break;
            case OpenConnectionRequest2::$id:

                $requiest2 = new OpenConnectionRequest2();
                $requiest2->buffer = $buffer;
                $requiest2->decode();

                $reply2 = new OpenConnectionReply2();
                $reply2->serverID = $this->getMain()->getServerID();
                $reply2->address = $requiest2->address;
                $reply2->port = $requiest2->port;
                $reply2->mtu = min(1464, $requiest2->mtu);
                $reply2->encode();
                $this->sendPacket($reply2->buffer);
                break;
        }
    }

    public function batchHandler($buffer) {
        $pack = new BatchPacket();
        $pack->buffer = $buffer;
        $pack->decode();

        foreach ($pack->getPackets() as $packet) {
            $id = ord($packet{0});
            $this->getMain()->getLogger()->debug("se recibio un BatchPacket con id: " . $id );
        }
    }

    public function getMain(): LunitySof {
        return $this->getSessionManager()->getMain();
    }

    public function sendPacket($buff) {
        $this->getSessionManager()->getSocket()->send($buff, $this->getAddress(), $this->getPort());
    }

}
