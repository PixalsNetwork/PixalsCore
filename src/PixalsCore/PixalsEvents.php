<?php

namespace PixalsCore;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\item\VanillaItems;
use pocketmine\block\utils\DyeColor;
use pocketmine\event\block\FarmlandHydrationChangeEvent;
use pocketmine\event\block\PressurePlateUpdateEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;
use alemiz\sga\events\ClientConnectedEvent;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\QueriesManager;
use PixalsCore\PixalsManagers\CoreManagers\ScoreboardManager;
use PixalsCore\PixalsPlayer\PPlayer;
use PixalsCore\PixalsPackets\PixalsPacketHandler;
use PixalsCore\PixalsPackets\DispatchCommandPackets\DispatchCommandPacket;
use PixalsCore\PixalsPackets\PermissionsPacket\PermissionsPacket;
use PixalsCore\PixalsPackets\OpenUIPackets\OpenUIPacket;
use PixalsCore\PixalsPackets\PlayerChatPackets\PlayerChatPacket;
use PixalsCore\PixalsPackets\ScoreboardPackets\ScoreboardPacket;
use PixalsCore\PixalsPlayer\PSessionSystem\SessionManager;
use PixalsCore\PixalsUIs\LockerUI;
use PixalsCore\PixalsUIs\SocialUI;
use PixalsCore\PixalsUIs\TeleporterUI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

class PixalsEvents implements Listener {

    public function onCreation(PlayerLoginEvent $e) {
        $pixals_player = new PPlayer;
        $player = $e->getPlayer();
        (new PPlayer)->preparePixalsPlayer($player);
        

    }

    public function onBlockUpdate(FarmlandHydrationChangeEvent $event): void {
        $block = $event->getBlock();
        $event->cancel();
    }

    public function onBlockBreak(BlockBreakEvent $e) : void {
        if(!PixalsCoreStartup::MINIGAMES_MODE) {
            if(!$e->getPlayer()->hasPermission("pixals.build")) {
                $e->cancel();
            }     
        }
    }

    public function onBlockPlace(BlockPlaceEvent $e) : void {
        if(!PixalsCoreStartup::MINIGAMES_MODE) {
            if(!$e->getPlayer()->hasPermission("pixals.build")) {
                $e->cancel();
            }     
        }
    }

    public function onJoin(PlayerJoinEvent $e) { 
        $player = $e->getPlayer();
        $pixals_player = new PPlayer;
        $pixals = Pixals::getPluginInstance();
        $e->setJoinMessage(" §a+  §c" . $player->getName());
        
        if(!PixalsCoreStartup::MINIGAMES_MODE) { 
            (new PPlayer)->preparePixalsConnectedPlayer($player);
        } else {
            (new PPlayer)->preparePixalsConnectedPlayer($player, true);
        }
        
        $packet = GameRulesChangedPacket::create([
            "locatorBar" => new BoolGameRule(false, false),
            "doDaylightCycle" => new BoolGameRule(false, false)
        ]);
        $player->getNetworkSession()->sendDataPacket($packet);
    
    }

    public function pressurePlate(PressurePlateUpdateEvent $e) : void {
        $entites = $e->getActivatingEntities();
        if(isset($entites)) {
            foreach($entites as $entity) {
                if(!$entity instanceof Player) return;
                $pushForce = $entity->getDirectionVector()->multiply(2.0)->add(0, 2, 0);
                $entity->setMotion($pushForce);
            }
        }
    }

    public function onQuit(PlayerQuitEvent $e) {
        $player = $e->getPlayer();
        $simple_scoreboard_manager = new ScoreboardManager();
        $e->setQuitMessage(" §4-  §c" . $player->getName());
        if(!PixalsCoreStartup::MINIGAMES_MODE) { $simple_scoreboard_manager->removeScoreboard($player); }
        (new SessionManager)->destroySession($player);
    }


    public function onChat(PlayerChatEvent $e) {
        $pixals = Pixals::getPluginInstance();

        $player = $e->getPlayer();
        $msg = $e->getMessage();
        $e->cancel();
        $session = (new SessionManager)->getSession($player);
        $rank_prefix = $session->getRank();
        $rank_color = $session->getRankData()["RankOriginalData"][QueriesManager::RANK_COLOR];
        $rank_chat_color = $session->getRankData()["RankOriginalData"][QueriesManager::RANK_CHAT];
        $clan = $session->getClanData()["ClanTag"];
        $tagPrefix = $session->getTag();

        if($tagPrefix == "None") {
            $format = "§7| " . "§e" . $clan . " §7| " . $rank_prefix . " $rank_color" . $player->getName() . " §7» $rank_chat_color" . $msg;
        } else {
            $format = "" . $tagPrefix . " " . "§7| " . "§e" . $clan . " §7| " . $rank_prefix . " $rank_color" . $player->getName() . " §7» $rank_chat_color" . $msg;   
        }

        Pixals::getPluginInstance()->getServer()->broadcastMessage($format);
    
    }



    public function Interaction(PlayerItemUseEvent $e) {
        $player = $e->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $pixals = Pixals::getPluginInstance();

        if($item->getTypeId() == VanillaItems::COMPASS()->getTypeId()){
            new TeleporterUI($player);
        }

        if($item->getTypeId() == VanillaItems::DYE()->setColor(DyeColor::RED())->getTypeId()) {
            new SocialUI($player);
        }

        if($item->getTypeId() == VanillaItems::EMERALD()->getTypeId()) {
            new LockerUI($player);
        }

        if($item->getTypeId() == VanillaItems::PAPER()->getTypeId()) {
            $messages = [
                "§7This paper... §cfeels heavy, like it's carrying all the lost words.",
                "§8Shadows creep across these lines, §7whispering forgotten dreams.",
                "§4Pain stains every letter, §7yet no one dares to read.",
                "§7It's not just ink, §cit's tears dried on paper.",
                "§8A silent scream, §7trapped between the folds of this §c§l§o§ktext§r§7.",
            ];

            $msg = $messages[array_rand($messages)];
            $player->sendMessage("§7» $msg");
        }
    }
    
    public function DropItem(PlayerDropItemEvent $e) {
        $e->cancel();
    }



    public function onDeath(EntityDamageEvent $e) {
        $player = $e->getEntity();
        if(!$player instanceof Player) { return; }
        if($e->getCause() == EntityDamageEvent::CAUSE_VOID) {
            if(PixalsCoreStartup::MINIGAMES_MODE) {
                if($player->getWorld()->getFolderName() == Pixals::getPluginInstance()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()) {
                    $player->teleport(Pixals::getPluginInstance()->getServer()->getWorldManager()->getWorldByName($player->getWorld()->getFolderName())->getSafeSpawn());
                    $player->sendMessage("§7» I hope you don't try this in the real world..");
                    $e->cancel();
        
                } else {
                    // NOPE
                }
            } else {
                $player->teleport(Pixals::getPluginInstance()->getServer()->getWorldManager()->getWorldByName($player->getWorld()->getFolderName())->getSafeSpawn());
                $player->sendMessage("§7» I hope you don't try this in the real world..");
                $e->cancel();
            }
        } 

        if($e->getCause() == EntityDamageEvent::CAUSE_FALL) {
            if (PixalsCoreStartup::MINIGAMES_MODE) {
                // NOPE
            } else {
                $e->cancel();
            }
        }
    }

    public function onHunger(PlayerExhaustEvent $e) {
        $e->cancel();
    }

    
    public function onClientConnection(ClientConnectedEvent $e) {
        $client = $e->getClient();

        $client->setCustomHandler(new PixalsPacketHandler($client->getSession()));
        $client->getProtocolCodec()->registerPacket(50, new DispatchCommandPacket());
        $client->getProtocolCodec()->registerPacket(55, new PermissionsPacket());
        $client->getProtocolCodec()->registerPacket(59, new OpenUIPacket());
        $client->getProtocolCodec()->registerPacket(30, new ScoreboardPacket());
        $client->getProtocolCodec()->registerPacket(103, new PlayerChatPacket());

    }

    
    


}

