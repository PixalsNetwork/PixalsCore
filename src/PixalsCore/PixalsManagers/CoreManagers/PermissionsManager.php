<?php

namespace PixalsCore\PixalsManagers\CoreManagers;

use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PPlayer;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class PermissionsManager {

    public $server_perms = [];
    
    
    public function PermissionsManager(Player $player) {
        if(!isset($this->server_perms[strval($player->getUniqueId())])) {
            // echo "nope";
            return $this->server_perms[strval($player->getUniqueId())] = $player->addAttachment(Pixals::getPluginInstance());
        }
        return $this->server_perms[strval($player->getUniqueId())];

    }



    public function setPermission(Player $server_player, String $permission) {
        $player = new PPlayer;
       $this->PermissionsManager($server_player)->setPermission($permission, true);
    }

    public function removePermission(Player $player, String $permission) {
        $effective_permissions = $player->getEffectivePermissions();
        foreach($effective_permissions as $permissions) {
            if($permissions->getPermission() == $permission) {
                $permissions->getAttachment()->unsetPermission($permission);
            }
        }
    }


    public function loadPermissions(Player $player) {
        $queriesmanager = new QueriesManager();
        Await::f2c(function() use ($player, $queriesmanager){
            $rank_name = yield from Await::promise(fn($accept, $fail) => $queriesmanager->getPlayerData($player, $accept, $fail));
            $rank_permissions = yield from Await::promise(fn($accept) => $queriesmanager->getRankPermissions($rank_name[0]["player_rank"], $accept));
            foreach($rank_permissions as $permission_array) {
                foreach($permission_array as $permission) {
                    $this->setPermission($player, $permission);

                }
            }
            $player_permissions = yield from Await::promise(fn($accept) => $queriesmanager->getPlayerPermissions($player, $accept));
            if(isset($player_permissions)) {
                foreach($player_permissions as $permission_array) {
                    foreach($permission_array as $permission) {
                       $this->setPermission($player, $permission);

                    }
                }
            }
            
        });
    }


   
    public function editPermissions(Player $player, String $permission, String $edit) {
        if($edit == "add") {
            $this->PermissionsManager($player)->setPermission($permission, true);
        } 
        if ($edit == "delete"){
            $this->removePermission($player, $permission);
            // echo $permission;
        }
    }

}