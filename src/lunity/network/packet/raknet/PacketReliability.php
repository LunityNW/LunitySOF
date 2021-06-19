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

interface PacketReliability
{
	const UNRELIABLE = 0;
	const UNRELIABLE_SEQUENCED = 1;

	const RELIABLE = 2;
	const RELIABLE_ORDERED = 3;
	const RELIABLE_SEQUENCED = 4;

	const UNRELIABLE_WITH_ACK_RECEIPT = 5;

	const RELIABLE_WITH_ACK_RECEIPT = 6;
	const RELIABLE_ORDERED_WITH_ACK_RECEIPT = 7;
}