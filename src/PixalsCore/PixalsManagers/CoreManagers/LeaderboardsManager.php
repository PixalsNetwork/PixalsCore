<?php


namespace PixalsCore\PixalsManagers\CoreManagers;

use PixalsCore\CoreHandlers\FloatingTextHandlers\FloatingText;
use PixalsCore\CoreHandlers\FloatingTextHandlers\FloatingTextEntity;
use PixalsCore\Everything;
use PixalsCore\Pixals;
use PixalsCore\PixalsCommands\Handlers\HologramsHandler;
use PixalsCore\PixalsTasks\ReloadDataTask;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use SOFe\AwaitGenerator\Await;

class LeaderboardsManager {

    public function generatePLeaderboard(Vector3 $position, String $leaderboard, ?String $type) : void {

        $queries_manager = new QueriesManager;
        $class = $this;
        Await::f2c(function() use ($queries_manager, $position, $leaderboard, $type, $class){
            $count_n = "";
            if($leaderboard == "kills") { $leaderboard_gen = yield from Await::promise(fn($accept, $fail) => $queries_manager->getKillsLeaderboard($accept, $fail)); $count_n = "total_kills"; }
            if($leaderboard == "deaths") { $leaderboard_gen = yield from Await::promise(fn($accept, $fail) => $queries_manager->getDeathsLeaderboard($accept, $fail)); $count_n = "total_deaths"; }
            if($leaderboard == "elo") { $leaderboard_gen = yield from Await::promise(fn($accept) => $queries_manager->getPointsLeaderboard($accept)); $count_n = "elo"; }
        
            $kst = [];
            foreach($leaderboard_gen as ["player_name" => $name, $count_n => $count]) {
               $kst[$name] = $count;
            }
            $rank = 1;
            $lines = [];
           foreach ($kst as $name => $count) {
            switch ($rank) {
                case 1: $color = "§6"; break;
                case 2: $color = "§6"; break; 
                case 3: $color = "§6"; break; 
                default: $color = "§3";
            }
            $lines[] =
                $color . "#" . $rank .
                " §f" . $name .
                " §7- §e" . $count . "";
                $rank++;
            }
            $line = implode("\n", $lines);
            switch($leaderboard) {
                case "kills": $l_name = "§6Kills"; break;
                case "deaths": $l_name = "§cDeaths"; break;
                case "elo": $l_name = "§3Elo"; break;
            }

            $class->spawnHologram($type, $leaderboard, $position, $line, $l_name);

        });
    }

    public function autoLoad() : void {
        $everything = new Everything;
        $hol_data = $everything->getHologramsData();
        $hol_pos = $hol_data->get("leaderboards");
        $hol_pos1 = $hol_data->get("holograms_available");

        $holo_handler = new HologramsHandler;
        if($hol_pos == true && $hol_data->get("holograms_active") == true) {
            foreach($hol_pos as  ["position" => ["x_p" => $x, "y_p" => $y, "z_p" => $z, "type" => $type]]) {
                $this->generatePLeaderboard(new Vector3($x, $y, $z), $type, null);
            }
            Pixals::getPluginInstance()->getLogger()->info("§aLoaded Holograms - Leaderboards Correctly.");
        } 
        
        if($hol_pos == true && $hol_data->get("holograms_active") == true) {
            foreach($hol_pos1 as  $name => ["position" => ["x_p" => $x, "y_p" => $y, "z_p" => $z ], "data" => $data, "title" => $title, "world" => $world_name]) {
                $this->spawnTextHolograms(new Vector3($x, $y, $z), $title, $name, $data, Server::getInstance()->getWorldManager()->getWorldByName($world_name));
            }
            Pixals::getPluginInstance()->getLogger()->info("§aLoaded Holograms - Other Holograms Correctly.");
        } 

        
    }

    public function autoReLoad() : void {
        $everything = new Everything;
        $hol_data = $everything->getHologramsData();
        $hol_pos = $hol_data->get("leaderboards");
        Pixals::getPluginInstance()->getScheduler()->scheduleRepeatingTask(new ReloadDataTask(function() use ($hol_pos, $hol_data){
            if($hol_pos == true && $hol_data->get("holograms_active") == true) {
                foreach($hol_pos as  ["position" => ["x_p" => $x, "y_p" => $y, "z_p" => $z, "type" => $type]]) {
                    $this->generatePLeaderboard(new Vector3($x, $y, $z), $type, "update");
                }
                Pixals::getPluginInstance()->getLogger()->info("§aRe-Loaded Holograms - Leaderboards Correctly.");
            } 
        }, 10), 20);
    }

    private function spawnTextHolograms(Vector3 $position, String $title, String $name, String $data, World $world) : void {
        $everything = new Everything;
        $ln = stripcslashes($data);

        $floating_text = new FloatingTextEntity($world, 2, new FloatingText($world->getFolderName(), $position->getFloorX(), $position->getFloorY(), $position->getFloorZ(), $ln));
        $floating_text->setFloatingText(" " . $title . " \n" . $ln);
        $floating_text->spawnToAll();
        $everything->texts[$name] = $floating_text;

    }

    private function spawnHologram(?String $type, String $leaderboard, Vector3 $position, String $line, String $l_name) : void {
        global $id;
        if($type == "edit") {
            $e = $id[$leaderboard];
            if($e instanceof FloatingTextEntity){
                $e->flagForDespawn();
                $leaderboard_holo = new FloatingTextEntity(Server::getInstance()->getWorldManager()->getDefaultWorld(), 1, 
                new FloatingText(Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName(), 
                    $position->getX(), 
                    $position->getY(),
                    $position->getZ(),
                    "§l $l_name LEADERBOARD\n§8────────" . " \n" . $line
                ));
                $id[$leaderboard] = $leaderboard_holo;
                $leaderboard_holo->setFloatingText("§l $l_name Leaderboard \n§8────────" . " \n" . $line);
                $leaderboard_holo->spawnToAll();
            }
            return;
        }

        if($type == "update") {
            $e = $id[$leaderboard];
            if($e instanceof FloatingTextEntity) {
                $e->editFloatingText("§l $l_name Leaderboard \n§8────────" . " \n" . $line);
            }
            return;
        }

        $leaderboard_holo = new FloatingTextEntity(Server::getInstance()->getWorldManager()->getDefaultWorld(), 1, 
            new FloatingText(Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName(), 
                    $position->getX(), 
                    $position->getY(),
                    $position->getZ(),
                    "§l $l_name Leaderboard\n§8────────" . " \n" . $line
            ));
        $id[$leaderboard] = $leaderboard_holo;
        $leaderboard_holo->setFloatingText("§l $l_name Leaderboard \n§8────────" . " \n" . $line);
        $leaderboard_holo->spawnToAll();
    }

}