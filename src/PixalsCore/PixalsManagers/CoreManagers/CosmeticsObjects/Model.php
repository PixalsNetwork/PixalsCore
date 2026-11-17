<?php

namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects;

use pocketmine\entity\Skin;
use skymin\skin\SkinTool;

class Model {

    private $name = "";
    private $file_path = "";
    private $model_geo_name = "";
    private $model_normal_path = "";
    private $permission = "";
    private ModelTexture $texture;
    private $activated = false;

    public function __construct(String $name, String $file_path, ModelTexture $texture, String $model_geo_name, String $normal_file, bool $activated, String $permission)
    {
        $this->name = $name;
        $this->file_path = $file_path;
        $this->model_geo_name = $model_geo_name;
        $this->texture = $texture;
        $this->activated = $activated;
        $this->model_normal_path = $normal_file;
        $this->permission = $permission;
    }

 

    public function getModelName() : String {
        return $this->name;
    }

    public function getModelFilePath() : String {
        return $this->file_path;
    }
    
    public function getModelNormalFilePath() : String {
        return $this->model_normal_path;
    }


    public function getModelActivated() : bool {
        return $this->activated;
    }

    public function getModelGeometryData() : String {
        return file_get_contents($this->getModelFilePath());
    }

    public function getModelNormalGeometryData() : String {
        return file_get_contents($this->getModelNormalFilePath());
    }

    public function getModelGeometryName() : String {
        return $this->model_geo_name;
    }

    public function getModelPermission() : String {
        return $this->permission;
    }

    public function getModelTexture() : ModelTexture {
        return $this->texture;
    }

    public function mergeModelTextureWithPlayerSkin(Skin $player_skin) : String {
        $texture_bytes = $this->getModelTexture()->getImageBytes();
        $player_skin_bytes = $player_skin->getSkinData();

        $new_skin_data = SkinTool::getImageTool($player_skin_bytes, SkinTool::IMAGE_TYPE_DATA)->mergeImage(SkinTool::getImageTool($texture_bytes, SkinTool::IMAGE_TYPE_DATA))->getSkinData();
        return $new_skin_data;
    }

    


}