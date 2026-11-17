<?php


namespace PixalsCore\PixalsTasks;

use Closure;
use pocketmine\scheduler\Task;

class ReloadDataTask extends Task {

    private $time = 0;
    private $callable;

    public function __construct(Closure $function, int $time)
    {
        $this->time = $time;
        $this->callable = $function;
    }

    public function onRun(): void
    {
        $this->time--;
        if($this->time == 0) {
            ($this->callable)();
            $this->time = 10;
        }
    }

}