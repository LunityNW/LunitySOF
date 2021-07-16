<?php


namespace lunity\network\server;


use lunity\LunitySof;
use lunity\network\packet\raknet\BatchPacket;
use lunity\network\packet\raknet\FramesData;
use lunity\network\packet\raknet\OpenConnectionReply1;
use lunity\network\packet\raknet\OpenConnectionReply2;
use lunity\network\packet\raknet\OpenConnectionRequest1;
use lunity\network\packet\raknet\OpenConnectionRequest2;
use lunity\network\packet\raknet\ConnectionRequest;
use lunity\network\packet\raknet\ConnectionRequestAccepted;
use lunity\network\server\SessionManager;
use lunity\network\raklib\Binary;
use lunity\network\packet\raknet\FramePacket;
use lunity\network\packet\raknet\PacketReliability;

class Session {

    public $sessionManager;
    public const DISCONNECTED = 0;
    public const REQUEST_ACCEPTED_ONE = 2;
    public const REQUEST_ACCEPTED_TOW = 3;
    public const CONNECTED = 5;
    public static $state;
    public $address, $port;

    /** @var array[][] */
    private $splitSystem = [];
    /** @var array */
    private $splitSettings = [];
    /** @var int */
    private $splitPointer = 0;
    /** @var int */
    private $sendSplitIndex = 0;

    /** @var int */
    private $orderIndex = 0;

    /** @var array[] */
    private $ackQueue = [];
    /** @var array[] */
    private $nackQueue = [];

    /** @var int */
    private $sequenceNumber = 0;
    /** @var int */
    private $highestSequenceNumber = -1;

    /** @var int */
    private $windowStart = 0;
    /** @var int */
    private $windowStop = 2048;

    public function __construct(SessionManager $sessionManager, $address, $port) {
        $this->sessionManager = $sessionManager;
        $this->address = $address;
        $this->port = $port;
        $this->setState(self::DISCONNECTED);
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

        /** @var int */
        $seq = $frame->sequenceNumber;

        if($seq < $this->windowStart || $seq > $this->windowStop || $seq > ($this->windowStart + 16) || isset($this->ackQueue[$seq])){
            return; //paquete ya recibido
        }

        if($seq > $this->highestSequenceNumber){
            $this->highestSequenceNumber = $seq;
        }

        //var_dump($seq);

        $this->sequenceNumber = $seq;

        if(isset($this->nackQueue[$seq])){
            unset($this->nackQueue[$seq]);
        }

        $this->ackQueue[$seq] = $seq;

        if($seq === $this->windowStart){
            for(; isset($this->ackQueue[$this->windowStart]); ++$this->windowStart){
                ++$this->windowStop;
                //var_dump("windowStart => ". $this->windowStart);
            }
        } else if($seq > $this->windowStart){
            //var_dump($this->windowStart ." => ". $this->highestSequenceNumber);
            for($i = $this->windowStart; $i < $this->highestSequenceNumber; ++$i){
                if(!isset($this->ackQueue[$i])){
                    $this->nackQueue[$i] = $i;
                    //var_dump("nack => ". $i);
                }
            }
        } else {
            return;
        }

        foreach ($frame->packets as $pack) {
            $packet = $pack[0];
            $settings = $pack[1];
            $id = ord($packet{0}); //packet ID
            $this->getMain()->getLogger()->debug("se recibio un FramePacket con id: " . $id );

            switch ($id) {
                case BatchPacket::$id:
                    $this->getMain()->getLogger()->debug("se recibio un BatchPaket");
                    $this->batchHandler($packet);
                break;
		        case ConnectionRequest::$id:
                    $this->procesConnection($packet);
                break;
                case 0x13:
                    $this->getMain()->getLogger()->debug("se recibio un Incomong");
                break;
            }
        }

    }

    public function procesConnection($buffer) {
        $id = ord($buffer{0});

        switch ($id) {
            case OpenConnectionRequest1::$id:
                $request1 = new OpenConnectionRequest1();
                $request1->buffer = $buffer;
                $request1->decode();

                $reply1 = new OpenConnectionReply1();
                $reply1->serverID = $this->getMain()->getServerID();
                $reply1->mtu = $request1->mtu;
                $reply1->encode();

                $this->sendPacket($reply1->buffer);
                $this->setState(self::REQUEST_ACCEPTED_ONE);
                $this->getMain()->getLogger()->info("se creo una nueva session");
                break;
            case OpenConnectionRequest2::$id:

                $request2 = new OpenConnectionRequest2();
                $request2->buffer = $buffer;
                $request2->decode();

                $reply2 = new OpenConnectionReply2();
                $reply2->serverID = $this->getMain()->getServerID();
                $reply2->address = $request2->address;
                $reply2->port = $request2->port;
                $reply2->mtu = min(1464, $request2->mtu);
                $reply2->encode();
                $this->sendPacket($reply2->buffer);
                $this->setState(self::REQUEST_ACCEPTED_TOW);
                $this->getMain()->getLogger()->info("se acepto la session");
            break;
            case ConnectionRequest::$id:
                $this->getMain()->getLogger()->debug("se recibio un Request Packet ");
                $packet = new ConnectionRequest();
                $packet->buffer = $buffer;
                $packet->decode();

                $connection = new ConnectionRequestAccepted();
                $connection->address = $this->getAddress();
                $connection->port = $this->getPort();
                $connection->ping = $packet->pingtime;
                $connection->encode();
                $this->sendFrame($connection->buffer);
                $this->getMain()->getLogger()->debug("Request aceptado :D");
            break;

        }
    }

    //Wertex-Master
    private function sendFrame(string $buffer): void{
        FramePacket::toBinary($buffer, PacketReliability::UNRELIABLE);
        $this->getSessionManager()->getSocket()->send("\x80". Binary::writeLTriad($this->sequenceNumber++) . $buffer, $this->address, $this->port);
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
