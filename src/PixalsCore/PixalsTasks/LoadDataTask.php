<?php


namespace PixalsCore\PixalsTasks;

use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PPlayer;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class LoadDataTask extends Task {

    private $time = 1;
    private Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }


    public function onRun(): void
    {
        $this->time--;
        if($this->time <= 0) {
            (new PPlayer)->loadData($this->player);
            $this->getHandler()->cancel();
        }
    }




}