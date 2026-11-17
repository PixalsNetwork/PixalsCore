<?php



namespace PixalsCore\PixalsTestingFiles;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\player\Player;

class TestCatEntity extends Living {

    private const TYPE_ID = EntityIds::CAT;
    private $catName = "im a cat";


    public static function getNetworkTypeId() : String {
        return static::TYPE_ID;
    }

    public function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.7, 0.6, 0.9);
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {
        parent::entityBaseTick($tickDiff);
        $this->followTarget();
        
        return true;
    }

    public function getName(): string
    {
        return $this->catName;
    }


    public function getInitialDragMultiplier() : float {
        return 4.0;
    }

    public function getInitialGravity() : float {
        return 1.0;
    }

    public function setTargetEntity(?Entity $target): void
    {
        parent::setTargetEntity($target);
    }

    public function followTarget() : void {
        $target = $this->getTargetEntity();
        if($target->getPosition()->distance($this->getPosition()->asVector3()) >= 10) {
            if($target instanceof Player) {
                $direction = $target->getPosition()->add($this->getPosition()->getX(), $this->getPosition()->getY(), $this->getPosition()->getZ())->normalize();

                if($target->getMovementSpeed() > 1) {
                    $this->location->yaw = $target->getLocation()->getYaw();
                    $this->motion->x = $direction->x * 0.2;
                    $this->motion->y = $target->getPosition()->getY();
                    $this->motion->z = $direction->z * 0.2;
                } else {
                    $this->location->yaw = $target->getLocation()->getYaw() * -1;
                    $this->motion->x = $direction->x * 0.2;
                    $this->motion->y = $target->getPosition()->getY();
                    $this->motion->z = $direction->z * 0.2;
                }
            }
        }
    }

}