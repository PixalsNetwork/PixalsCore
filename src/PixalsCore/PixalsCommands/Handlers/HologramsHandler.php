<?php


namespace PixalsCore\PixalsCommands\Handlers;

use PixalsCore\CoreHandlers\FloatingTextHandlers\FloatingText;
use PixalsCore\CoreHandlers\FloatingTextHandlers\FloatingTextEntity;
use PixalsCore\Everything;
use PixalsCore\Pixals;
use PixalsCore\PixalsCommands\CommandHandler;
use PixalsCore\PixalsManagers\CoreManagers\LeaderboardsManager;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;
use WeakMap;

class HologramsHandler extends CommandHandler {


    public function commandHandle(CommandSender $player, ?array $args): void
    {
        $everything = new Everything;
        $leaderboards_manager = new LeaderboardsManager;
        if((!isset($args[0]) || !isset($args[1]) || !isset($args[2]))) {  $player->sendMessage("§7» §cSorry, An Error Occurred, Please Refer to the Documentation.");  return; }
        if($args[0] == "activate") { if($everything->getHologramsData()->get("holograms_active")) { $everything->getHologramsData()->set("holograms_active", false); } else { $everything->getHologramsData()->set("holograms_active", true); } $everything->getHologramsData()->save(); $player->sendMessage("§7» §aHolograms State Changed Successfully."); }
        if(!$everything->getHologramsData()->get("holograms_active") && $args[0] !== "activate") { $player->sendMessage("§7» §cPlease Activate the hologram system first before using."); return; }
        if(!$player instanceof Player) { return; }
        if($args[0] == "create") {
            switch($args[1]) {
                case "ldb":
                    $postion = $player->getPosition()->asVector3();
                    $hol = $everything->getHologramsData()->get("leaderboards");
                    if(isset($hol[$args[2] . "_ld"])) { 
                        $leaderboards_manager->generatePLeaderboard($postion, $args[2], "edit"); 
                    } else {
                        $leaderboards_manager->generatePLeaderboard($postion, $args[2], null); 
                    }
                    $hol[$args[2] . "_ld"] = ["position" => ["x_p" => $postion->getFloorX(), "y_p" => $postion->getFloorY(), "z_p" => $postion->getFloorZ(), "type" => $args[2]]];
                    $data = $hol;
                    $everything->getHologramsData()->set("leaderboards", $data);
                    $everything->getHologramsData()->save();
                break;

                case "holo":
                    if(!isset($args[3])) { $player->sendMessage("§7» §cSorry, An Error Occurred, Please Refer to the Documentation."); return; }
                    $this->spawnHologram($player->getPosition()->asVector3(), $args[4], $args[3], $args[2], null, $player->getWorld());
                    $player->sendMessage("§7» §aSuccessfully, Summoned the Hologram, Probably, Can you see around you?");

                break;
            }
        }

        if($args[0] == "delete") {
            if($args[1] == "holo") {
                $this->spawnHologram($player->getPosition()->asVector3(), null, null, $args[2], "delete", $player->getWorld());
                $player->sendMessage("§7» §aSuccessfully, Deleted the Hologram, Probably?");
            }
        }
    }

    public function spawnHologram(Vector3 $pos, ?String $lines, ?String $title, String $name, ?String $type, World $world) : void {
        $position = $pos;
        $everything = new Everything;
        $hol_data = $everything->getHologramsData();

        $holograms = $hol_data->get("holograms_available");
        if($type == "delete") {
            $entity = $everything->texts[$name];
            if($entity instanceof FloatingTextEntity) {
                $entity->flagForDespawn();
            }
            unset($everything->texts[$name]);
            $hol_data->remove($holograms[$name]);
            $hol_data->save();
        }
        if(isset($holograms[$name])) {
            return;
        } else {
            $holograms[$name] = ["position" => [
                "x_p" => $position->getFloorX(),
                "y_p" => $position->getFloorY(),
                "z_p" => $position->getFloorZ()
            ],
                "data" => $lines,
                "title" => $title,
                "world" => $world->getFolderName()
            ];
            $data = $holograms;
            $hol_data->set("holograms_available", $data);
            $hol_data->save();

            $text = $hol_data->get("holograms_available")[$name]["data"];
            $ln = stripcslashes($text);

            $floating_text = new FloatingTextEntity($world, 2, new FloatingText($world->getFolderName(), $position->getFloorX(), $position->getFloorY(), $position->getFloorZ(), $ln));
            $floating_text->setFloatingText(" " . $title . " \n" . $ln);
            $floating_text->spawnToAll();
            $everything->texts[$name] = $floating_text;

        }
    }

}