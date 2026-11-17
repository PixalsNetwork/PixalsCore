<?php


namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Model;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\ModelTexture;

class HaloModel extends Model {

    public function __construct()
    {
        $data_folder = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsData/";
        $textures = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsTextures/";
        parent::__construct("halo", $data_folder . "halo.json", new ModelTexture($textures . "halo.png"), "geometry.halo", $data_folder . "halonormal.json", true, "cosmetics.halo");
    }


}