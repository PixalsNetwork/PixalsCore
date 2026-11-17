<?php


namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects;

use Himbeer\LibSkin\SkinConverter;

class ModelTexture {

    private $image_path = "";

    public function __construct(String $image_path)
    {
        $this->image_path = $image_path;
    }

    public function getImagePath() : String {
        return $this->image_path;
    }

    public function getImageBytes() : String {
        return SkinConverter::imageToSkinDataFromPngPath($this->getImagePath());
    }


}