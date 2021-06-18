<?php


namespace lunity\network\packet\raknet;


use lunity\network\packet\Packet;

class OpenConnectionRequest1 extends Packet {

    public static $id = 0x05;

    public $magic;
    public $protocol;
    public $mtu;

    public function decode() {
        parent::decode();
        $this->offset += 16;
        $this->protocol = $this->getByte();
        $this->mtu = strlen(substr($this->buffer, 18)) + 18;
    }

}