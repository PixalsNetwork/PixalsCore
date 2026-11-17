<?php


namespace PixalsCore\PixalsTasks;

use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PPlayer;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class LoadDataAfterJoinTask extends Task {

    private $time = 3;
    private Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }


    public function onRun(): void
    {
        $this->time--;
        if($this->time == 1) {

            $this->getHandler()->cancel();
        }
    }




}