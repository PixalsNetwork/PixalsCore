<?php


namespace PixalsCore\PixalsNPCs\PixalsNPCsEntities;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;

class SpotLights extends Living {


    public function getName(): string
    {
        return 'SpotLight';
    }
    
    public function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.5, 2);
    }

    public static function getNetworkTypeId() : String {
        return "pixals:spot_light";
    }

    public function __construct(Location $loc)
    {
        parent::__construct($loc);
    }

    public function initEntity(CompoundTag $nbt) : void {
        parent::initEntity($nbt);
    }


}