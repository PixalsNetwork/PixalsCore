<?php



namespace PixalsCore\PixalsTestingFiles;

use pocketmine\block\Air;
use pocketmine\block\Fence;
use pocketmine\block\FenceGate;
use pocketmine\block\Flowable;
use pocketmine\block\Opaque;
use pocketmine\block\TallGrass;
use pocketmine\block\Wall;
use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\entity\Zombie;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\player\Player;

class TestZombie extends Zombie {


    public function entityBaseTick(int $tickDiff = 1): bool {
        parent::entityBaseTick($tickDiff);
        $this->followTarget();
        return true;
    }

    public function setTargetEntity(?Entity $target): void
    {
        if($this->getLocation()->distance($target->getLocation()->asVector3()) <= 50) {
            parent::setTargetEntity($target);
        }
    }

    public function followTarget() : void {
        $this->location->pitch = 0.5;
        $target = $this->getTargetEntity();
        if($target instanceof Player) {
            $direction = $target->getPosition()->subtract($this->getPosition()->getX(), $this->getPosition()->getY(), $this->getPosition()->getZ())->normalize();
            $block = $this->getTargetBlock(1);
            $world_block = $this->getWorld()->getBlockAt($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
            $eye_block = $this->getWorld()->getBlockAt($block->getPosition()->getX(), intval($this->getEyePos()->getY()), $block->getPosition()->getZ());
            if($eye_block instanceof Air && $world_block instanceof Flowable) {
                $this->motion->x = $direction->x * 0.2;
                $this->motion->y = $this->motion->y;
                $this->motion->z = $direction->z * 0.2;
                $this->jumpVelocity = 0.55;
                $block = $this->getTargetBlock(1);
                $rot = $this->calculateYawTo($this->getPosition(), $target->getPosition());
                $this->setRotation($rot, $this->getLocation()->getPitch()); 
            } else if(!$world_block instanceof Air && $eye_block instanceof Air) {
                $this->jump();
                echo $block->getPosition()->getY() . "\n";
                echo $this->getPosition()->getY() . "\n";
                echo $this->getEyePos()->getY() . "Eye Pos\n";
            } else if(!$eye_block instanceof Air){
                $left_block = $this->getLeftBlock($this->getPosition(), $this->getHorizontalFacing());
                $right_block = $this->getRightBlock($this->getPosition(), $this->getHorizontalFacing());
                $direction_left = $left_block->subtract($this->getPosition()->getX(), $this->getPosition()->getY(), $this->getPosition()->getZ())->normalize();
                $direction_right = $right_block->subtract($this->getPosition()->getX(), $this->getPosition()->getY(), $this->getPosition()->getZ())->normalize();
                $new_block1 = $this->getWorld()->getBlock($direction_left);
                $new_block2 = $this->getWorld()->getBlock($direction_right);
                if($this->getWorld()->getBlockAt(intval($direction_left->x), intval($this->getEyePos()->getY()), intval($direction_left->z)) instanceof Air) {
                    $this->motion->x = $new_block1->getPosition()->x * 0.2;
                    $this->motion->z = $new_block1->getPosition()->z * 0.2;
                    if($new_block1->getPosition()->equals($this->getPosition())) {
                        echo "you have reached your destination\n";
                    }
                } else {
                    $this->motion->x = $new_block2->getPosition()->x * 0.2;
                    $this->motion->z = $new_block2->getPosition()->z * 0.2;
                    if($new_block2->getPosition()->equals($this->getPosition())) {
                        echo "you have reached your destination\n";
                    }
                }
            }
            if($this->getWorld()->getBlockAt($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ()) instanceof Water) {
                $this->jump();
            }

            if($this->getPosition()->distance($target->getPosition()) <= 0.5
             && !$this->getWorld()->getBlockAt($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ()) instanceof Wall) {
                $target->knockBack($this->getPosition()->getX()+1, $this->getPosition()->getZ()+2);
                $target->attack(new EntityDamageEvent($this, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 1.5));
            }
        }
    }

    private function calculateYawTo(Vector3 $from, Vector3 $to): float {
        $dx = $to->x - $from->x;
        $dz = $to->z - $from->z;

        // Calculate the angle in radians and convert to degrees
        $yaw = atan2(-$dx, $dz) * 180 / M_PI;

        return $yaw;
    }

    private function getLeftBlock(Vector3 $position, int $facingDirection): Vector3 {
        switch ($facingDirection) {
            case Facing::NORTH:
                return $position->add(-1, 0, -1); // Left of north is east
            case Facing::WEST:
                return $position->add(-1, 0, 0); // Left of west is south
            case Facing::SOUTH:
                return $position->add(-1, 0, 1); // Left of south is west
            case Facing::EAST:
                return $position->add(1, 0, 0); // Left of east is north
        }
    }
    
    // Get the block to the right based on the entity's facing direction
    private function getRightBlock(Vector3 $position, string $facingDirection): Vector3 {
        switch ($facingDirection) {
            case Facing::NORTH:
                return $position->add(1, 0, 0); // Left of north is east
            case Facing::WEST:
                return $position->add(0, 0, 1); // Left of west is south
            case Facing::SOUTH:
                return $position->add(-1, 0, 0); // Left of south is west
            case Facing::EAST:
                return $position->add(0, 0, -1); // Right of east is south
        }
    }



}