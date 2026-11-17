<?php



namespace PixalsCore\PixalsNPCs\PixalsNPCsEntities\SpotLightFunctions;

use PixalsCore\Pixals;
use PixalsCore\PixalsNPCs\PixalsNPCsEntities\SpotLights;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
use pocketmine\player\Player;

final Class SplFunctions {

    public function createSpotLight(Player $player) : void {
        $spldata = Pixals::getPluginInstance()->getSpotLightData();
        $spot_light = new SpotLights($player->getLocation());
        $spot_light->spawnToAll();
        $spldata->set($spot_light->getId());
        $spldata->save();
    }

    public function startEvent(Player $player) : void {
        $spldata = Pixals::getPluginInstance()->getSpotLightData();
        foreach($spldata->getAll() as $id => $a) {
            echo $id;
            $spot_light = $player->getWorld()->getEntity($id);
            $animatepk = AnimateEntityPacket::create("start_lights", "", "", 0, "", 0, [$spot_light->getId()]);
            NetworkBroadcastUtils::broadcastPackets(Pixals::getPluginInstance()->getServer()->getOnlinePlayers(), [$animatepk]);
        }
    }


}