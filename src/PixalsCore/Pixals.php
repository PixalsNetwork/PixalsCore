<?php

namespace PixalsCore;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use DatabaseManager\PixalsLibs\DatabaseManager;
use DatabaseManager\PixalsLibs\Queries\QueriesManager;
use PixalsCore\PixalsManagers\CoreManagers\ConnectionManager;

class Pixals extends PluginBase {

    private static $instance;

    public $npcs;
    public $holograms;
    public $stargate;
    public $fly;
    public $vanish;
    public $saved_face;
    public $rfly = [];

    public $needed_dirs = ["PlayerFaces", "NPCs", "NPCsData", "CapesData", "SpotLightData", "DefaultSkin", "CosmeticsData", "CosmeticsTextures", "PlayerSkins", "DefaultGeos"];

    public function onEnable() : void {
        self::$instance = $this;
        $pixals_bv = new PixalsCoreStartup();
        (new ConnectionManager)->connectToDB();
        $pixals_bv->coreStartup();
        
    }

    public function onDisable() : void {
        $this->getLogger()->info("§7| §8PixalsCore §7- §cDisabled §7|");
        $this->getLogger()->info("§7| §3Connection Status §7- §cDisconnected §7|");
        
    }

    public static function getPluginInstance() : self {
        return self::$instance;
    }

    public function getQueriesManager() : QueriesManager {
        $database_manager = new DatabaseManager;
        return $database_manager->getQueriesManager();
    }


 
    public function getSpotLightData() : Config {
        $spldata = new Config($this->getDataFolder() . "SpotLightData/spldata.yml", 2);
        return $spldata;
    }
    

    public function getProductionPass() : String {
        $pxls_start = new PixalsCoreStartup;
        return $pxls_start->pixals_prod_mysql_pass;
    }


    
    
    
    public function GeneratePixalsSecurityAuthCode() : String {
        $chars = "ABDCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!#";
        $psc = "PX-";
        for($i=0; $i<6; $i++) {
            $psc .= $chars[rand(0, strlen($chars)-1)];
        }
        return $psc;
    }
    

    
    

    


}