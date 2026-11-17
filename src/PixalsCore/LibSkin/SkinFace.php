<?php

namespace PixalsCore\LibSkin;


use pocketmine\player\Player;
use PixalsCore\Pixals;
use Exception;


class SkinFace {
    // Lib Skin Converter


    public const ACCEPTED_SKIN_SIZES = [
		64 * 32 * 4,
		64 * 64 * 4,
		128 * 128 * 4
	];

	public const SKIN_WIDTH_MAP = [
		64 * 32 * 4 => 64,
		64 * 64 * 4 => 64,
		128 * 128 * 4 => 128
	];

	public const SKIN_HEIGHT_MAP = [
		64 * 32 * 4 => 32,
		64 * 64 * 4 => 64,
		128 * 128 * 4 => 128
	];

	public static function validateSize(int $size) {
		if (!in_array($size, self::ACCEPTED_SKIN_SIZES)) {
			throw new Exception("Invalid skin size");
		}
	}

/**
     * @param string $path
     * @return Skin
     */
    public static function getSkinFromFile(string $path) : Skin{
        $img = imagecreatefrompng($path);
        $bytes = '';
        $l = (int) getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $r = ($rgba >> 16) & 0xff;
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        imagedestroy($img);
        return new Skin("Standard_Custom", $bytes);
    }

    public static function playerToFaceSkin(Player $player) {
        $pixals = Pixals::getPluginInstance();
        $skin = $player->getSkin()->getSkinData();
        $height = 64;
        $width = 64;
        $head = 8;
        $helm = 40;
        switch (strlen($skin)) {
            case 64 * 32 * 4:
                $height = 32;
                $width = 64;
                $head = 4;
                $helm = 20;
                break;
            case 64 * 64 * 4:
                $height = 64;
                $width = 64;
                $head = 8;
                $helm = 40;
                break;
            case 128 * 128 * 4:
                $height = 128;
                $width = 128;
                $head = 16;
                $helm = 80;
                break;
        }

        $img = imagecreatetruecolor(128, 128);
        $skin_img = imagecreatetruecolor($width, $height);
        $head_img = imagecreatetruecolor($head, $head);

        /*
         * Skin Data To Skin
         */
        $skinPos = 0;
        imagefill($skin_img, 0, 0, imagecolorallocatealpha($skin_img, 0, 0, 0, 127));
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $r = ord($skin[$skinPos]);
                $skinPos++;
                $g = ord($skin[$skinPos]);
                $skinPos++;
                $b = ord($skin[$skinPos]);
                $skinPos++;
                $a = 127 - intdiv(ord($skin[$skinPos]), 2);
                $skinPos++;
                $col = imagecolorallocatealpha($skin_img, $r, $g, $b, $a);
                imagesetpixel($skin_img, $x, $y, $col);
            }
        }
        imagesavealpha($skin_img, true);

        /*
         * Skin To Face Skin
         */
        imagecopymerge($head_img, $skin_img, 0, 0, $head, $head, $head, $head, 100);
        for ($x = 0; $x < $head; $x++) {
            for ($y = 0; $y < $head; $y++) {
                $rgba = imagecolorat($skin_img, $x + $helm, $y + $head);
                $alpha = ($rgba & 0x7F000000) >> 24;
                if (!$alpha >= 127) {
                    imagecopymerge($head_img, $skin_img, $x, $y, $x + $helm, $y + $head, 1, 1, 100);
                }
            }
        }
        imagecopyresized($img, $head_img, 0, 0, 0, 0, 128, 128, $head, $head);
        imagepng($img, $pixals->getDataFolder() . "PlayerFaces/" . $player->getName() . ".png");
        imagedestroy($head_img);
        imagedestroy($skin_img);
        imagedestroy($img);
        return $img;
    }
}