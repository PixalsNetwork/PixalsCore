<?php


namespace PixalsCore\PixalsManagers\DBConnectorRefresher;

use PixalsCore\Pixals;
use pocketmine\scheduler\Task;

class ReQueryDBTask extends Task {



    public function onRun(): void
    {
        (new Reconnector)->refresher();
    }

    
}