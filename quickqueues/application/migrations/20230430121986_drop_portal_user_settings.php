<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Drop_portal_user_settings extends CI_Migration {

    public function up()
    {
        $this->down();

    }

    public function down()
    {
        $this->dbforge->drop_table('qq_portal_user_settings');

    }

}
