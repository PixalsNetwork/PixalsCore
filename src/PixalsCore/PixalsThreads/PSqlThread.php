<?php

namespace PixalsCore\PixalsThreads;

use pocketmine\thread\Thread;
use pocketmine\thread\log\ThreadSafeLogger;
use pocketmine\plugin\PluginBase;
use mysqli;

use PixalsCore\Pixals;
use PixalsCore\PixalsCoreStartup;
use pocketmine\thread\NonThreadSafeValue;

class PSqlThread extends Thread {

    private ThreadSafeLogger $logger;
    private NonThreadSafeValue $ip;
    private NonThreadSafeValue $username;
    private NonThreadSafeValue $password;
    private NonThreadSafeValue $db_name;


    public function __construct(String $ip, String $username, String $password, String $db_name)
    {
        $this->ip = new NonThreadSafeValue($ip);
        $this->username = new NonThreadSafeValue($username);
        $this->password = new NonThreadSafeValue($password);
        $this->db_name = new NonThreadSafeValue($db_name);
    }


    public function onRun() : void {
        

        $db = new mysqli($this->ip->deserialize(), $this->username->deserialize(), $this->password->deserialize());

        if($db->connect_error) { 
            die("Connection Failed : " . $db->connect_error);
            echo("§7| §3Connection Status §7- §cDisconnected §7|");
        }

        $db->query("USE " . $this->db_name->deserialize());

        // PlayersData Table
        $db->query("CREATE TABLE IF NOT EXISTS PlayersData (
            player_uuid VARCHAR(36) NOT NULL UNIQUE , player_rank VARCHAR(20) NOT NULL, player_ucoins INT, player_networklevel INT, player_psc VARCHAR(10) NOT NULL UNIQUE,
            chat_level INT, linkstatus BOOLEAN, player_name VARCHAR(36), player_clan VARCHAR(20), accept_clan_invites BOOLEAN , accept_friends_requests BOOLEAN NOT NULL, accept_msg BOOLEAN NOT NULL, messages_no INT, inbox_messages_period VARCHAR(10), online_time BIGINT,
            PRIMARY KEY (player_uuid)
        );");

        // Ranks Table
        $db->query("CREATE TABLE IF NOT EXISTS Ranks (
            rank_name VARCHAR(20) NOT NULL UNIQUE, rank_prefix VARCHAR(20) NOT NULL UNIQUE, rank_discord_id VARCHAR(20), rank_color VARCHAR(10), rank_chat VARCHAR(20)
        );");
        
        $db->query("CREATE TABLE IF NOT EXISTS PlayerPermissions (
            player_uuid VARCHAR(36) NOT NULL, player_name VARCHAR(32) NOT NULL, player_permission VARCHAR(50)
        );");

        // Permissions Table
        $db->query("CREATE TABLE IF NOT EXISTS Permissions(
            rank_name VARCHAR(20), rank_perms VARCHAR(36),
            FOREIGN KEY (rank_name) REFERENCES Ranks(rank_name)
        );");

        $db->query("CREATE TABLE IF NOT EXISTS PlayersFaces (
            player_uuid VARCHAR(36), image_path TEXT,
            FOREIGN KEY (player_uuid) REFERENCES PlayersData(player_uuid)
        );");

        // Discord Accounts Table
        $db->query("CREATE TABLE IF NOT EXISTS DiscordAccounts(
            discord_id VARCHAR(20), player_uuid VARCHAR(36),
            FOREIGN KEY (player_uuid) REFERENCES PlayersData(player_uuid)
        );");

        // Ban Status Table
        $db->query("CREATE TABLE IF NOT EXISTS Bans(
            player_uuid VARCHAR(36), player_name VARCHAR(36), player_ban_status BOOLEAN, player_banned_on TIMESTAMP, player_banned_until TIMESTAMP, player_banned_reason VARCHAR(20), player_banned_by VARCHAR(20), player_appeal_code VARCHAR(5), player_ban_type VARCHAR(15),
            FOREIGN KEY (player_uuid) REFERENCES PlayersData(player_uuid)
        );");

        // Mute Status Table
        $db->query("CREATE TABLE IF NOT EXISTS Mutes(
            player_uuid VARCHAR(36), player_name VARCHAR(36), player_mute_status BOOLEAN, player_muted_on TIMESTAMP, player_muted_until TIMESTAMP, player_muted_reason VARCHAR(20), player_muted_by VARCHAR(20), player_appeal_code VARCHAR(5), player_mute_type VARCHAR(15),
            FOREIGN KEY (player_uuid) REFERENCES PlayersData(player_uuid)
        );");

        // Clans Table
        $db->query("CREATE TABLE IF NOT EXISTS ClansData(
            clan_name VARCHAR(10), clan_tag VARCHAR(10), clan_leader_uuid VARCHAR(36),
            FOREIGN KEY (clan_leader_uuid) REFERENCES PlayersData(player_uuid),
            PRIMARY KEY (clan_name)
        );");
 
        // Clan Requests, members
        $db->query("CREATE TABLE IF NOT EXISTS ClanMembers(
            clan_name VARCHAR(10), clan_member_uuid VARCHAR(36),
            FOREIGN KEY (clan_name) REFERENCES ClansData(clan_name)
        );");

        $db->query("CREATE TABLE IF NOT EXISTS ClanRequests(
            clan_name VARCHAR(10), invitation_player_uuid VARCHAR(36),
            FOREIGN KEY (clan_name) REFERENCES ClansData(clan_name)
        );");

        // Friends Table
        $db->query("CREATE TABLE IF NOT EXISTS FriendsData(
            friend_name VARCHAR(10), player_uuid VARCHAR(36),
            FOREIGN KEY (player_uuid) REFERENCES PlayersData(player_uuid)
        );");

        // Friends Requests
        $db->query("CREATE TABLE IF NOT EXISTS FriendsRequests(
            adder_uuid VARCHAR(36), added_uuid VARCHAR(36), adder_name VARCHAR(36),
            FOREIGN KEY (adder_uuid) REFERENCES PlayersData(player_uuid)
        );");

        // Creating Cape-On Table ( as i can do it for proxy system , but this will be easier )
        $db->query("CREATE TABLE IF NOT EXISTS CapesUsed(
            player_uuid VARCHAR(36), used_cape VARCHAR(15)
        );");

        // Creating Tags Table
        $db->query("CREATE TABLE IF NOT EXISTS Tags(
            tag_name TEXT, tag_creator TEXT, tag_prefix TEXT
        )");
        
        $db->query("CREATE TABLE IF NOT EXISTS TagsOwned(
            player_uuid VARCHAR(36), tag_name TEXT
        )");

        $db->query("CREATE TABLE IF NOT EXISTS TagsUsed(
            player_uuid VARCHAR(36), tag_name TEXT
        )");

        $db->query("CREATE TABLE IF NOT EXISTS ProofIt(
            player_name VARCHAR(36), discord_id VARCHAR(20), staff_uuid VARCHAR(36), staff_rank VARCHAR(36), timestamp_logged TIMESTAMP, evidence_url TEXT, reason TEXT, punishment_type VARCHAR(36)
        );");
        
        $db->query("CREATE TABLE IF NOT EXISTS LevelSystem(
            player_uuid VARCHAR(36) PRIMARY KEY, player_name VARCHAR(36), player_experience_points INT
        )");

        $db->query("CREATE TABLE IF NOT EXISTS Reports(
            player_uuid VARCHAR(36) PRIMARY KEY, player_name VARCHAR(36), reported_uuid VARCHAR(36), reviewed BOOLEAN, staff_reviewed VARCHAR(36), reported_on TIMESTAMP
        )");


        $db->query("CREATE TABLE IF NOT EXISTS RaqmSystem(
            player_uuid VARCHAR(36), player_email VARCHAR(89)
        )");
  
  
        $db->query("CREATE TABLE IF NOT EXISTS RaqmCodes(
            player_uuid VARCHAR(36), raqm_code INT, expires_at TIMESTAMP
        )");

        // Creating Member Rank and Setting Permissions And Checking if Already There - صنع الرتب و وضع الصلاحيات لهم والتاكد منهم
        $result = $db->query("SELECT * FROM Ranks WHERE rank_name = 'Member' AND rank_prefix = '§7Member';");
        $result2 = $db->query("SELECT * FROM Permissions WHERE rank_name = 'Member';");
        if ($result->num_rows == 0) {                                                                                                         
            $db->query("INSERT INTO Ranks VALUES ('Member', '§7Member', '984400080717115392', '§7', '§7')"); 
        }

        if($result2->num_rows == 0) {
            $db->query("INSERT INTO Permissions VALUES ('Member', 'member.pixals');");
        }

        // Pixals::getPluginInstance()->getLogger()->info("§3PixalsCore - Connected To SQL");

    }    
    


}