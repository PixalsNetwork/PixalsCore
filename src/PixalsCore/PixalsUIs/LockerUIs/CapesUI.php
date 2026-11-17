<?php


namespace PixalsCore\PixalsUIs\LockerUIs;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PPlayer;
use PixalsCore\PixalsUIs\PixalsUI;
use pocketmine\player\Player;

class CapesUI extends PixalsUI{


    public function __construct(Player $player)
    {
        $this->CapesUI($player);
    }

    private function CapesUI(Player $player) : void {
        $pixals_player = new PPlayer;
        $capes_form = new MenuForm("Capes", "Choose Your Option !", [
            new MenuOption("§7» §cReset Your Skin §7«"),
            new MenuOption("§7» §eYour Wardrobe §7«"),
        ], function(Player $player, int $selected) use ($pixals_player) : void {
            switch($selected) {
                case 0:
                    $pixals_player->resetCape($player);
                break;

                case 1:
                    $this->unlockedCapes($player);
                break;
            }
        });
        $player->sendForm($capes_form);
    }

    private function getCapes() : array {
        $capes = [];
        $files = scandir(Pixals::getPluginInstance()->getDataFolder() . "CapesData/");
        foreach($files as $file) {
            if(pathinfo($file, PATHINFO_EXTENSION) == "png") {
                $file_name = pathinfo($file, PATHINFO_FILENAME);
                $capes["pixals.$file_name.permission"] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        return $capes;
    }

    private function unlockedCapes(Player $player) : void {
        $unlocked_capes_buttons = [];
        $unlocked_capes = [];
        
        $pixals_player = new PPlayer;
        $capes = $this->getCapes();
        foreach($capes as $permission => $cape) {
            if($player->hasPermission($permission)) {
                array_push($unlocked_capes, $cape);
                array_push($unlocked_capes_buttons, new MenuOption("§7» §aEquip §7«", new FormIcon("https://cdn.pixalsnetwork.net/cape_previews/" . $cape . "p.png", FormIcon::IMAGE_TYPE_URL)));
            }
        }
        array_push($unlocked_capes_buttons, new MenuOption("§7» §cExit §7«", new FormIcon("textures/gamesui/back", FormIcon::IMAGE_TYPE_PATH)));
        $unlocked_capes_form = new MenuForm("Your Wardrobe", "§7» §eChoose To Equip One Of Your Wonderful Capes!", $unlocked_capes_buttons, 
            function(Player $player, int $selected) use ($unlocked_capes, $pixals_player) : void {
                if($selected+1 > count($unlocked_capes)) {
                    $this->CapesUI($player);
                } else {
                    $cape_file = $unlocked_capes[$selected];
                    $pixals_player->setCape($player, $cape_file);
                }
            }
        
        );

        $player->sendForm($unlocked_capes_form);
    }
}