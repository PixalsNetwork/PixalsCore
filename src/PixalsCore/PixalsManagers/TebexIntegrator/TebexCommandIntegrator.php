<?php

namespace PixalsCore\PixalsManagers\TebexIntegrator;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

class TebexCommandIntegrator implements CommandExecutor {

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {
        $tebex_integrator = new TebexIntegratorManager;

        if($cmd->getName() == "rank") {
            if($args[0] == "set") {
                $player_name = $args[1];
                $rank_name = $args[2];

                $tebex_integrator->setRank($player_name, $rank_name);
            }
        }

        if($cmd->getName() == "tags") {
            if($args[0] == "give") {
                $player_name = $args[1];
                $tag_name = $args[2];

                $tebex_integrator->giveTag($player_name, $tag_name);
            }
        }

        if($cmd->getName() == "ppermissions") {
            if($args[0] == "add") {
                $player_name = $args[1];
                $permission = $args[2];

                $tebex_integrator->addCapePermission($player_name, $permission);
            }
        }

        return true;
    }


}