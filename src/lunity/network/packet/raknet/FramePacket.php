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

class FramePacket
{
	private static function isReliable(int $reliability): bool{
		return (
			$reliability === PacketReliability::RELIABLE ||
			$reliability === PacketReliability::RELIABLE_ORDERED ||
			$reliability === PacketReliability::RELIABLE_SEQUENCED ||
			$reliability === PacketReliability::RELIABLE_WITH_ACK_RECEIPT ||
			$reliability === PacketReliability::RELIABLE_ORDERED_WITH_ACK_RECEIPT
		);
	}

	private static function isSequenced(int $reliability): bool{
		return (
			$reliability === PacketReliability::UNRELIABLE_SEQUENCED ||
			$reliability === PacketReliability::RELIABLE_SEQUENCED
		);
	}

	private static function isOrdered(int $reliability): bool{
		return (
			$reliability === PacketReliability::RELIABLE_ORDERED ||
			$reliability === PacketReliability::RELIABLE_ORDERED_WITH_ACK_RECEIPT
		);
	}

	public static function fromBinary(string &$binary, int &$offset, array &$options = []): void{
		/** @var int */
		$flags = ord($binary{0});

		/** @var int */
		$reliability = ($flags & 0b11100000) >> 5;
		/** @var int */
		$len = (int) ceil(Binary::readShort(substr($binary, 1, 3)) / 8);
		
		$offset = 3;

		if($reliability > PacketReliability::UNRELIABLE){
			if(self::isReliable($reliability)){
				$options["MessageIndex"] = Binary::readLTriad(substr($binary, $offset, 3));
				$offset += 3;
			}

			if(self::isSequenced($reliability)){
				$options["SequenceIndex"] = Binary::readLTriad(substr($binary, $offset, 3));
				$offset += 3;
			}

			if(self::isSequenced($reliability) || self::isOrdered($reliability)){
				$options["OrderIndex"] = Binary::readLTriad(substr($binary, $offset, 3));
				$offset += 3;
				$options["OrderChannel"] = ord($binary{$offset++});
			}
		}

		if(($flags & 0b00010000) > 0){
			$options["SplitCount"] = Binary::readInt(substr($binary, $offset, 4));
			$offset += 4;
			$options["SplitID"] = Binary::readShort(substr($binary, $offset, 2));
			$offset += 2;
			$options["SplitIndex"] = Binary::readInt(substr($binary, $offset, 4));
			$offset += 4;
		}

		$binary = substr($binary, $offset, $len);
		$offset += $len;
	}

	public static function toBinary(string &$buffer, int $reliability = PacketReliability::RELIABLE, array $splitSettings = [], ?array $options = []): void{
		$packet = chr(($reliability << 5) | ($splitSettings != [] ? 0b00010000 : 0)) . Binary::writeShort(strlen($buffer) << 3);

		if($reliability > PacketReliability::UNRELIABLE){
			if(self::isReliable($reliability)){
				$packet .= Binary::writeLTriad($options["MessageIndex"]);
			}

			if(self::isSequenced($reliability)){
				$packet .= Binary::writeLTriad($options["SequenceIndex"]);
			}

			if(self::isSequenced($reliability) || self::isOrdered($reliability)){
				$packet .= Binary::writeLTriad($options["OrderIndex"]) . chr($options["OrderChannel"]);
			}
		}

		if($splitSettings != []){
			$packet .= Binary::writeInt($splitSettings["SplitCount"]) . Binary::writeShort($splitSettings["SplitID"]) . Binary::writeInt($splitSettings["SplitIndex"]);
		}

		$buffer = $packet . $buffer;
	}
}