<?php


namespace PixalsCore\PixalsCommands;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\ModelsManager;
use PixalsCore\PixalsServer;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ReloadCommand implements CommandExecutor {

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        
        if($sender instanceof Player) {
            if($command->getName() == "meow") {
                $model_manager = new ModelsManager;
                $model_manager->removeModel($sender);
            }
        }

        return true;
    }

}