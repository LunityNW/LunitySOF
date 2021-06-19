<?php


namespace lunity\network\packet\raknet;


use lunity\network\packet\Packet;
use lunity\network\raklib\Binary;
use lunity\network\raklib\RakLib;

class OpenConnectionReply1 extends Packet {

    public static $id = 0x06;

    public $serverID;
    public $security = 0;
    public $mtu;

    public function encode() {
        parent::encode();
        $this->putMagic(); //magic
        $this->putLong($this->serverID);
        $this->put(0x00);
        $this->putShort($this->mtu);
    }

}