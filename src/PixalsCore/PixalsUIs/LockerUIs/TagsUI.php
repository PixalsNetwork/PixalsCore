<?php


namespace PixalsCore\PixalsUIs\LockerUIs;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsPlayer\PPlayer;
use PixalsCore\PixalsUIs\PixalsUI;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class TagsUI extends PixalsUI {

    public function __construct(Player $player)
    {
        $this->TagsMenu($player);
    }


    private function TagsMenu(Player $player) : void {
        $pixals_player = new PPlayer;
        $tags_form = new MenuForm("Tags", "Choose Your Option", 
            [
                new MenuOption("§cReset Your Tag"),
                new MenuOption("§aOwned Tags")
            ],
            function(Player $player, int $selected) use ($pixals_player) : void {
                switch($selected) {
                    case 0:
                        $pixals_player->setTagUsed($player, "None");
                        $player->sendMessage("§7| §3Pixals §l§7» §r§aRemoved Your Tag");
                    break;

                    case 1:
                        $this->OwnedTagsMenu($player);
                    break;
                }
            }
        );
        $player->sendForm($tags_form);
    }

 
    private function OwnedTagsMenu(Player $player) : void {
        $simple_queries_manager = new QueriesManager;
        $buttons = [];
        $tags_shown = [];
        $pixals_player = new PPlayer;
        Await::f2c(function () use ($player, $simple_queries_manager, $buttons, $tags_shown, $pixals_player){
            $tagsOwned = yield from Await::promise(fn($accept) => $simple_queries_manager->getPlayerOwnedTags($player, $accept));
            if($tagsOwned == null) {
                $player->sendMessage("§7| §3Pixals §l§7» §r§cYou Don't Own Any Tags");
                return;
            } else {
                foreach($tagsOwned as $tag) {
                    if($tag["tag_name"] !== "None") {
                        $tag_prefix = yield from Await::promise(fn($accept) => $simple_queries_manager->getTagPrefix($tag["tag_name"], $accept));
                        $buttons[$tag["tag_name"]] = new MenuOption($tag_prefix[0]["tag_prefix"]); 
                        $tags_shown[] = $tag["tag_name"];
                    }
                }

                $tags_owned_form = new MenuForm("Your Tags", "Choose The Tag The Shows Your Style!",
                    $buttons,
                    function(Player $player, int $selected) use ($pixals_player, $tags_shown) : void {
                        $pixals_player->setTagUsed($player, $tags_shown[$selected]);
                        $player->sendMessage("§7| §3Pixals §l§7» §r§aUpdated Your Tag Successfully");
                    }
                );
                $player->sendForm($tags_owned_form);
            }
        });
    }



}