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

use wertex\session\Session;
use wertex\binary\Binary;

class BatchPacket extends Packet
{
	public static $id = 0xfe;

	/**
	 * @return Zlib-decoded string buffer
	*/
	public function decode(): string{
		$payload = "";

		try {
			$payload = zlib_decode(substr($this->buffer, 1), 1024 * 1024 * 2);
		} catch(\ErrorException $e){}

		return $payload;
	}

	/**
	 * @return Decoded packets
	*/
	public  function getPackets(): array{
		$packets = [];

		while(isset($this->buffer{$this->offset})){
			$packets[] = $this->get(Binary::readUnsignedVarInt($this->buffer, $this->offset));
		}

		return $packets;
	}

	public function addPacket(string $buffer): void{
		$this->buffer .= Binary::writeUnsignedVarInt(strlen($buffer)) . $buffer;
	}

	public function encode(): void{
		$this->buffer = chr(0xfe) . zlib_encode($this->buffer, ZLIB_ENCODING_RAW, 6);
	}

	public function handle(Session $session): void{
		$this->buffer = $this->decode();
		if($this->buffer == null) return;
		
		$this->offset = 0;

		foreach($this->getPackets() as $pk){
			//var_dump(bin2hex($pk));
			//$session->handlePacket($pk);
		}
	}
}