<?php

namespace PixalsCore\LibSkin;

use pocketmine\player\Player;

class TestIP {

    public function getPlayerIP(Player $player) : String {
        $ip = $player->getNetworkSession()->getIp();
        return $ip;
    }

    public function getPlayerPort(Player $player) : int {
        $port = $player->getNetworkSession()->getPort();
        return $port;
    }

    
    public function CameraTest(Player $player) {
        $x = $player->getPosition()->getX();
        $y = $player->getPosition()->getY();
        $z = $player->getPosition()->getZ();
        $camera_pos = new Vector3($x+1, $y+1, $z+1.75);
        $setCameraInstruction = new SetCameraInstruction();
        $setCameraInstruction->setPreset(CameraPresets::FREE());
        $setCameraInstruction->setEase(CameraSetInstructionEaseType::IN_CIRC, 0.5);
        $setCameraInstruction->setCameraPosition($camera_pos);

        $setCameraInstruction->send($player);
    }

}