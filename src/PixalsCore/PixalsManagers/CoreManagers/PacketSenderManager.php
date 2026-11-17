<?php

namespace PixalsCore\PixalsManagers\CoreManagers;

use PixalsCore\Pixals;
use PixalsCore\PixalsPackets\DispatchCommandPackets\DispatchCommandPacket;
use PixalsCore\PixalsPackets\OpenUIPackets\OpenUIPacket;
use PixalsCore\PixalsPackets\ScoreboardPackets\ScoreboardPacket;

class PacketSenderManager {


    public function sendDispatchCMDPacket(String $command_name, String $player_name) {
        $client = Pixals::getPluginInstance()->stargate->getDefaultClient();
        
        $packet = new DispatchCommandPacket();

        $packet->setCommand($command_name);
        $packet->setName($player_name);

        $client->getSession()->sendPacket($packet);
    }

    public function sendOpenFormPacket(String $player_name, String $form_name) {
        $client = Pixals::getPluginInstance()->stargate->getDefaultClient();

        $packet = new OpenUIPacket();
        $packet->setPlayerName($player_name);
        $packet->setUIName($form_name);

        $client->getSession()->sendPacket($packet);
    }

    public function sendScoreboardPacket(String $player_name) {
        $client = Pixals::getPluginInstance()->stargate->getDefaultClient();

        $packet = new ScoreboardPacket();
        $packet->setPlayerName($player_name);

        $client->getSession()->sendPacket($packet);
    }


}