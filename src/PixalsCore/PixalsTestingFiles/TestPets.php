<?php

namespace PixalsCore\PixalsTestingFiles;

use Himbeer\LibSkin\SkinConverter;
use PixalsCore\Pixals;
use pocketmine\block\Block;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\entity\Zombie;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\player\Player;

class TestPets {

	private Zombie $zombie;
	private array $holding = [];


	public function setArray(Player $player, TestCatEntity $cat) {
		global $test;
		$test[$player->getName()] = $cat->getId();
	}

	public function getArray(Player $player) {
		global $test;
		return $test[$player->getName()];
	}

	public function createPet(Player $player) {
		$cat = new TestCatEntity($player->getLocation());
		$cat->setNameTag("im a cat");
		$this->setArray($player, $cat);
		$cat->setTargetEntity($player);
		$cat->spawnTo($player);
	}


	public function createZombie(Player $player) : void {
		$zombie = new TestZombie($player->getLocation());
		$zombie->setTargetEntity($player);
		$zombie->spawnToAll();
	}

	/*public function testLights(Player $player) {
		$skin_data = SkinConverter::imageToSkinDataFromPngPath(Pixals::getPluginInstance()->getDataFolder() . "/spot_light.png");
		$skin = new Skin("test", $skin_data, "", "geometry.spot_light", file_get_contents(Pixals::getPluginInstance()->getDataFolder() . "/spot_lights.geo.json"));
		$lights = new TestLights(new Location($player->getPosition()->getX() + 75, $player->getPosition()->getY()+2, $player->getPosition()->getZ() + 45, $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()));
		$lights->spawnToAll();

	}*/


}