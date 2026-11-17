<?php

namespace PixalsCore\PixalsPackets\PermissionsPacket;

use pocketmine\player\Player;
use pocketmine\Server;

use alemiz\sga\protocol\StarGatePacket;
use alemiz\sga\protocol\types\PacketHelper;
use alemiz\sga\codec\StarGatePacketHandler;

use PixalsCore\Pixals;
use PixalsCore\PixalsPackets\PixalsPacketHandler;

class PermissionsPacket extends StarGatePacket {

    private $player_name;
    private $permission;
    private $edit;

    public function handle(StarGatePacketHandler $handler) : bool {
        if($handler instanceof PixalsPacketHandler) {
            $handler->handlePermissionsPacket($this);
        }

        return parent::handle($handler);
    }


    public function encodePayload() : void {
        PacketHelper::writeString($this, $this->player_name);
    }

    public function decodePayload() : void {
        $this->player_name = PacketHelper::readString($this);
        $this->permission = PacketHelper::readString($this);
        $this->edit = PacketHelper::readString($this);
    }

    public function getPacketId() : int {
        return 55;
    }

    public function getLogLevel() : int {
        return 2;
    }

    public function getResponseId() : int {
        return 4;
    }

    public function isResponse() : bool {
        return true;
    }

    public function getPlayerName() : String {
        return $this->player_name;
    }

    public function getPermissionName() : String {
        return $this->permission;
    }

    public function getEditName() : String {
        return $this->edit;
    }

    public function setPlayerName(String $name) : void { 
        $this->player_name = $name;
    }


}