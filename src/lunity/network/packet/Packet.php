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

namespace lunity\network\packet;

use lunity\network\raklib\Binary;
use lunity\network\raklib\RakLib;


//use wertex\utils\data\EntityData;

//use wertex\item\Item;
//use wertex\item\ItemManager;

abstract class Packet
{
    public static $id;
    /** @var int */
    public $offset = 0;
    /** @var string */
    public $buffer;

    public function encode() {
        $this->buffer = chr(self::$id);
    }

    public function decode() {
        $this->offset = 1;
    }

    public function putMagic() {
        $this->buffer .= RakLib::MAGIC;
    }

    public function put($n) {
        $this->buffer .= chr($n);
    }

    public function putByte($byte) {
        $this->buffer .= chr($byte);
    }


    /**
     * @param $len
     * @return string
     */
    public function get($len): string{
        return $len === true ? substr($this->buffer, $this->offset) : substr($this->buffer, ($this->offset += $len) - $len, $len);
    }

    public function getByte(): int{
        return ord($this->buffer{$this->offset++});
    }

    public function getInt(bool $l = false): int{
        return $l ? Binary::readLInt($this->get(4)) : Binary::readInt($this->get(4));
    }

    protected function putInt(int $n): void{
        $this->buffer .= Binary::writeInt($n);
    }

    protected function getLong(): int{
        return Binary::readLong($this->get(8));
    }

    protected function putLong(int $n): void{
        $this->buffer .= Binary::writeLong($n);
    }

    protected function getShort(bool $signed = true): int{
        return $signed ? Binary::readSignedShort($this->get(2)) : Binary::readShort($this->get(2));
    }

    protected function putShort(int $n): void{
        $this->buffer .= Binary::writeShort($n);
    }

    protected function getLShort(): int{
        return Binary::readLShort($this->get(2));
    }

    protected function putLShort(int $n): void{
        $this->buffer .= Binary::writeLShort($n);
    }

    /**
     * @return mixed
     */
    protected function getLTriad(){
        return Binary::readLTriad($this->get(3));
    }

    /**
     * @param mixed
     */
    protected function putLTriad($n){
        $this->buffer .= Binary::writeLTriad($n);
    }

    protected function getAddress(string &$address, int &$port): void{
        $version = $this->getByte();

        if($version == 4){
            $address = ((~$this->getByte()) & 0xff) .".". ((~$this->getByte()) & 0xff) .".". ((~$this->getByte()) & 0xff) .".". ((~$this->getByte()) & 0xff);
            $port = $this->getShort(false);
        }
    }

    protected function putAddress(string $address, int $port, int $version = 4): void{
        $this->buffer .= chr($version);

        if($version == 4){
            foreach(explode(".", $address) as $value){
                $this->buffer .= chr((~(int) $value) & 0xff);
            }

            $this->putShort($port);
        }
    }

    /**protected function putItem(Item $item): void{
        if($item->id == 0){
            $this->buffer .= Binary::writeVarInt(0);
            return;
        }

        $netItem = ItemManager::$instance->toNetworkId($item->id, $item->meta);
        $this->buffer .= Binary::writeVarInt($netItem[0]) . Binary::writeVarInt((($netItem[1] & 0x7fff) << 8 | $item->count));

        //durable soon...
        $this->buffer .= Binary::writeLShort(0); //nbt is soon
        $this->buffer .= Binary::writeVarInt(0) . Binary::writeVarInt(0); //CanPlaceOn & CanDestroyOn
    }*/

    /**protected function putMetadata(array $metadata): void{
        $this->buffer .= Binary::writeUnsignedVarInt(count($metadata));

        foreach($metadata as $meta => $data){
            $this->buffer .= Binary::writeUnsignedVarInt($meta);
            $this->buffer .= Binary::writeUnsignedVarInt($data[0]);

            switch($data[0]):
                case EntityData::DATA_TYPE_BYTE:
                    $this->buffer .= chr($data[1]);
                    break;
                case EntityData::DATA_TYPE_SHORT:
                    $this->buffer .= Binary::writeLShort($data[1]);
                    break;
                case EntityData::DATA_TYPE_INT:
                    $this->buffer .= Binary::writeVarInt($data[1]);
                    break;
                case EntityData::DATA_TYPE_FLOAT:
                    $this->buffer .= pack("g", $data[1]);
                    break;
                case EntityData::DATA_TYPE_LONG:
                    $this->buffer .= Binary::writeVarLong($data[1]);
                    break;
                case EntityData::DATA_TYPE_STRING:
                    $this->buffer .= Binary::writeUnsignedVarInt(strlen($data[1])) . $data[1];
                    break;
                case EntityData::DATA_TYPE_POS:
                    $this->buffer .= Binary::writeVarInt($data[1][0]) . Binary::writeVarInt($data[1][1]) . Binary::writeVarInt($data[1][2]);
                    break;
                case EntityData::DATA_TYPE_VECTOR3F:
                    $this->buffer .= pack("g", $data[1][0]) . pack("g", $data[1][1]) . pack("g", $data[1][2]);
                    break;
            endswitch;
        }
    }*/

    protected function putAttributes(array $attributes): void{
        $this->buffer .= Binary::writeUnsignedVarInt(count($attributes));

        foreach($attributes as $name => $options){
            $this->buffer .= pack("g", $options[0]) . pack("g", $options[1]) . pack("g", $options[2]) . pack("g", $options[3]) . Binary::writeUnsignedVarInt(strlen($name)) . $name; //WHY NAME IN LAST POSITION MOJANGGGG
        }
    }
}
