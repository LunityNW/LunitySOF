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

use lunity\network\raklib\Binary;
use lunity\network\packet\Packet;

class FramesData extends Packet
{
	/** @var string[] */
	public $packets = [];

	/** @var int */
	public $sequenceNumber;

	public function decode(): void{
		$this->offset = 1;
		$this->sequenceNumber = $this->getLTriad();
		//var_dump($this->sequenceNumber);

		do {
			$offset = 0;

			/** @var string */
			$data = substr($this->buffer, $this->offset);
			$options = [];

			FramePacket::fromBinary($data, $offset, $options);
			if($data === "") break;

			$this->offset += $offset;
			$this->packets[] = [$data, $options];
		} while(isset($this->buffer{$this->offset}));
	}
}