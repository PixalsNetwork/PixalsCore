<?php

namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Model;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\ModelTexture;

class MustacheModel extends Model {


    public function __construct()
    {
        $data_folder = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsData/";
        $textures = Pixals::getPluginInstance()->getDataFolder() . "CosmeticsTextures/";
        parent::__construct("mustache",  $data_folder . "/mustache.json", new ModelTexture($textures . "mustache.png"), "geometry.mustache", $data_folder . "mustachenonSlim.json", true, "cosmetics.mustache");
    }


}