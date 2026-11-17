<?php

namespace PixalsCore\PixalsManagers\TebexIntegrator;

use PixalsCore\PixalsManagers\CoreManagers\PacketSenderManager;

class TebexIntegratorManager {

    public function setRank(String $player_name, String $rank_name) : void {
        $packet_manager = new PacketSenderManager;
        $packet_manager->sendDispatchCMDPacket("rank set $player_name $rank_name", "console");
    }


    public function giveTag(String $player_name, String $tag_name) : void {
        $packet_manager = new PacketSenderManager;
        $packet_manager->sendDispatchCMDPacket("tags give $player_name $tag_name", "console");
    }

    
    public function addCapePermission(String $player_name, String $cape_permission) : void {
        $packet_manager = new PacketSenderManager;
        $packet_manager->sendDispatchCMDPacket("ppermissions add $player_name $cape_permission", "console");
    }


}