<?php

namespace lunity\network\packet\raknet;

use lunity\network\packet\Packet;

class ConnectionRequest extends Packet {

    public static $id = 0x09;

    public $guid;
    public $pingtime;

    public function decode() {
        parent::decode();
        $this->guid = $this->getLong();
        $this->pingtime = $this->getLong();
    }

}