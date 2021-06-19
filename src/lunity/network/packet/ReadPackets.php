<?php


namespace lunity\network\packet;


use lunity\LunitySof;
use lunity\network\packet\raknet\OpenConnectionReply1;
use lunity\network\packet\raknet\OpenConnectionReply2;
use lunity\network\packet\raknet\OpenConnectionRequest1;
use lunity\network\packet\raknet\OpenConnectionRequest2;
use lunity\network\packet\raknet\UnconnectedPing;
use lunity\network\packet\raknet\UnconnectedPong;
use lunity\network\raklib\UDPSocket;

class ReadPackets {

    protected $main;
    protected $socket;

    public function __construct(LunitySof $main, UDPSocket $socket) {
        $this->main = $main;
        $this->socket = $socket;
    }

    public function readPackets($buff, $addres, $port) {
        $id = ord($buff{0});

        $this->main->getLogger()->info("se recivio el packet: " . $id);

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
                $packet = new OpenConnectionRequest1();
                $packet->buffer = $buff;
                $packet->decode();

                $reply1 = new OpenConnectionReply1();
                $reply1->serverID = $this->main->getServerID();
                $reply1->mtu = $packet->mtu;
                $reply1->encode();

                $this->sendPacket($reply1->buffer, $addres, $port);
            break;
            case OpenConnectionRequest2::$id:
                $packet = new OpenConnectionRequest2();
                $packet->buffer = $buff;
                $packet->decode();

                $reply2 = new OpenConnectionReply2();
                $reply2->serverID = $this->main->getServerID();
                $reply2->address = $packet->address;
                $reply2->port = $packet->port;
                $reply2->mtu = min(1464, $packet->mtu);
                $reply2->encode();

                $this->sendPacket($reply2->buffer, $addres, $port);
            break;
        }
        if ($id >= 0x80 and $id <= 0x8d ) {
            $this->main->getLogger()->debug("el packete: " . $id . " es un FramePacket");
        }
    }

    public function sendPacket($buff, $addres, $port) {
        $this->socket->send($buff, $addres, $port);
    }



}