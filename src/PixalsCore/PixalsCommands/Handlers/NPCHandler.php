<?php


namespace PixalsCore\PixalsCommands\Handlers;

use Himbeer\LibSkin\SkinGatherer;
use Locale;
use PixalsCore\Everything;
use PixalsCore\Pixals;
use PixalsCore\PixalsCommands\CommandHandler;
use PixalsCore\PixalsNPCs\PixalsNPCsEntities\HumanNPC;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class NPCHandler extends CommandHandler {

    public function commandHandle(CommandSender $player, ?array $args): void
    {   
        $everything = new Everything;
        $npcs_data = $everything->getNPCData();
        if(!isset($args[0]) || !isset($args[1])) { $player->sendMessage("§7» §cSorry, an Error Has Occured, Please refer to the documentation instead."); return; }

        if($args[0] == "delete") { 
            if(array_key_exists(stripcslashes($args[1]), $npcs_data->get("npcs_registered"))) {
                $values = $npcs_data->get("npcs_registered");
                unset($values[$args[1]]);
                $npcs_data->set("npcs_registered", $values);
                $npcs_data->save();
                $player->sendMessage("§7» §aDeleted the NPC");
                return;
            } else {
                $player->sendMessage("§7» §cSorry, an Error Has Occured, Error: This NPC hasn't been registered yet, Please refer to the documentation instead."); 
                return;
            }
        }

        if($args[0] == "spawn") {
            if (!isset($args[2]) || !isset($args[3]) || !isset($args[4])) {
                $player->sendMessage("§7» §cSorry, an Error Has Occured, Please refer to the documentation instead.");
                return;
            }
            if (!$player instanceof Player) {
                $player->sendMessage("§7» §cSorry, This is an IN-Game Command Only.");
                return;
            }
            $name = $args[1];
            $proxyCmd = $args[2];
            $localCmd = $args[3];
            $message = $args[4];
            $scale = $player->getScale();
            if (isset($args[5])) {
                $scale = $args[5];
            }
            if(array_key_exists($args[1], $npcs_data->get("npcs_registered"))){ 
                $player->sendMessage("§7» §cSorry, This NPC Is Already Spawned.");
                return;
            } else {
                $npcs = $npcs_data->get("npcs_registered");
                $human_npc = new HumanNPC(new Location($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getPosition()->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), new Skin(0, $player->getSkin()->getSkinData(), "", $player->getSkin()->getGeometryName(), $player->getSkin()->getGeometryData()));
                $npcs[stripcslashes($name)] = ["data" => ["proxyCMD" => $proxyCmd, "localCMD" => $localCmd, "data" => $message, "id" => $human_npc->getId()], "position" => [
                    "x_p" => $player->getPosition()->getFloorX(), 
                    "y_p" => $player->getPosition()->getFloorY(),
                    "z_p" => $player->getPosition()->getFloorZ()
                ]];

                $npcs_data->set("npcs_registered", $npcs);
                $npcs_data->save();
                
                $human_npc->setScale($scale);
                $human_npc->setNameTagAlwaysVisible(true);
                $human_npc->setNameTag(stripcslashes($name));
                $human_npc->spawnToAll();
                
                $player->sendMessage("§7» §aSpawned NPC Successfully");
            }
        }
    }

}