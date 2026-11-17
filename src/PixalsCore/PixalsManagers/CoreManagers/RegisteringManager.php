<?php

namespace PixalsCore\PixalsManagers\CoreManagers;

use PixalsCore\CoreHandlers\FloatingTextHandlers\FloatingTextEntity;
use PixalsCore\Pixals;
use PixalsCore\PixalsCoreStartup;
use PixalsCore\PixalsNPCs\PixalsNPCsEntities\HumanNPC;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use RuntimeException;

class RegisteringManager {


    public function registerEntities() {
        $dir = Pixals::getPluginInstance()->getDataFolder() . "NPCs/";

        EntityFactory::getInstance()->register(HumanNPC::class, function(World $world, CompoundTag $nbt) : HumanNPC {

            $npc = new HumanNPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);

            $npc->setNameTagVisible(true);
            $npc->setNameTagAlwaysVisible(true);
            return $npc;
        }, ["HumanNPC"]);

        
        EntityFactory::getInstance()->register(FloatingTextEntity::class, fn(World $world, CompoundTag $nbt) : FloatingTextEntity => throw new RuntimeException("Did not expect floating text entity to save"), ["cosmicpe:floating_text"]);

    }

    private function registerEntityIds(String $id) {
        $pxls_start = new PixalsCoreStartup;
        // $pxls_start->customEntitiesId($id, "");
        // echo "registered\n";
    }


}