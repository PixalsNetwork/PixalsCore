<?php

namespace PixalsCore;

use PixalsCore\PixalsPlayer\PSessionSystem\Session;
use pocketmine\utils\Config;

class Everything {

    public $texts = [];

    public $npcs = [];


    public function formatTime(Session $session) : String {
        $total_seconds = intdiv($session->getOnlineTime(), 1000);
		$total_hours = intdiv($total_seconds, 3600);
		$total_min = intdiv($total_seconds % 3600, 59);
		$total_seconds = intval($total_seconds / 59);

        $formatted = sprintf("%02d:%02d:%02d", $total_hours, $total_min, $total_seconds);

        return $formatted;

    }

    public function getHologramsData() : Config {
        $holograms = Pixals::getPluginInstance()->holograms;

        return $holograms;
    }

    public function getNPCData() : Config {
        $npcs = Pixals::getPluginInstance()->npcs;

        return $npcs;
    }


}