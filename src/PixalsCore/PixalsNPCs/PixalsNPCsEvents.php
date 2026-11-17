<?php

namespace PixalsCore\PixalsNPCs;

use PixalsCore\BasicValues;
use PixalsCore\Everything;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\PacketSenderManager;
use PixalsCore\PixalsManagers\SimpleManagers\SimplePacketSenderManager;
use PixalsCore\PixalsNPCs\PixalsNPCsEntities\HumanNPC;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class PixalsNPCsEvents implements Listener {


    public function onInteract(EntityDamageByEntityEvent $e) {
        $entity = $e->getEntity();
        $player = $e->getDamager();
        if($player instanceof Player) {
            if($player->getInventory()->getItemInHand()->getTypeId() == VanillaItems::WOODEN_AXE()->getTypeId()) {
                if($player->hasPermission("pixals.destroynpcs") && $player->hasPermission("pixals.highstaff") && $player->hasPermission("pixals.devteam")) {
                    $entity->kill();
                }
            } else {
                if($entity instanceof HumanNPC) {
                    $entity->handleInteraction($player);
                    $e->cancel();
                }
            }
        }
    }

    

}