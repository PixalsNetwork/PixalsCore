<?php 


namespace PixalsCore\PixalsManagers\CoreManagers;

use Himbeer\LibSkin\SkinConverter;
use PixalsCore\Pixals;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\convert\LegacySkinAdapter;
use pocketmine\player\Player;

class SkinManager {

    public function prepareDefaultSkin(Player $player) : void {
        $skin_name = $player->getSkin()->getGeometryName();
        $adapter = new LegacySkinAdapter;
        if($player->getPlayerInfo()->getExtraData()["PersonaSkin"]) {
            $skin = SkinConverter::imageToSkinDataFromPngPath(Pixals::getPluginInstance()->getDataFolder() . "DefaultSkin/defaultskin.png");
            $geo = file_get_contents(Pixals::getPluginInstance()->getDataFolder() . "DefaultSkin/defaultgeo.json");
            $skin = new Skin($player->getSkin()->getSkinId(), $skin, $player->getSkin()->getCapeData(), "geometry.humanoid.custom", $geo);
            $player->setSkin($skin);
            $player->sendSkin();
        }
    }

    public function determinePlayerSkinType(Player $player) : string {
        $skin_name = $player->getSkin()->getGeometryName();

        return $skin_name;

    }

    public function savePlayerSkin(Player $player) : void {
        SkinConverter::skinDataToImageSave($player->getSkin()->getSkinData(), Pixals::getPluginInstance()->getDataFolder() . "PlayerSkins/" . $player->getName() . ".png");
    }


}