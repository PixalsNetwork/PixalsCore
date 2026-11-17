<?php


namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Model;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\ModelTexture;

class SmallerHatModel extends Model {

    public function __construct()
    {
        $data_folder = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsData/";
        $textures = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsTextures/";
        parent::__construct("smallerhat", $data_folder . "smallerhat.json", new ModelTexture($textures . "smallerhat.png"), "geometry.smallerhat", $data_folder . "smallerhatnormal.json", true, "cosmetics.smallhat");
    }

}