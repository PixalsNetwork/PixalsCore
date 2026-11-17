<?php

namespace PixalsCore\PixalsPackets;

use alemiz\sga\handler\SessionHandler;

use PixalsCore\Pixals;
use PixalsCore\PixalsPackets\DispatchCommandPackets\DispatchCommandPacket;
use PixalsCore\PixalsPackets\PermissionsPacket\PermissionsPacket;
use PixalsCore\PixalsPackets\ScoreboardPackets\ScoreboardPacket;
use PixalsCore\PixalsPackets\PlayerChatPackets\PlayerChatPacket;
use PixalsCore\PixalsEvents;
use PixalsCore\PixalsManagers\CoreManagers\PermissionsManager;
use PixalsCore\PixalsManagers\CoreManagers\ScoreboardManager;
use PixalsCore\PixalsPlayer\PPlayer;

class PixalsPacketHandler extends SessionHandler {

    public function handleDispatchCMDPacket(DispatchCommandPacket $packet) {
        $pixals = Pixals::getPluginInstance();

        $pixals->getServer()->dispatchCommand($pixals->getServer()->getPlayerExact($packet->getPlayerName()), $packet->getCommand());
    }

    public function handlePermissionsPacket(PermissionsPacket $packet) {
        $pixals = Pixals::getPluginInstance();
        $permissions_manager = new PermissionsManager();
        echo $packet->getEditName();
        echo $packet->getPlayerName();
        $permissions_manager->editPermissions($pixals->getServer()->getPlayerExact($packet->getPlayerName()), $packet->getPermissionName(), $packet->getEditName());
    }



    public function handleScoreboardPacket(ScoreboardPacket $packet)  {
        $pixals = Pixals::getPluginInstance();
        $pixals_player = new PPlayer;
        $scoreboard_manager = new ScoreboardManager();
        $player = $pixals->getServer()->getPlayerExact($packet->getPlayerName());
        $scoreboard_manager->PixalsHubScoreboard($player);
		$scoreboard_manager->removeScoreboard($player);
		$scoreboard_manager->PixalsHubScoreboard($player);
        $scoreboard_manager->PixalsHubScoreboard($player);
        $pixals_player->changePlayerNametag($player);
        //$pixals->PixalsScoreboard($player, $packet->getChatLevel(), $packet->getRankPrefix(), $packet->getUltimateCoins());
    }

    public function handlePlayerChatPacket(PlayerChatPacket $packet) {
        $pixals = Pixals::getPluginInstance();
        if($pixals->getServer()->getPlayerExact($packet->getPlayerName()) !== null) {
            $player = $pixals->getServer()->getPlayerExact($packet->getPlayerName());
            $msg = $packet->getMessage();
            $player->chat($msg);
        }

                
    }


}