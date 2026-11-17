<?php


namespace PixalsCore\PixalsNPCs\PixalsNPCsEntities;

use PixalsCore\Everything;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\PacketSenderManager;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class HumanNPC extends Human {
    
    private const PX_SCALE = "pxScale";

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setScale($nbt->getFloat(self::PX_SCALE, 1.0));
        $this->setNameTagAlwaysVisible();
        $this->setCanSaveWithChunk(true);
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setFloat(self::PX_SCALE, $this->getScale());
        return $nbt;
    }

    public function spawnToAll(): void
    {
        parent::spawnToAll();
        $this->setNameTagAlwaysVisible();

    }

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        $this->handleInteraction($player);
        return true;
    }

    public function handleInteraction(Player $player) : void {
        $everything = new Everything;
        $data = $everything->getNPCData()->get("npcs_registered")[$this->getName()]["data"];
        $packet_sender_manager = new PacketSenderManager;
        $localCMD = filter_var($data["localCMD"], FILTER_VALIDATE_BOOLEAN);
        $proxyCMD = filter_var($data["proxyCMD"], FILTER_VALIDATE_BOOLEAN);;
        $data_m = $data["data"];
        if ($localCMD && $proxyCMD) {
            $player->sendMessage("§7» §cAn Error has Occurred, Please Report that to the Innovation Wing of Pixals - Error: 2 CMD Types Are Enabled in NPC");
            return;
        }

        if($data_m !== "null") {
            $player->sendMessage($data_m);
            return;
        }

        switch (true) {
            case $localCMD:
                Pixals::getPluginInstance()->getServer()->dispatchCommand($player, $data_m);
            break;

            case $proxyCMD:
                $packet_sender_manager->sendDispatchCMDPacket($data_m, $player->getName());
            break;
        }

    }



}