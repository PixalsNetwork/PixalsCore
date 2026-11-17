<?php


namespace PixalsCore\PixalsUIs;

use pocketmine\player\Player;

abstract class PixalsUI {

    abstract public function __construct(Player $player);

}