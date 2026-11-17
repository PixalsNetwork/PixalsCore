<?php

namespace PixalsCore\PixalsPackets\DispatchCommandPackets;

use PixalsCore\Pixals;

use alemiz\sga\protocol\StarGatePacket;
use alemiz\sga\protocol\types\PacketHelper;
use alemiz\sga\codec\StarGatePacketHandler;

use PixalsCore\PixalsPackets\PixalsPacketHandler;

class DispatchCommandPacket extends StarGatePacket {

    private $command;
    private $player_name;


    public function handle(StarGatePacketHandler $handler) : bool {
        if($handler instanceof PixalsPacketHandler) {
            $handler->handleDispatchCMDPacket($this);
        }

        return parent::handle($handler);
    }

    public function encodePayload() : void {
        PacketHelper::writeString($this, $this->command);
        PacketHelper::writeString($this, $this->player_name);    
        // echo $this->command;
    }

    public function decodePayload() : void {
        $this->command = PacketHelper::readString($this);
        $this->player_name = PacketHelper::readString($this);
    }

    public function getLogLevel() : int {
        return 2;
    }

    public function getResponseId() : int {
        return 5;
    }

    public function isResponse() : bool {
        return true;
    }

    public function getPacketId() : int {
        return 50;
    }

    public function setCommand(String $s) {
        $this->command = $s;
    }

    public function getCommand() : String {
        return $this->command;
    }

    public function setName(String $s) {
        $this->player_name = $s;
    }

    public function getPlayerName() : String {
        return $this->player_name;
    }



}