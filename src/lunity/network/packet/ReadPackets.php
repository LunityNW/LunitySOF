<?php


namespace lunity\network\packet;


use lunity\LunitySof;
use lunity\network\packet\raknet\OpenConnectionReply1;
use lunity\network\packet\raknet\OpenConnectionReply2;
use lunity\network\packet\raknet\OpenConnectionRequest1;
use lunity\network\packet\raknet\OpenConnectionRequest2;
use lunity\network\packet\raknet\UnconnectedPing;
use lunity\network\packet\raknet\UnconnectedPong;
use lunity\network\server\SessionManager;
use lunity\network\raklib\UDPSocket;

class ReadPackets {

    protected $main;
    protected $socket;
    protected $sessionManager;

    public function __construct(LunitySof $main, UDPSocket $socket) {
        $this->main = $main;
        $this->socket = $socket;
    }

    public function readPackets($buff, $addres, $port) {
        $id = ord($buff{0});

        switch ($id) {
            case UnconnectedPing::$id:
            $packet = new UnconnectedPing();
            $packet->buffer = $buff;
            $packet->decode();

            $pong = new UnconnectedPong();
            $pong->pingID = $packet->pingID;
            $pong->serverID = $this->main->getServerID();
            $pong->serverName = "MCPE;LunitySOF;408;1.16.40;0;20;{$this->main->getServerID()};Lunity Nwtwork;Survival;1;19132;19133;";
            $pong->encode();
            $this->sendPacket($pong->buffer, $addres, $port);
            break;
            case OpenConnectionRequest1::$id:
                if (!$this->getSessionManager()->isSession($addres, $port)) {
                    $this->getSessionManager()->addSession($addres, $port);
                }

                $this->getSessionManager()->getSession($addres, $port)->procesConnection($buff);
            break;
            case OpenConnectionRequest2::$id:
                if ($this->getSessionManager()->isSession($addres, $port)) {
                    $this->getSessionManager()->getSession($addres, $port)->procesConnection($buff);
                }
            break;
        }
        if ($id >= 0x80 and $id <= 0x8d ) {
            $this->main->getLogger()->debug("el packete: " . $id . " es un FramePacket");
	    $this->getSessionManager()->getSession($addres, $port)->handle($buff);
        }
    }

    /**
     * @return LunitySof
     */
    public function getMain(): LunitySof {
        return $this->main;
    }

    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager {
        return $this->getMain()->sessionManager;
    }
    public function sendPacket($buff, $addres, $port) {
        $this->socket->send($buff, $addres, $port);
    }



}