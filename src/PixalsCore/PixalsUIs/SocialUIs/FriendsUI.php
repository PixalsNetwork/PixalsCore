<?php

namespace PixalsCore\PixalsUIs\SocialsUIs;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\ModalForm;
use PixalsCore\PixalsManagers\CoreManagers\PacketSenderManager;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsUIs\SocialsUIs\SocialUIElements\CustomOption;
use PixalsCore\PixalsUIs\SocialUI;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class FriendsUI extends SocialUI {

    public function __construct(Player $player)
    {
        $player->sendForm($this->FriendsMenu());
    }

    private function FriendsMenu() : MenuForm {
        return new MenuForm('Your Friends', '', [
            new MenuOption('§7» §3Add a Friend'),
            new MenuOption('§7» §3Remove a Friend'),
            new MenuOption('§7» §3View Your Friend List'),
            new MenuOption('§7» §3View Your Friend Requests')
        ], 
            function(Player $player, int $selected) : void {
                switch($selected) {
                    case 0:
                        $player->sendForm($this->addFriendMenu());
                    break;

                    case 1:
                        $player->sendForm($this->removeFriendMenu());
                    break;
                    
                    case 2:
                        $this->listFriends($player);
                    break;

                    case 3:
                        $this->listRequests($player);
                    break;
                }
            }
        );
    }

    private function addFriendMenu() : CustomForm {
        return new CustomForm('Add a New Friend', [
            new Input('f_name', '§7» §rPlease Enter The Player Name', 'e.g BreadLoaf')
        ], 
            function(Player $player, CustomFormResponse $response) : void {
                $target_name = $response->getString('f_name');
                (new PacketSenderManager)->sendDispatchCMDPacket("f add " . $target_name, $player->getName());
            }
        );
    }

    private function removeFriendMenu() : CustomForm {
        return new CustomForm("Remove Your Friend", [
            new Input('f_name', "§7» §rPlease Enter The Player Name", "e.g BreadLoaf")
        ], 
            function(Player $player, CustomFormResponse $response) : void {
                $target_name = $response->getString('f_name');
                (new PacketSenderManager)->sendDispatchCMDPacket("f remove $target_name", $player->getName());
            }
        );
    }

    private function listFriends(Player $player){
        $options = [];
        $names = [];
        $queries_manager = new QueriesManager;
        Await::f2c(function() use ($queries_manager, $player, $options, $names) {
            $list = yield from Await::promise(fn($accept) => $queries_manager->getFriendsList($player, $accept));
            if(!isset($list)) {
                $requests = $list;
                foreach($requests as $request) {
                    $names[] = $request["friend_name"];
                    $options[] = new MenuOption("§7» §e" . $request["friend_name"], new FormIcon("https://cdn.pixalsnetwork.com/player_faces/" . $request["friend_name"] . ".png"));
                }
            } else {
                $label = "§3[§b!§3] §7» §cSorry, You Don't Have Any Friends :(, Here's The Life Chicken Waiting.";

            }

            $fl = new MenuForm("Friends List", "", $options, function(Player $player, int $selected) use ($names): void {
                $name = $names[$selected];
                $player->sendForm($this->friendListMenu($name));
            });

            $player->sendForm($fl);
        });
    }

    private function listRequests(Player $player) : void {
        $options = [];
        $names = [];
        $queries_manager = new QueriesManager;
        $label = "";
        Await::f2c(function() use ($queries_manager, $player, $options, $names, $label) {
            $list = yield from Await::promise(fn($accept) => $queries_manager->getFriendsRequests($player, $accept));
            if(!isset($list)) {

                foreach($list as $request) {
                    $names[] = $request["adder_name"];
                    $options[] = new MenuOption("§7» §e" . $request["adder_name"], new FormIcon("https://cdn.pixalsnetwork.com/player_faces/" . $request["adder_name"] . ".png"));
                }
            } else {
                $label = "§3[§b!§3] §7» §cSorry, You Don't Have Any Friend Requests :(, Here's The Life Chicken Waiting.";
            }

            $fl = new MenuForm("Friend Requests", $label, $options, function(Player $player, int $selected) use ($names): void {
                $name = $names[$selected];
                $player->sendForm($this->friendRequestMenu($name));
            });

            $player->sendForm($fl);
        });
    }

    private function friendRequestMenu(String $accepted) : ModalForm {
        return new ModalForm("Accept/Deny Your Friend Request", '§7Please Choose Either to Accept or Deny This Request.',
            function(Player $player, bool $response) use ($accepted) : void {
                $response ? (new PacketSenderManager)->sendDispatchCMDPacket("f accept $accepted", $player->getName()) : (new PacketSenderManager)->sendDispatchCMDPacket("f deny $accepted", $player->getName());
            },
            "§7» §aAccept This Friend Request", "§7» §cDeny This Friend Request" 
        );
    }

    public function friendListMenu(String $friend_name) : MenuForm {
        return new MenuForm(
            "$friend_name - Settings",
            '',
            [
                new MenuOption("§7» §cRemove Your Friend"),
                new MenuOption("§7» §aReturn back to the Menu"),
            ],
            function(Player $player, int $selected) use ($friend_name) : void {
                switch($selected) {
                    case 0:
                        (new PacketSenderManager)->sendDispatchCMDPacket("f remove $friend_name", $player->getName());
                    break;

                    case 1:
                        $this->listFriends($player);
                    break;
                }
            }
        );
    }



}