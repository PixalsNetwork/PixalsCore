<?php


namespace PixalsCore\CoreHandlers;

use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PSessionSystem\SessionManager;
use pocketmine\player\Player;

class LevelHandler {

    private $levels = [];

    public function __construct()
    {
        $config = Pixals::getPluginInstance()->getConfig();
        $this->levels = $config->get("levels");
    }

    public function calculateXPForLevel(int $level) : void {
        
    }

    public function getPlayerXP(Player $player) : int {
        $session_manager = new SessionManager;
        $session = $session_manager->getSession($player);
        return $session->getPlayerLevelData()["player_experience_points"];
    }

    public function setPlayerLevelTag(Player $player) : void {
        
    }


}