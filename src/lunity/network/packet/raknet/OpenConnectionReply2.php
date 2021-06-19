<?php


namespace lunity\network\packet\raknet;


use lunity\network\packet\Packet;
use lunity\network\raklib\RakLib;

class OpenConnectionReply2 extends Packet {

    public static $id = 0x08;

    public $serverID;
    public $address = "";
    public $port = 0;
    public $mtu;

    public function encode() {
        $this->buffer = chr(self::$id);
        $this->buffer .= RakLib::MAGIC;

        $this->putLong($this->serverID);
        $this->putAddress($this->address, $this->port);
        $this->putShort($this->mtu);
        $this->buffer .= chr(0x00);

    }

}