<?php


namespace PixalsCore\PixalsPlayer;

use DatabaseManager\PixalsLibs\DatabaseManager;
use Himbeer\LibSkin\SkinConverter;
use PixalsCore\LibSkin\SkinFace;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\PermissionsManager;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsManagers\CoreManagers\ScoreboardManager;
use PixalsCore\PixalsManagers\CoreManagers\SkinManager;
use PixalsCore\PixalsPlayer\PSessionSystem\SessionManager;
use PixalsCore\PixalsQueries\TestQuery;
use PixalsCore\PixalsTasks\LoadDataAfterJoinTask;
use PixalsCore\PixalsTasks\LoadDataTask;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use SOFe\AwaitGenerator\Await;

class PlayerDataLoader {

    public function loadData(Player $player) : void {
        $session_manager = new SessionManager;
		$queries_manager = new QueriesManager;
		Await::f2c(function() use ($player, $queries_manager, $session_manager){
			$player_data = yield from Await::promise(fn($accept, $fail) => $queries_manager->getPlayerData($player, $accept, $fail));
			$rank_name = $player_data[0][$queries_manager::PLAYER_RANK];
			$rank_data =  yield from Await::promise(fn($accept, $fail) => $queries_manager->getRankData($rank_name, $accept, $fail));
			$rank_prefix = $rank_data[0][$queries_manager::RANK_PREFIX];
			$ucoins = $player_data[0][$queries_manager::PLAYER_UCOINS];
			$net_level = $player_data[0][$queries_manager::PLAYER_NET_LEVEL];
            $clan = $player_data[0][$queries_manager::PLAYER_CLAN];
            if($clan != "None") {
                $clan_data = yield from Await::promise(fn($accept) => $queries_manager->getClanData($clan, $accept));
                $clan_prefix = $clan_data[0]["clan_tag"];
            } else {
                $clan_prefix = "None";
            }
            $tag_used = yield from Await::promise(fn($accept) => $queries_manager->getTagUsed($player, $accept));
            
            if($tag_used != null) {
                $tag_used_name = $tag_used[0][$queries_manager::TAG_NAME];
                if($tag_used_name != "None") { $tag_prefix = yield from Await::promise(fn($accept) => $queries_manager->getTagPrefix($tag_used_name, $accept)); } else { $tag_prefix = ""; }
            } else { $tag_prefix = "None"; }

            $clan_data_arr = ["ClanName" => $clan, "ClanTag" => $clan_prefix];
            $rank_data_arr = ["RankName" => $rank_name, "RankOriginalData" => $rank_data[0]];

            if(is_array($tag_prefix)) {
                $tag_prefix = $tag_prefix[0]["tag_prefix"];
            }

            $level_data = yield from Await::promise(fn($accept) => $queries_manager->getLevelData($player, $accept));
            $online_time = $player_data[0][$queries_manager::ONLINE_TIME];

            $session_manager->createSession($player, $rank_prefix, $ucoins, $net_level, $rank_data_arr, $tag_prefix, $clan_data_arr, $player_data[0], $level_data[0], $online_time);
        });
    }

    public function updatePlayTime() : void {
        $session_manager = new SessionManager;
        foreach($session_manager->getSessions() as $session) {
            $session->reloadPlayTime();
        }
    }

    private function LobbyItems (Player $player) : void { 

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        $compass = VanillaItems::COMPASS();
        $head = VanillaBlocks::MOB_HEAD()->asItem();
        // $paper = VanillaItems::PAPER();
        $flower = VanillaBlocks::POPPY()->asItem();
        $chest = VanillaBlocks::CHEST()->asItem();

        $sad_book = VanillaItems::PAPER();
        $thecore = VanillaItems::WRITTEN_BOOK();

        
        $compass->setCustomName("§r§l§6Teleporter");
        $head->setCustomName("§r§l§bProfile");

        $chest->setCustomName("§r§l§aLocker");
        $flower->setCustomName("§r§l§3Social");
        $sad_book->setCustomName("§r§tThe Sad Paper");
        $thecore->setCustomName("§r§bThe Core Feed");


        $player->getInventory()->setItem(4, $compass);
        $player->getInventory()->setItem(7, $chest);
        $player->getInventory()->setItem(0, $flower);
        $player->getInventory()->setItem(8, $head);
    }

    public function preparePixalsPlayer(Player $player) : void {
        $pixals_player = new PPlayer;
        $pixals = Pixals::getPluginInstance();
        $queries_manager = new QueriesManager;
        $queries_manager->setupPlayerData($player);
        
        $player->teleport(Pixals::getPluginInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
    }

    public function preparePixalsConnectedPlayer(Player $player, $mode = false) : void {
        $permissions_manager = new PermissionsManager;
        $pixals_player = new PPlayer;
        $skin_to_face_library = new SkinFace;
        $queries_manager = new QueriesManager;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        if($mode == false) {
            $scoreboard_manager = new ScoreboardManager;
            
            $this->LobbyItems($player);
            $pixals_player->changePlayerNametag($player);
            $pixals_player->prepareCape($player);
            $permissions_manager->loadPermissions($player);
            $scoreboard_manager->PixalsHubScoreboard($player);
            (new SkinManager)->prepareDefaultSkin($player);
            (new SkinManager)->savePlayerSkin($player);
            $skin_to_face_library->playerToFaceSkin($player);
            $pixals_player->uploadFaceToAPI(new FaceAsyncTask(file_get_contents(Pixals::getPluginInstance()->getDataFolder() . "PlayerFaces/" . $player->getName() . ".png"), $player->getName() . ".png"));
        } else {
            $pixals_player->changePlayerNametag($player);
            $pixals_player->prepareCape($player);
            (new SkinManager)->prepareDefaultSkin($player);
            (new SkinManager)->savePlayerSkin($player);
            $permissions_manager->loadPermissions($player);
        }


    }


}