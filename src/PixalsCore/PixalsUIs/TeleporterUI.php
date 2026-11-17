<?php


namespace PixalsCore\PixalsUIs;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\PacketSenderManager;
use PixalsCore\PixalsManagers\SimpleManagers\SimplePacketSenderManager;
use pocketmine\player\Player;

class TeleporterUI extends PixalsUI{


    public function __construct(Player $player)
    {
        $this->TeleporterUI($player);
    }

    private function TeleporterUI(Player $player) : void {
        $nav_form = new MenuForm(
            "Teleporter",
            "Why are you Reading?, Go and Play!",
            [
                new MenuOption("§7» §eFFA§7 «\n§7» §eClick To Play! §7«"),
                new MenuOption("§7» §cThe§9Bridge§7 «\n§7» §eClick To Play! §7«"),
                new MenuOption("§7» §fSumo§7 «\n§7» §eClick To Play! §7«"),
                new MenuOption("§7» §cDu§9els§7 «\n§7» §eClick To Play! §7«"),
                new MenuOption("§7» §5Knockback§dFFA§7 «\n§7» §eClick To Play! §7«"),
            ],
            function(Player $player, int $selected) : void {
                switch($selected) {
                    case 0:
                        (new PacketSenderManager)->sendDispatchCMDPacket("ffa", $player->getName());
                    break;
                }
            }
        );
        $player->sendForm($nav_form);
    }


}