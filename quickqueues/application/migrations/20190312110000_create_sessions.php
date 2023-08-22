<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_create_sessions extends CI_Migration {


    public function up()
    {
        $query  = "CREATE TABLE IF NOT EXISTS `qq_sessions` (";
        $query .= "`id` varchar(128) NOT NULL,";
        $query .= "`ip_address` varchar(45) NOT NULL,";
        $query .= "`timestamp` int(10) unsigned DEFAULT 0 NOT NULL,";
        $query .= "`data` blob NOT NULL,";
        $query .= "KEY `qq_sessions_timestamp` (`timestamp`) );";

        $this->db->query($query);
    }

    public function down()
    {
        $this->dbforge->drop_table('qq_sessions');
    }

}
