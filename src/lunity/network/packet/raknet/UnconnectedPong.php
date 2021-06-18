<?php

/**
 * @package wertex
 * @author encluced
 * __        __        _
 * \ \      / /__ _ __| |_ _____  __
 *  \ \ /\ / / _ \ '__| __/ _ \ \/ /
 *   \ V  V /  __/ |  | ||  __/>  <
 *    \_/\_/ \___|_|   \__\___/_/\_\
 *
 * @link https://github.com/encluced
 * @link https://github.com/WertexTeam/wertex
 */

namespace lunity\network\packet\raknet;


use lunity\network\packet\Packet;
use lunity\network\raklib\RakLib;

class UnconnectedPong extends Packet
{
    public static $id = 0x1c;

    /** @var int */
    public $pingID;
    /** @var int */
    public $serverID;

    /** @var string */
    public $serverName;

    public function encode(): void{
        $this->offset = 0;
        $this->buffer = chr(self::$id);

        $this->putLong($this->pingID);
        $this->putLong($this->serverID);

        $this->buffer .= RakLib::MAGIC;

        $this->putShort(strlen($this->serverName));
        $this->buffer .= $this->serverName;
    }
}