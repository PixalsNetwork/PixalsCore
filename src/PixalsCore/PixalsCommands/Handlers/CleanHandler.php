<?php


namespace PixalsCore\PixalsCommands\Handlers;

use PixalsCore\PixalsCommands\CommandHandler;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CleanHandler extends CommandHandler {

    public function commandHandle(CommandSender $player, ?array $args): void
    {
        $player->sendMessage("§7» Cleared The Chat, §oMaybe?");
    }

}