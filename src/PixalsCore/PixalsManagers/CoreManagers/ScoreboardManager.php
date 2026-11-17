<?php

declare(strict_types=1);

namespace PixalsCore\PixalsManagers\CoreManagers;

use PixalsCore\Everything;
use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PSessionSystem\SessionManager;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

class ScoreboardManager{

    /** @var array<string, string> */
    private array $scoreboards = [];

    public function getObjectiveName(Player $player): ?string{
        return $this->scoreboards[$player->getName()] ?? null;
    }

    public function newScoreboard(
        Player $player,
        string $objectiveName,
        string $displayName
    ): void{
        if(isset($this->scoreboards[$player->getName()])){
            $this->removeScoreboard($player);
        }

        $packet = SetDisplayObjectivePacket::create(
            SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR,
            $objectiveName,
            $displayName,
            "dummy",
            SetDisplayObjectivePacket::SORT_ORDER_DESCENDING
        );

        $player->getNetworkSession()->sendDataPacket($packet);

        $this->scoreboards[$player->getName()] = $objectiveName;
    }

    public function removeScoreboard(Player $player): void{
        $objectiveName = $this->getObjectiveName($player);

        if($objectiveName === null){
            return;
        }

        $entries = [];

        for($i = 1; $i <= 20; ++$i){
            $entry = new ScorePacketEntry();

            $entry->scoreboardId = $i;
            $entry->objectiveName = $objectiveName;
            $entry->score = 0;
            $entry->type = ScorePacketEntry::TYPE_REMOVE;
            $entry->actorUniqueId = null;
            $entry->customName = null;

            $entries[] = $entry;
        }

        $player->getNetworkSession()->sendDataPacket(
            SetScorePacket::create($entries)
        );

        $player->getNetworkSession()->sendDataPacket(
            RemoveObjectivePacket::create($objectiveName)
        );

        unset($this->scoreboards[$player->getName()]);
    }

    public function addLine(
        Player $player,
        int $score,
        string $message
    ): void{
        $objectiveName = $this->getObjectiveName($player);

        if($objectiveName === null){
            Pixals::getPluginInstance()
                ->getLogger()
                ->error(
                    "Cannot set a score to a player with no scoreboard"
                );

            return;
        }

        $entry = new ScorePacketEntry();

        $entry->scoreboardId = $score;
        $entry->objectiveName = $objectiveName;
        $entry->score = $score;
        $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $entry->actorUniqueId = null;
        $entry->customName = $message;

        $player->getNetworkSession()->sendDataPacket(
            SetScorePacket::create([$entry])
        );
    }

    public function PixalsHubScoreboard(Player $player): void{
        $session = (new SessionManager)->getSession($player);

        $everything = new Everything();
        $pt = $everything->formatTime($session);

        $this->newScoreboard(
            $player,
            "pixalshub",
            "pixals.hub.logo"
        );

        $serverName = Pixals::getPluginInstance()
            ->stargate
            ->getDefaultClient()
            ->getClientName();

        $this->addLine($player, 16, "§8");
        $this->addLine($player, 15, " ");
        $this->addLine($player, 14, "");
        $this->addLine(
            $player,
            13,
            " Rank§7: " . $session->getRank()
        );
        $this->addLine(
            $player,
            12,
            " Tokens: §e" . $session->getUCoins()
        );
        $this->addLine(
            $player,
            11,
            " Online Time: §7" . $pt
        );
        $this->addLine($player, 10, "   ");
        $this->addLine($player, 9, "");
        $this->addLine(
            $player,
            8,
            " Server§7: §a" . $serverName
        );
        $this->addLine($player, 7, "§8");
        $this->addLine(
            $player,
            6,
            "  §7play.pixalsnetwork.net  "
        );
    }

    public function reload(Player $player): void{
        $this->removeScoreboard($player);
        $this->PixalsHubScoreboard($player);
    }
}