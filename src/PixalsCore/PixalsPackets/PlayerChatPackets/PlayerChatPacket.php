<?php

namespace PixalsCore\PixalsPackets\PlayerChatPackets;

use pocketmine\Server;
use pocketmine\player\Player;

use alemiz\sga\protocol\StarGatePacket;
use alemiz\sga\protocol\types\PacketHelper;
use alemiz\sga\codec\StarGatePacketHandler;

use PixalsCore\PixalsPackets\PixalsPacketHandler;

class PlayerChatPacket extends StarGatePacket {

    private $message;
    private $player_name;

    public function handle(StarGatePacketHandler $handler) : bool {
        if($handler instanceof PixalsPacketHandler) {
            $handler->handlePlayerChatPacket($this);
        }

        return parent::handle($handler);
    }

    public function encodePayload() : void {  
    }

    public function decodePayload() : void {
        $this->message = PacketHelper::readString($this);
        $this->player_name = PacketHelper::readString($this);
    }


    public function getPacketId() : int {
        return 103;
    }

    public function getPlayerName() : String {
        return $this->player_name;
    }

    public function getMessage() : String {
        return $this->message;
    }



}