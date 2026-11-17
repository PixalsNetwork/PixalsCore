<?php


namespace PixalsCore\PixalsCommands\Handlers;

use PixalsCore\PixalsCommands\CommandHandler;
use PixalsCore\PixalsManagers\CoreManagers\PacketSenderManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PotatoHandler extends CommandHandler {

    public function commandHandle(CommandSender $player, ?array $args): void
    {
        if($player instanceof Player) {
            (new PacketSenderManager)->sendDispatchCMDPacket("tags give " . $player->getName() . " Potato", "console");
        }
    }

}