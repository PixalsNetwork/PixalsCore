<?php

namespace PixalsCore\PixalsManagers\CoreManagers;

use Closure;
use DatabaseManager\PixalsLibs\DatabaseManager;
use PixalsCore\Pixals;
use PixalsCore\PixalsPlayer\PlayerDataLoader;
use PixalsCore\PixalsPlayer\PPlayer;
use PixalsCore\PixalsQueries\Queries;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class QueriesManager {
    
    public const PLAYER_RANK = "player_rank";
    public const PLAYER_CLAN = "player_clan";
    public const PLAYER_UCOINS = "player_ucoins";
    public const PLAYER_NET_LEVEL = "player_networklevel";
    public const PLAYER_PSC = "player_psc";
    public const PLAYER_CHAT_LEVEL = "chat_level";
    public const PLAYER_LINK_STATUS = "linkstatus";

    public const RANK_PREFIX = "rank_prefix";
    public const RANK_DISCORD_ID = "rank_discord_id";
    public const RANK_COLOR = "rank_color";
    public const RANK_CHAT = "rank_chat";

    public const TAG_NAME = "tag_name";
    public const TAG_PREFIX = "tag_prefix";
    public const ONLINE_TIME = "online_time";

    public const BASIC_VALUES = [
        "player_rank" => "Member",
        "player_ucoins" => 20,
        "player_networklevel" => 1,
        "chat_level" => 1,
        "linkstatus" => 0,
        "player_clan" => "None",
        "accept_clan_invites" => 1,
        "accept_friends_requests" => 1,
        "accept_msg" => 1,
        "messages_no" => 0,
        "inbox_messages_period" => "5 min",
        "playing_time" => 0
    ];

    public function getPlayerData(Player $player, Closure $onSuccess) {
        $queries_manager = (new DatabaseManager)->getQueriesManager();
        $queries = new Queries;
        
        return $queries_manager->fetchQuery($queries::get_player_data, 
            [
                "type" => "fetch"
            ], 
            [
                ":player_uuid" => $player->getUniqueId()->toString()
            ], 
            $onSuccess,
        );

    }

    public function getRankData(String $rank_name, Closure $onSuccess, Closure $onFail) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_rank_data,
            [
                "type" => "fetch"
            ],
            [
                ":rank_name" => $rank_name
            ],
            $onSuccess,
            $onFail
        );

        return $fetcher;
    }

    public function getRankPermissions(String $rank_name, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_rank_permissions, 
            [
                "type" => "fetch"
            ],
            [
                ":rank_name" => $rank_name
            ],
            $onSuccess
        );
        return $fetcher;
    }

    public function getPlayerPermissions(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_player_permissions, 
            [
                "type" => "fetch"
            ],
            [
                ":player_name" => $player->getName()
            ],
            $onSuccess
        );

        return $fetcher;
    }

    public function getPlayerCape(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_player_cape, 
            [
                "type" => "fetch"
            ],
            [
                ":player_uuid" => $player->getUniqueId()->toString()
            ],
            $onSuccess
        );

        return $fetcher;
    }

    public function setupPlayerData(Player $player) : void {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        Await::f2c(function() use ($player, $queries, $queries_manager){
            $pdata = yield from Await::promise(fn($accept, $error) => $this->getPlayerData($player, $accept, $error));
            if($pdata == null) {
                $queries_manager->executeQuery($queries::new_player_data,
                    [
                        "type" => "execute"
                    ],
                    [
                        ":player_uuid" => $player->getUniqueId()->toString(),
                        ":player_rank" => self::BASIC_VALUES["player_rank"],
                        ":player_ucoins" => self::BASIC_VALUES["player_ucoins"],
                        ":player_networklevel" => self::BASIC_VALUES["player_networklevel"],
                        ":player_psc" => Pixals::getPluginInstance()->GeneratePixalsSecurityAuthCode(),
                        ":chat_level" => self::BASIC_VALUES["chat_level"],
                        ":linkstatus" => self::BASIC_VALUES["linkstatus"],
                        ":player_name" => $player->getName(),
                        ":player_clan" => self::BASIC_VALUES["player_clan"],
                        ":accept_clan_invites" => self::BASIC_VALUES["accept_clan_invites"],
                        ":accept_friends_requests" => self::BASIC_VALUES["accept_friends_requests"],
                        ":accept_msg" => self::BASIC_VALUES["accept_msg"],
                        ":messages_no" => self::BASIC_VALUES["messages_no"],
                        ":inbox_messages_period" => self::BASIC_VALUES["inbox_messages_period"],
                        ":playing_time" => self::BASIC_VALUES["playing_time"]
                    ]   
                );
                $this->setupCape($player);

            } else {
                // var_dump($pdata);
                if($pdata[0]["player_name"] != $player->getName()) {
                    $queries_manager->executeQuery($queries::update_player_name,
                        [
                            "type" => "execute"
                        ],
                        [
                            ":player_name" => $player->getName(),
                            ":player_uuid" => $player->getUniqueId()
                        ]
                    );
                }
            }
            $queries_manager->executeQuery($queries::create_player_level, ["type" => "execute"], ["player_uuid" => $player->getUniqueId()->toString(), "player_name" => $player->getName(), "level" => 50, "player_updated_name" => $player->getName()]);

            (new PPlayer)->loadData($player);
            // The Error Was that Atlas Deletes the Query Before it's being executed.
        });
    }



    public function editCapeUsed(Player $player, String $cape_name) : void {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries_manager->executeQuery($queries::update_player_cape, [
            "type" => "execute"
        ], [
            ":cape_name" => $cape_name,
            ":player_uuid" => $player->getUniqueId()->toString()
        ]);
    }

    public function setupCape(Player $player) : void {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries_manager->executeQuery(
            $queries::new_player_cape,
            [
                "type" => "execute"
            ],
            [
                ":player_uuid" => $player->getUniqueId()->toString(),
                ":used_cape" => "none"
            ]   
        );
    }

    public function setPlayerFace(Player $player, String $path) : void {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();

        $queries_manager->executeQuery(
            $queries::set_player_face,
            [
                "type" => "execute"
            ],
            [
                ":player_uuid" => $player->getUniqueId()->toString(),
                ":path" => $path
            ]
        );
    }

    public function getPlayerOwnedTags(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_player_tags, 
            [
                "type" => "fetch"
            ],
            [
                ":player_uuid" => $player->getUniqueId()->toString()
            ],
            $onSuccess
        );

        return $fetcher;
    }


    public function getTagPrefix(String $tag_name, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_tag_prefix, 
            [
                "type" => "fetch"
            ],
            [
                ":tag_name" => $tag_name
            ],
            $onSuccess
        );

        return $fetcher;
    }

    public function getTagUsed(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        $fetcher = $queries_manager->fetchQuery($queries::get_used_tag, 
            [
                "type" => "fetch"
            ],
            [
                ":player_uuid" => $player->getUniqueId()->toString()
            ],
            $onSuccess
        );

        return $fetcher;
    }

    public function changeTagUsed(String $tag_name, Player $player) : void {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        Await::f2c(function() use ($queries, $queries_manager, $player, $tag_name){
            $tag_used = yield from Await::promise(fn($accept) => $this->getTagUsed($player, $accept));
            if($tag_used == null) {
                $queries_manager->executeQuery($queries::set_used_tag, 
                    [
                        "type" => "execute"
                    ],
                    [
                        ":player_uuid" => $player->getUniqueId()->toString(),
                        ":tag_name" => $tag_name
                    ]
                );
            } else {
                $queries_manager->executeQuery($queries::update_used_tag, 
                    [
                        "type" => "execute"
                    ],
                    [
                        ":tag_name" => $tag_name,
                        ":player_uuid" => $player->getUniqueId()->toString(),
                    ]
            )   ;
            }
        });
    }

    public function getFriendsRequests(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        return $queries_manager->fetchQuery($queries::get_f_requests, ["type" => "fetch"], [":added_uuid" => $player->getUniqueId()->toString()], $onSuccess);
    }


    public function getFriendsList(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;
        return $queries_manager->fetchQuery($queries::get_f, ["type" => "fetch"], [":player_uuid" => $player->getUniqueId()->toString()], $onSuccess);
    }

    public function getPlayerInboxTimer(Player $player, Closure $onSuccess) {
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        $queries = new Queries;

        return $queries_manager->fetchQuery($queries::get_player_data, ["type" => "fetch"], ["player_uuid" => $player->getUniqueId()->toString()], $onSuccess);
    }

    public function editPlayerInboxTimer(Player $player, String $duration) : void {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();

        $queries_manager->executeQuery($queries::edit_notifier_time, ["type" => "execute"], ["duration" => $duration, "player_uuid" => $player->getUniqueId()->toString()]);
    }
    
    public function getClanData(String $clan_name, Closure $onSuccess) { 
        $executor = Pixals::getPluginInstance()->getQueriesManager();
        return $executor->fetchQuery(Queries::get_clan_data, ["type" => "fetch"], ["clan_name" => $clan_name], $onSuccess);
    }

    public function getLevelData(Player $player, Closure $onSuccess) {
        return Pixals::getPluginInstance()->getQueriesManager()->fetchQuery(Queries::get_player_level, ["type" => "fetch"], ["player_uuid" => $player->getUniqueId()->toString()], $onSuccess);
    }

    public function getKillsLeaderboard(Closure $onSuccess, Closure $onFail) {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        return $queries_manager->fetchQuery($queries::get_kills_leaderboard, ["type" => "fetch_only"], null, $onSuccess, $onFail);
    }

    public function getDeathsLeaderboard(Closure $onSuccess, Closure $onFail) {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        return $queries_manager->fetchQuery($queries::get_deaths_leaderboard, ["type" => "fetch_only"], null, $onSuccess, $onFail);
    }

    public function getPointsLeaderboard(Closure $onSuccess) {
        $queries = new Queries;
        $queries_manager = Pixals::getPluginInstance()->getQueriesManager();
        return $queries_manager->fetchQuery($queries::get_elo_leaderboard, ["type" => "fetch_only"], null, $onSuccess);
    }
}