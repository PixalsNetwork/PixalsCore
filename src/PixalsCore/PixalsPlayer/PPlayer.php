<?php

namespace PixalsCore\PixalsPlayer;

use Himbeer\LibSkin\LibSkin;
use Himbeer\LibSkin\SkinConverter;
use PixalsCore\LibSkin\SkinFace;
use pocketmine\player\Player;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\PermissionsManager;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsManagers\CoreManagers\ScoreboardManager;
use PixalsCore\PixalsManagers\CoreManagers\SkinManager;
use PixalsCore\PixalsPlayer\PSessionSystem\SessionManager;
use PixalsCore\PixalsTasks\ReloadDataTask;
use pocketmine\entity\Skin;
use pocketmine\scheduler\AsyncTask;
use skymin\skin\SkinTool;
use SOFe\AwaitGenerator\Await;

class PPlayer extends PlayerDataLoader{


    public $pixals_players = [];

    public function changePlayerNametag(Player $player) {
        $session = (new SessionManager)->getSession($player);
        $rank_prefix = $session->getRank();
        $rank_color = $session->getRankData()["RankOriginalData"][QueriesManager::RANK_COLOR];
        $clan = $session->getClanData()["ClanTag"];
        $player_name = $rank_prefix . " " . "$rank_color" . $player->getName() . "\n" . "  " ."§eClan §7» " . $clan;
        $player->setNameTag($player_name);
    }

    public function setTagUsed(Player $player, String $tag_name) : void {
        $queries_manager = new QueriesManager;
        $queries_manager->changeTagUsed($tag_name, $player);
        (new SessionManager)->getSession($player)->reloadTag($tag_name);

    }

    public function setCape(Player $player, String $cape_name) : void {
        $queries_manager = new QueriesManager;
        $cape = SkinConverter::imageToSkinDataFromPngPath(Pixals::getPluginInstance()->getDataFolder() . "CapesData/" . $cape_name . ".png");
        $new_skin = new Skin($player->getSkin()->getSkinId(), $player->getSkin()->getSkinData(), $cape, $player->getSkin()->getGeometryName(), $player->getSkin()->getGeometryData());
        $player->setSkin($new_skin);
        $player->sendSkin();
        $player->sendMessage("§7§l» §r§aPrepared Your Cape Successfully");
        $player->sendPopup("§7» §aEquipped a Cape !§7«");
        $queries_manager->editCapeUsed($player, $cape_name);
    }



    public function resetCape(Player $player) : void {
        $queries_manager = new QueriesManager;
        $new_skin = new Skin($player->getSkin()->getSkinId(), $player->getSkin()->getSkinData(), "", $player->getSkin()->getGeometryName(), $player->getSkin()->getGeometryData());
        $player->setSkin($new_skin);
        $player->sendSkin();
        $player->sendMessage("§7§l» §r§aRemoved Your Cape Successfully");
        $player->sendPopup("§7» §cRemoved Your Cape !§7«");
        $queries_manager->editCapeUsed($player, "none");
    }

    public function prepareCape(Player $player) : void {
        $queries_manager = new QueriesManager;
        Await::f2c(function() use ($player, $queries_manager){
            $cape_used = yield from Await::promise(fn($accept) => $queries_manager->getPlayerCape($player, $accept));
            if($cape_used == []) {
                $queries_manager->setupCape($player);
            } else if($cape_used[0]["used_cape"] !== "none") {
                $this->setCape($player, $cape_used[0]["used_cape"]);
            }
        });
    }
    
    public function uploadFaceToAPI(AsyncTask $async_task) : void {
        Pixals::getPluginInstance()->getServer()->getAsyncPool()->submitTask($async_task);
    }




}