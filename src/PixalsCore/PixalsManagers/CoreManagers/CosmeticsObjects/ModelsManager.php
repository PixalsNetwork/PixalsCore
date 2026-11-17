<?php

namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects;

use Himbeer\LibSkin\SkinConverter;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\SkinManager;
use PixalsCore\PixalsPlayer\PSessionSystem\SessionManager;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

final class ModelsManager extends SkinManager {


    public function setModel(Model $model, Player $player) : void {
        $model_registrar = new ModelRegisteringManager;
        $new_skin_data = $model->mergeModelTextureWithPlayerSkin($player->getSkin());
        if($this->determinePlayerSkinType($player) == "geometry.humanoid.custom") {
            $geo = $model->getModelNormalGeometryData();
        } else {
            $geo = $model->getModelGeometryData();
        }
        $new_skin = new Skin($player->getSkin()->getSkinId(), $new_skin_data, $player->getSkin()->getCapeData(), $model->getModelGeometryName(), $geo);
        $player->setSkin($new_skin);
        $player->sendSkin();
    }

    public function removeModel(Player $player) : void {
        if($this->determinePlayerSkinType($player) == "geometry.humanoid.custom") {
            $geo = Pixals::getPluginInstance()->getDataFolder() . "DefaultGeos/defaultgeo.json";
        } else {
            $geo = Pixals::getPluginInstance()->getDataFolder() . "DefaultGeos/defaultSlim.json";
        }
        $skin_type = (new SessionManager)->getSession($player)->getSkinFormat();
        $skin_path = Pixals::getPluginInstance()->getDataFolder() . "PlayerSkins/" . $player->getName() . ".png";
        $original_skin = new Skin($player->getSkin()->getSkinId(), SkinConverter::imageToSkinDataFromPngPath($skin_path), $player->getSkin()->getCapeData(), $skin_type, file_get_contents($geo));
        $player->setSkin($original_skin);
        $player->sendSkin();
    }



}