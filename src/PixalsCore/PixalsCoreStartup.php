<?php

namespace PixalsCore;

use alemiz\sga\StarGateAtlantis;
use PixalsCore\PixalsManagers\CoreManagers\CommandRegistering;
use PixalsCore\PixalsManagers\CoreManagers\ConnectionManager;
use PixalsCore\PixalsManagers\CoreManagers\LeaderboardsManager;
use PixalsCore\PixalsManagers\CoreManagers\RegisteringManager;
use PixalsCore\PixalsManagers\CoreManagers\TasksManager;
use PixalsCore\PixalsNPCs\PixalsNPCsEvents;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\utils\Config;
use ReflectionClass;

class PixalsCoreStartup {

    public int $PIXALS_CORE_VERSION = 9;
    public String $PIXALS_CORE_MODE = "Development";
    public String $PIXALS_CORE_MUST_MODE = "Production";
    public bool $NEEDED_MODE_DEVELOPMENT = true;
    public const MINIGAMES_MODE = false;
    public String $pixals_dev_mysql_pass = "carcar2233";
    public String $pixals_prod_mysql_pass = "pixalsdatabasemaineu2030.1!";

    public function coreStartup() {
        $cmd_manager = new CommandRegistering();
        $entity_manager = new RegisteringManager();
        $tasks_manager = new TasksManager;
        $leaderboard_manager = new LeaderboardsManager;
        $entity_manager->registerEntities();
        $cmd_manager->removeCommands();
        $cmd_manager->registerCommands();
        foreach(Pixals::getPluginInstance()->needed_dirs as $dir) {
            @mkdir(Pixals::getPluginInstance()->getDataFolder() . $dir . "/");
        }
        
        (new ConnectionManager)->connectToDB();
        Pixals::getPluginInstance()->stargate = StarGateAtlantis::getInstance();
        Pixals::getPluginInstance()->saved_face = Pixals::getPluginInstance()->getDataFolder() . "PlayerFaces/";
        Pixals::getPluginInstance()->holograms = new Config(Pixals::getPluginInstance()->getDataFolder() . "holograms.yml", 2, [
            "holograms_active" => false,
            "holograms_available" => []
        ]);   
        Pixals::getPluginInstance()->npcs = new Config(Pixals::getPluginInstance()->getDataFolder() . "npcs.yml", 2, [
            "npcs_active" => false,
            "npcs_registered" => []
        ]);             
        Pixals::getPluginInstance()->getLogger()->info("§7| §8PixalsCore §7- §aEnabled §7|");
        Pixals::getPluginInstance()->getServer()->getPluginManager()->registerEvents(new PixalsEvents, Pixals::getPluginInstance());
        Pixals::getPluginInstance()->getServer()->getPluginManager()->registerEvents(new PixalsNPCsEvents, Pixals::getPluginInstance());
        $tasks_manager->runTasks();
        $leaderboard_manager->autoLoad();
        $leaderboard_manager->autoReLoad();
    }

    /* public function customEntitiesId(String $id, String $entity_runtime_id) {
        $instance = StaticPacketCache::getInstance();
        $staticPacketCache = new ReflectionClass($instance);
        $property = $staticPacketCache->getProperty("availableActorIdentifiers");
        $property->setAccessible(true);
        /** @var AvailableActorIdentifiersPacket $packet 
        $packet = $property->getValue($instance);
        /** @var CompoundTag $root 
        $root = $packet->identifiers->getRoot();
        $idList = $root->getListTag("idlist") ?? new ListTag();
        $idList->push(CompoundTag::create()
            ->setString("id", $id)
            ->setString("bid", ""));
        $packet->identifiers = new CacheableNbt($root);
    } */
}