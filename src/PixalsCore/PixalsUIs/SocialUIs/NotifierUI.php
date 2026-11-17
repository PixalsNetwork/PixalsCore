<?php


namespace PixalsCore\PixalsUIs\SocialsUIs;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsUIs\SocialUI;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class NotifierUI extends SocialUI{

    public function __construct(Player $player)
    {
        $this->NotifierMenu($player);
    }

    private function NotifierMenu(Player $player) : void {
        Await::f2c(function() use ($player) {
            $notifier_data = yield from Await::promise(fn($accept) => (new QueriesManager)->getPlayerInboxTimer($player, $accept));
            $notifer_menu = new CustomForm("Notifer - Settings", [
                new Label("label", "§7» Your Current Timer is Set to §b" . $notifier_data[0]["inbox_messages_period"]),
                new Dropdown("duration", "", [
                    "§7» §b5 Mins",
                    "§7» §b3 Mins",
                    "§7» §b1 Mins"
                ])
            ], 
                function(Player $player, CustomFormResponse $response) : void {
                    switch($response->getInt("duration")) {
                        case 0: 
                            $duration = "5 min";
                            (new QueriesManager)->editPlayerInboxTimer($player, $duration);
                            $player->sendMessage("§3[§b!§3] §7» §aChanged the Timer to $duration");
                        break;

                        case 1:
                            $duration = "3 min";
                            (new QueriesManager)->editPlayerInboxTimer($player, $duration);
                            $player->sendMessage("§3[§b!§3] §7» §aChanged the Timer to $duration");
                        break;

                        case 2:
                            $duration = "1 min";
                            (new QueriesManager)->editPlayerInboxTimer($player, $duration);
                            $player->sendMessage("§3[§b!§3] §7» §aChanged the Timer to $duration");
                        break;

                        default:
                            // NOPE
                        break;
                    }                    
                }
            );
            $player->sendForm($notifer_menu);
        });
    }

    
}