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

namespace lunity\network\raklib;

class Binary
{
    public static function readInt(string $str): int{
        return unpack("N", $str)[1] << 32 >> 32;
    }

    public static function writeInt(int $value): string{
        return pack("N", $value);
    }

    public static function readFloat(string $str): float{
        return unpack("f", $str)[1];
    }

    public static function writeFloat(float $value): string{
        return pack("f", $value);
    }

    public static function readDouble(string $str): float{
        return unpack("d", $str)[1];
    }

    public static function readLong(string $str): int{
        return unpack("J", $str)[1];
    }

    public static function writeLong(int $value): string{
        return pack("J", $value);
    }

    public static function readShort(string $str): int{
        return unpack("n", $str)[1];
    }

    public static function writeShort(int $value): string{
        return pack("n", $value);
    }

    public static function readLShort(string $str): int{
        return unpack("v", $str)[1];
    }

    /**
     * @return mixed
     */
    public static function writeLShort(int $value){
        return pack("v", $value);
    }

    public static function readSignedShort(string $str): int{
        return unpack("n", $str)[1] << 48 >> 48;
    }

    public static function readLInt(string $str): int{
        return unpack("V", $str)[1] << 32 >> 32;
    }

    public static function writeLInt(int $value): string{
        return pack("V", $value);
    }

    /**
     * @return mixed
     */
    public static function readLTriad(string $str){
        return unpack("V", $str ."\x00")[1];
    }

    /**
     * @param mixed
     */
    public static function writeLTriad($value): string{
        return substr(pack("V", $value), 0, -1);
    }

    public static function readVarInt(string $str, int &$offset = 0): int{
        $raw = self::readUnsignedVarInt($str, $offset);
        $temp = ((($raw << 63) >> 63) ^ $raw) >> 1;

        return $temp ^ ($raw & (1 << 63));
    }

    public static function readUnsignedVarInt(string $str, int &$offset = 0): int{
        $value = 0;

        for($x = 0; $x <= 28; $x += 7){
            $bytes = ord($str[$offset++]);
            $value |= (($bytes & 0x7f) << $x);

            if(($bytes & 0x80) === 0){
                return $value;
            }
        }
    }

    public static function writeVarInt(int $value): string{
        $value = ($value << 32 >> 32);
        return self::writeUnsignedVarInt(($value << 1) ^ ($value >> 31));
    }

    public static function writeUnsignedVarInt(int $value): string{
        $buffer = "";
        $value &= 0xffffffff;

        for($x = 0; $x < 5; ++$x){
            if(($value >> 7) !== 0){
                $buffer .= chr($value | 0x80);
            } else {
                $buffer .= chr($value & 0x7f);
                return $buffer;
            }

            $value = (($value >> 7) & (PHP_INT_MAX >> 6));
        }
    }

    public static function readVarLong(string $str, int &$offset = 0): int{
        $raw = self::readUnsignedVarLong($str, $offset);
        $temp = ((($raw << 63) >> 63) ^ $raw) >> 1;

        return $temp ^ ($raw & (1 << 63));
    }

    public static function readUnsignedVarLong(string $str, int &$offset = 0): int{
        $value = 0;

        for($x = 0; $x <= 63; $x += 7){
            $bytes = ord($str[$offset++]);
            $value |= (($bytes & 0x7f) << $x);

            if(($bytes & 0x80) === 0){
                return $value;
            }
        }
    }

    public static function writeVarLong(int $value): string{
        return self::writeUnsignedVarLong(($value << 1) ^ ($value >> 63));
    }

    public static function writeUnsignedVarLong(int $value): string{
        $buffer = "";

        for($x = 0; $x < 10; ++$x){
            if(($value >> 7) !== 0){
                $buffer .= chr($value | 0x80);
            } else {
                $buffer .= chr($value & 0x7f);
                return $buffer;
            }

            $value = (($value >> 7) & (PHP_INT_MAX >> 6));
        }
    }
}