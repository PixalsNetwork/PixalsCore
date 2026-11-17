<?php

namespace PixalsCore\PixalsQueries;

final class Queries {

    public const get_player_data = "SELECT * FROM PlayersData WHERE player_uuid = ?;"; 
    public const get_rank_data = "SELECT * FROM Ranks WHERE rank_name = ?;";
    public const get_rank_permissions = "SELECT * FROM Permissions WHERE rank_name = ?;";
    public const get_player_permissions = "SELECT * FROM PlayerPermissions WHERE player_name = ?;";
    public const get_player_cape = "SELECT * FROM CapesUsed WHERE player_uuid = ?;";
    public const update_player_name = "UPDATE PlayersData SET player_name = ? WHERE player_uuid = ?;";
    public const update_player_cape = "UPDATE CapesUsed SET used_cape = ? WHERE player_uuid = ?;";
    public const new_player_data = "INSERT INTO PlayersData VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    public const new_player_cape = "INSERT INTO CapesUsed VALUES(?, ?);";
    public const set_player_face = "INSERT INTO PlayersFaces VALUES(?, ?);";
    public const get_player_tags = "SELECT * FROM TagsOwned WHERE player_uuid = ?;";
    public const get_tag_prefix = "SELECT * FROM Tags WHERE tag_name = ?";
    public const set_used_tag = "INSERT INTO TagsUsed VALUES(?, ?)";
    public const update_used_tag = "UPDATE TagsUsed SET tag_name = ? WHERE player_uuid = ?";
    public const get_used_tag = "SELECT * FROM TagsUsed WHERE player_uuid = ?";
    public const get_f_requests = "SELECT * FROM FriendsRequests WHERE added_uuid = ?";
    public const get_f = "SELECT * FROM FriendsData WHERE player_uuid = ?;";
    public const get_clan_data = "SELECT * FROM ClansData WHERE clan_name = ?";
    public const edit_notifier_time = "UPDATE PlayersData SET inbox_messages_period = ? WHERE player_uuid = ?";
    public const get_player_level = "SELECT * FROM LevelSystem WHERE player_uuid = ?";
    public const save_player_level = "UPDATE LevelSystem SET player_experience_points = ? WHERE player_name = ?";
    public const create_player_level = "INSERT INTO LevelSystem VALUES(?,?,?) ON DUPLICATE KEY UPDATE player_name = ?";
    public const get_kills_leaderboard = "SELECT player_name, SUM(player_kills) AS total_kills FROM ( SELECT player_name, player_kills FROM PixalsFFA UNION ALL SELECT player_name, kills FROM DuelsEngineData ) AS combined GROUP BY player_name ORDER BY total_kills DESC LIMIT 15;";
    
    public const get_deaths_leaderboard = "SELECT player_name, SUM(player_deaths) AS total_deaths
                                           FROM (
                                                SELECT player_name, player_deaths FROM PixalsFFA
                                                UNION ALL
                                                SELECT player_name, deaths FROM DuelsEngineData
                                            ) AS combined
                                            GROUP BY player_name
                                            ORDER BY total_deaths DESC LIMIT 15;";

    public const get_elo_leaderboard = "SELECT player_name, elo FROM DuelsEngineData ORDER BY elo DESC LIMIT 15;";
    public const get_thebridge_data = "SELECT player_name, player_wins FROM TheBridgeData ORDER BY player_wins DESC LIMIT 15;";

    public const refresh_db = "SELECT ?";
}