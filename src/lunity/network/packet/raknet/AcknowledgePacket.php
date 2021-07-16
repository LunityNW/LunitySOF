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

abstract class AcknowledgePacket extends Packet {
	/** @var int */
	public $start;
	/** @var int */
	public $stop;

	/** @var int */
	public $record = 1;

	public function encode(): void{
		$this->buffer = chr(static::$id);
		$this->putShort($this->record);

		if($this->start == $this->stop){
			$this->buffer .= "\x01";
			$this->putLTriad($this->stop);
		} else {
			$this->buffer .= "\x00";

			$this->putLTriad($this->start);
			$this->putLTriad($this->stop);
		}
	}

	public function decode(): void{
		$this->offset = 1;

		$this->record = $this->getShort();
		$startIsStop = (bool) ord($this->buffer{$this->offset++});

		if($startIsStop){
			$this->start = $this->stop = $this->getLTriad();
		} else {
			for($i = 0; $i < $this->record; ++$i){
				if($this->start == null){
					$this->start = $this->getLTriad();
				} else {
					$this->offset += 1;

					if($i !== $this->record){
						$this->getLTriad();
					}
				}

				$this->stop = $this->getLTriad();
			}
		}

		var_dump(((static::$id) == 0xc0 ? "ack " : "nack ") ." => start [". $this->start ."] stop [". $this->stop ."]");
	}
}