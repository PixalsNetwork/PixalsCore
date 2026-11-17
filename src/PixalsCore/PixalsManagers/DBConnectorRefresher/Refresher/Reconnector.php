<?php


namespace PixalsCore\PixalsManagers\DBConnectorRefresher;


use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\ConnectionManager;
use PixalsCore\PixalsQueries\Queries;
use SOFe\AwaitGenerator\Await;

class Reconnector {


    public function reQueryDB() : void {

        Await::f2c(function() {
            $db_status = yield from Await::promise(function($accept, $refuse){

            });

            if($db_status == false) {
                // IDK
            }
        });

    }

    public function refresher() : void {
        
        Pixals::getPluginInstance()->getQueriesManager()->executeQuery(Queries::refresh_db, ["type" => "execute"], ["t" => 1]);
        Pixals::getPluginInstance()->getLogger()->info("§7[§aReconnector§7] §aConnection with Database has been refreshed");

    }



}