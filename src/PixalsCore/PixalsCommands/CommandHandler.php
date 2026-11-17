<?php


namespace PixalsCore\PixalsCommands;

use pocketmine\command\CommandSender;

abstract class CommandHandler {

    abstract public function commandHandle(CommandSender $player, ?array $args) : void;

}