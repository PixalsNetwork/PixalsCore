<?php


namespace PixalsCore\PixalsManagers\CoreManagers;

use DatabaseManager\PixalsLibs\DatabaseManager;
use PixalsCore\Pixals;
use PixalsCore\PixalsCoreStartup;
use PixalsCore\PixalsThreads\PSqlThread;

class ConnectionManager {

    public function connectToDB() : void {
        $pixals_bv = new PixalsCoreStartup();
        $database_manager = new DatabaseManager;
        $dev_pass = $pixals_bv->pixals_dev_mysql_pass;
        $prod_pass = $pixals_bv->pixals_prod_mysql_pass;
        $pl = Pixals::getPluginInstance();
        if($pixals_bv->PIXALS_CORE_MODE == "Development") {
            if(!$pixals_bv->NEEDED_MODE_DEVELOPMENT) {
                Pixals::getPluginInstance()->getLogger()->info("§7» §cCan't Run Development Mode on Production Server");
                Pixals::getPluginInstance()->getServer()->getPluginManager()->disablePlugin(Pixals::getPluginInstance());
            } else {
                $database_manager->connectDB(
                    "127.0.0.1", 
                    "root", 
                    $dev_pass, 
                    "s33_PixalsNetworkDB", 
                    3306, 
                    $pl
                );
                Pixals::getPluginInstance()->getLogger()->info("[§aConnection Manager§r] §aConnected to the Database");

                $pixals_sqlsetup_thread = new PSqlThread("127.0.0.1", "root", $dev_pass, "s33_PixalsNetworkDB");
                $pixals_sqlsetup_thread->start();

            } 
        } else {
            
                if($pixals_bv->PIXALS_CORE_MODE == "Production") {
                if(!$pixals_bv->NEEDED_MODE_DEVELOPMENT) {
                    
                    $database_manager->connectDB(
                        "127.0.0.1", 
                        "pixals", 
                        $prod_pass, 
                        "s33_PixalsNetworkDB", 
                        3306, 
                        $pl
                    );
                    $pixals_sqlsetup_thread = new PSqlThread("127.0.0.1", "pixals", $prod_pass, "s33_PixalsNetworkDB");
                    $pixals_sqlsetup_thread->start();
            
                    Pixals::getPluginInstance()->getLogger()->info("[§aConnection Manager§r] §aConnected to the Database");

                
                } else {
                    Pixals::getPluginInstance()->getLogger()->info("§7» §cCan't Run Production Mode on Development Server");
                    Pixals::getPluginInstance()->getServer()->getPluginManager()->disablePlugin(Pixals::getPluginInstance());
                }
            }
        }
    }





}