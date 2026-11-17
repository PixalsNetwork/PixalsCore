<?php


namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Model;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\ModelTexture;

class MaskModel extends Model {

    public function __construct()
    {
        $data_folder = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsData/";
        $textures = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsTextures/";
        parent::__construct("mask", $data_folder . "mask.json", new ModelTexture($textures . "mask.png"), "geometry.mask", $data_folder . "masknormal.json", true, "cosmetics.mask");
    }

}