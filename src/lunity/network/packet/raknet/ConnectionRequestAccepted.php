<?php

namespace lunity\network\packet\raknet;

use lunity\network\packet\Packet;

class ConnectionRequestAccepted extends Packet {

    public static $id = 0x10;
    public $address;
    public $port;
    public $systemindex = 0;
    public $internalId = "����������������������������������������";
    public $ping;


    public function encode() {
        $this->buffer = chr(self::$id);

		$this->putShort(0);
		$this->buffer .= $this->internalId;

		$this->putLong($this->ping);
		$this->putLong(bcadd($this->ping, "1000"));


    }

}
