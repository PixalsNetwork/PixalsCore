<?php

namespace PixalsCore\PixalsPackets\OpenUIPackets;

use PixalsCore\Pixals;

use alemiz\sga\protocol\StarGatePacket;
use alemiz\sga\protocol\types\PacketHelper;
use alemiz\sga\codec\StarGatePacketHandler;

use PixalsCore\PixalsPackets\PixalsPacketHandler;

class OpenUIPacket extends StarGatePacket {

    private $player_name;
    private $ui_name;

    public function encodePayload() : void {
        PacketHelper::writeString($this, $this->player_name);
        PacketHelper::writeString($this, $this->ui_name);
    }

    public function decodePayload() : void {
        $this->player_name = PacketHelper::readString($this);
        $this->ui_name = PacketHelper::readString($this);
    }

    public function getPacketId() : int {
        return 59;
    }

    public function setPlayerName(String $player_name) : void {
        $this->player_name = $player_name;
    }

    public function setUIName(String $ui_name) : void {
        $this->ui_name = $ui_name;
    }

}