<?php

namespace PixalsCore\PixalsCommands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\player\GameMode;
use pocketmine\entity\Skin;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\entity\Location;

use PixalsCore\Pixals;
use PixalsCore\PixalsGUIs;

class SpectateCommands implements CommandExecutor {



    public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {

        $player = $sender;
        $prefix = "§7| §3Pix§bals §l§7» §r";

        if($player instanceof Player) {
            if($cmd->getName() == "spectate") {
                if(!isset($args[0])) {
                    $player->sendMessage($prefix . "§cSorry, Usage §7: §3/spectate {player_name}");
                } else {
                    $pixals = Pixals::getPluginInstance();
                    $target = $pixals->getServer()->getPlayerExact($args[0]);
                    if($target == null) {
                        $player->sendMessage($prefix . "§cSorry, This Player Has Gone Offline");
                    } else {
                        $x = $target->getPosition()->getX();
                        $y = $target->getPosition()->getY();
                        $z = $target->getPosition()->getZ();

                        $player->setGamemode(GameMode::SPECTATOR());
                        $player->teleport(new Vector3($x, $y, $z));
                        $player->sendMessage($prefix . "§aYou're Spectating The Player {$target->getName()}, To Un Spectate, Type§7: §3/unspectate");
                    }
                }
            }

            if($cmd->getName() == "unspectate") {
                $pixals = Pixals::getPluginInstance();
                $player->setGamemode(GameMode::SURVIVAL());
                $player->teleport($pixals->getServer()->getWorldManager()->getWorldByName($player->getWorld()->getFolderName())->getSafeSpawn());
                $player->sendMessage($prefix . "§aSuccessfully Turned off Spectate");
            }
        }
       

        return true;
    }


}