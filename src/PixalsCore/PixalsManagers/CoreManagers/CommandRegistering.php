<?php

namespace PixalsCore\PixalsManagers\CoreManagers;

use oPinqzz\AdvancedCommands\AdvancedCommands;
use oPinqzz\AdvancedCommands\commands\CommandBuilder;
use PixalsCore\Pixals;
use PixalsCore\PixalsCommands\Handlers\CleanHandler;
use PixalsCore\PixalsCommands\Handlers\HologramsHandler;
use PixalsCore\PixalsCommands\Handlers\NPCHandler;
use PixalsCore\PixalsCommands\Handlers\PotatoHandler;
use PixalsCore\PixalsCommands\ReloadCommand;
use PixalsCore\PixalsManagers\TebexIntegrator\TebexCommandIntegrator;
use PixalsCore\PixalsNPCs\PixalsNPCsCommands;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class CommandRegistering {

    public $removed_cmds = ["version", "msg", "ban", "pardon", "help"];

    public function removeCommands() {
        foreach($this->removed_cmds as $cmd) {
            Pixals::getPluginInstance()->getServer()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand($cmd));
        }
    }

    public function registerCommands() {
        new AdvancedCommands(Pixals::getPluginInstance());
        $builder = new CommandBuilder;
        $commands = [
            "rank" => new TebexCommandIntegrator,
            "tags" => new TebexCommandIntegrator,
            "ppermissions" => new TebexCommandIntegrator
        ];

        foreach($commands as $command => $executor_class) {
            Pixals::getPluginInstance()->getCommand($command)->setExecutor($executor_class);
        }

        $builder->newCommand("clean", "Clean the Chat Place!", null, null, ["cl"])->setExecution(function(CommandSender $sender, array $args){
            (new CleanHandler)->commandHandle($sender, null);
        })->prepareCommandPermissions("pixals.cleanchat", $builder::ALLOWED_ALL)->build();

        $builder->newCommand("potato", "Get the Potato Tag", null, null, ["pot"])->setExecution(function(CommandSender $sender, array $args){
            (new PotatoHandler)->commandHandle($sender, null);
        })->prepareCommandPermissions("pixals.potato.claim", $builder::ALLOWED_ALL)->build();

        $builder->newCommand("holograms", "Holograms Pixals", null, null, ["ho", "hologram"])->setExecution(function(CommandSender $sender, array $args){
            (new HologramsHandler)->commandHandle($sender, $args);
        })->prepareCommandPermissions("pixals.holograms", $builder::ALLOWED_OPERATOR)->build();

        
        $builder->newCommand("npcs", "Pixals NPCs System", null, null, ["npc", "citzen"])->setExecution(function(CommandSender $sender, array $args){
            (new NPCHandler)->commandHandle($sender, $args);
        })->prepareCommandPermissions("pixals.npcs.spawn", $builder::ALLOWED_OPERATOR)->build();

        
        
    }

}