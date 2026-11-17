<?php


namespace PixalsCore\PixalsUIs;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use PixalsCore\PixalsUIs\SocialsUIs\FriendsUI;
use PixalsCore\PixalsUIs\SocialsUIs\NotifierUI;
use pocketmine\player\Player;

class SocialUI extends PixalsUI {


    public function __construct(Player $player)
    {
        $player->sendForm($this->socialUI());
    }

    protected function socialUI() : MenuForm {
        /**
         * Friends, DMs , Notifier Settings, Profile
         * Clans MUST Be Through Commands To Avoid The Abusing of the System.
         * 
         */

        $social_form = new MenuForm("Social Menu", '', [
            new MenuOption('§7» §aYour Friends'),
            new MenuOption('§7» §bInbox Notifier'),
        ], 
            function(Player $player, int $selected) : void {
                switch($selected) {
                    case 0:
                        new FriendsUI($player);
                    break;

                    case 1:
                        new NotifierUI($player);
                    break;

                }
            }
        
        );

        return $social_form;
        
    }



    private function DirectMessagesMenu(Player $player) : void {

    }


    
    private function ProfileMenu(Player $player) : void {

    }


}