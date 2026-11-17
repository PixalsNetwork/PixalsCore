<?php


namespace PixalsCore\PixalsPlayer\PSessionSystem;

use PixalsCore\PixalsCoreStartup;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsManagers\CoreManagers\ScoreboardManager;
use PixalsCore\PixalsManagers\CoreManagers\SkinManager;
use PixalsCore\PixalsQueries\Queries;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class Session {

    private Player $player;
    private $player_skin_format = "";
    private $online_time = 0;

    private $data = [];

    public function __construct(Player $player, String $rank, int $ucoins, int $level, array $rank_data, String $tag, array $clan_data, array $player_data, array $level_data, int $online_time)
    {
        $skin_manager = new SkinManager;
        $this->player = $player;
        $this->player_skin_format = $skin_manager->determinePlayerSkinType($player);
        $this->data[$player->getName()]["PlayerSkinFormat"] = $this->player_skin_format;
        $this->data[$player->getName()]["Rank"] = $rank;
        $this->data[$player->getName()]["UCoins"] = $ucoins;
        $this->data[$player->getName()]["Level"] = $level;
        $this->data[$player->getName()]["RankData"] = $rank_data;
        $this->data[$player->getName()]["Tag"] = $tag;
        $this->data[$player->getName()]["ClanData"] = $clan_data;
        $this->data[$player->getName()]["PlayerData"] = $player_data;
        $this->data[$player->getName()]["LevelData"] = $level_data;
        $this->data[$player->getName()]["OnlineTime"] = $online_time;
    }

    public function reloadTag(String $tag_name) : void {
        Await::f2c(function() use ($tag_name){
            
            
            if($tag_name != "None") {
                $tag_prefix = yield from Await::promise(fn($accept) => (new QueriesManager)->getTagPrefix($tag_name, $accept));
            } else { $tag_prefix = "None"; }

            $this->data[$this->player->getName()]["Tag"] = $tag_prefix;

        });
    }

    public function reloadPlayTime() : void {
        $session_class = $this;
        $scm = new ScoreboardManager;
        $queries_manager = new QueriesManager;
        if(!PixalsCoreStartup::MINIGAMES_MODE) {
            Await::f2c(function () use ($session_class, $queries_manager, $scm) {
                $player_data = yield from Await::promise(fn($accept) => $queries_manager->getPlayerData($this->player, $accept));
                $playtime = $player_data[0][$queries_manager::ONLINE_TIME];

                $session_class->updateOnlineTime($playtime);
                $scm->removeScoreboard($session_class->player);
                $scm->removeScoreboard($session_class->player);
                $scm->PixalsHubScoreboard($session_class->player);
            });
        }
    }

    public function getSkinFormat() : String {
        return $this->data[$this->player->getName()]["PlayerSkinFormat"];
    }

    public function getRank() : String {
        return $this->data[$this->player->getName()]["Rank"];
    }

    public function getUCoins() : int {
        return $this->data[$this->player->getName()]["UCoins"];
    }

    public function getLevel() : int {
        return $this->data[$this->player->getName()]["Level"];
    }
    
    public function getRankData() : array {
        return $this->data[$this->player->getName()]["RankData"];
    }

    public function getTag() : String|array {
        if(is_array($this->data[$this->player->getName()]["Tag"])){
            $tag_prefix = $this->data[$this->player->getName()]["Tag"][0]["tag_prefix"];
        } else {
            $tag_prefix = $this->data[$this->player->getName()]["Tag"];
        }
        
        return $tag_prefix;
    }

    public function getClanData() : array {
        return $this->data[$this->player->getName()]["ClanData"];
    }

    public function getPlayerData() : array {
        return $this->data[$this->player->getName()]["PlayerData"];
    }

    public function getPlayerLevelData() : array {
        return $this->data[$this->player->getName()]["LevelData"];
    }

    public function updateOnlineTime(int $new_time) : void {
        $this->data[$this->player->getName()]["OnlineTime"] = $new_time;  

    }

    public function getOnlineTime() : int {
        return $this->data[$this->player->getName()]["OnlineTime"];
    }


}