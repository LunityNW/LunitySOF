<?php

use lunity\network\packet\raknet\AcknowledgePacket;

class NACKPacket extends AcknowledgePacket{
    public static $id = 0xa0;
}