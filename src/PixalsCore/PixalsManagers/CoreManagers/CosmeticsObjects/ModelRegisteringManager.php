<?php


namespace PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects;

use LogicException;
use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models\HaloModel;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models\MaskModel;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models\MustacheModel;
use PixalsCore\PixalsManagers\CoreManagers\CosmeticsObjects\Models\SmallerHatModel;

final class ModelRegisteringManager {



    private static $models_registered = [];


    public function registerModels() : void {
        /**
         * Models :
         * Golf Cap
         * Halo
         * Headset
         * Horns
         * Snowman
         */
        $models = [
            new MustacheModel,
            new SmallerHatModel,
            new HaloModel,
            new MaskModel
        ];
        
        foreach($models as $model) {
            if($model instanceof Model) {
                if(in_array($model->getModelName(), $models)) {
                    throw new LogicException("This Model is Already Registered!");
                } else {
                    if($model->getModelActivated()) {
                        self::$models_registered[$model->getModelName()] = $model;
                        Pixals::getPluginInstance()->getServer()->getLogger()->info("[§4ModelRegistrar§r] §aRegistered §e" . $model->getModelName() . " §aSuccessfully");
                    }
                }
            }
        }
    }

    public function isModelRegistered(Model $model) : bool {
        return array_key_exists($model->getModelName(), self::$models_registered);
    }


}