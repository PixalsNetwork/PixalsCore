<?php


namespace PixalsCore\PixalsManagers\CoreManagers;

use PixalsCore\Pixals;
use PixalsCore\PixalsManagers\DBConnectorRefresher\ReQueryDBTask;
use pocketmine\scheduler\Task;

class TasksManager {

    public function runTasks() : void {
        $tasks = [
            new ReQueryDBTask
        ];
    

    }



}