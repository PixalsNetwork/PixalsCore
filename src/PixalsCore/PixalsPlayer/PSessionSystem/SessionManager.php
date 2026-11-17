<?php


namespace PixalsCore\PixalsPlayer\PSessionSystem;

use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PlayerDataLoader;
use PixalsCore\PixalsTasks\ReloadDataTask;
use pocketmine\player\Player;

class SessionManager {

    private static $sessions = [];

    public function createSession(Player $player, String $rank, int $ucoins, int $level, array $rank_data, String $tag, array $clan_data, array $player_data, array $level_data, int $online_time) : void {
        $pixals = Pixals::getPluginInstance();
        $pixals->getLogger()->info("§7[§aSession System§7] §aCreated Player Session for §b" . $player->getName());
        $session = new Session($player, $rank, $ucoins, $level, $rank_data, $tag, $clan_data, $player_data, $level_data, $online_time);
        self::$sessions[$player->getName()] = $session;
        $pixals->getScheduler()->scheduleRepeatingTask(new ReloadDataTask(function() {
            (new PlayerDataLoader)->updatePlayTime();
            
        }, 60), 20);
    }

    public function getSession(Player $player) : Session {
        return self::$sessions[$player->getName()];
    }

    public function destroySession(Player $player) : void {
        Pixals::getPluginInstance()->getLogger()->info("§7[§aSession System§7] §cDestroyed Player Session for §b" . $player->getName());
        unset(self::$sessions[$player->getName()]);
    }

    public function getSessions() : array {
        return self::$sessions;
    }


}