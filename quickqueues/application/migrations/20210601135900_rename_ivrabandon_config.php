<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_rename_ivrabandon_config extends CI_Migration {

    public function up()
    {
        $this->db->query("UPDATE qq_config SET name = 'app_track_ivrabandon' WHERE name = 'app_track_ivr_abandon'");
    }

    public function down()
    {
        $this->db->query("UPDATE qq_config SET name = 'app_track_ivr_abandon' WHERE name = 'app_track_ivrabandon'");
    }

}
