<?php


namespace PixalsCore\PixalsUIs;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use PixalsCore\PixalsUIs\LockerUIs\CapesUI;
use PixalsCore\PixalsUIs\LockerUIs\TagsUI;
use pocketmine\player\Player;

class LockerUI extends PixalsUI{

    public function __construct(Player $player)
    {
        $this->LockerUI($player);
    }

    private function LockerUI(Player $player) {
        $locker_form = new MenuForm("Locker", "", [
            new MenuOption("§6Capes"),
            new MenuOption("§cTags")
        ], 
            function(Player $player, int $selected) : void {
                switch($selected) {
                    case 0:
                        new CapesUI($player);
                    break;

                    case 1:
                        new TagsUI($player);
                    break;
                }
            }
        );

        $player->sendForm($locker_form);
    }


}