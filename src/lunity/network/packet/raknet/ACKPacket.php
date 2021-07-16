<?php

use lunity\network\packet\raknet\AcknowledgePacket;

class ACKPacket extends AcknowledgePacket{
    public static $id = 0xc0;
}