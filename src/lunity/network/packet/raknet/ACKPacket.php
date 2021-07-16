<?php

namespace lunity\network\packet\raknet;

use lunity\network\packet\raknet\AcknowledgePacket;

class ACKPacket extends AcknowledgePacket{
    public static $id = 0xc0;
}