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

class UnconnectedPing extends Packet {
    public static $id = 0x01;

    /** @var int */
    public $pingID;

    public function decode(): void{
        $this->offset = 1;
        $this->pingID = $this->getLong();
    }
}