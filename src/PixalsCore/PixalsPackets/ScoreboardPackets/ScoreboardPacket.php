<?php

namespace PixalsCore\PixalsPackets\ScoreboardPackets;

use pocketmine\Server;
use pocketmine\player\Player;

use alemiz\sga\protocol\StarGatePacket;
use alemiz\sga\protocol\types\PacketHelper;
use alemiz\sga\codec\StarGatePacketHandler;

use PixalsCore\PixalsPackets\PixalsPacketHandler;

class ScoreboardPacket extends StarGatePacket {

    private $player_name;
    private $rank_prefix;
    private $chat_level;
    private $ultimate_coins;
      
    public function handle(StarGatePacketHandler $handler) : bool {
        if($handler instanceof PixalsPacketHandler) {
            $handler->handleScoreboardPacket($this);
        }

        return parent::handle($handler);
    }

    public function encodePayload() : void {
        PacketHelper::writeString($this, $this->player_name);    
    }

    public function decodePayload() : void {
        $this->player_name = PacketHelper::readString($this);
    }


    public function getPacketId() : int {
        return 30;
    }

    public function getPlayerName() : String {
        return $this->player_name;
    }

    public function getRankPrefix() : String {
        return $this->rank_prefix;
    }

    public function getChatLevel() : int {
        return $this->chat_level;
    }

    public function getUltimateCoins() : int {
        return $this->ultimate_coins;
    }

    public function setPlayerName(String $player_name) {
        $this->player_name = $player_name;
    } 

}