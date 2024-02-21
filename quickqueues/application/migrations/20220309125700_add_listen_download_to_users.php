<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_listen_download_to_users extends CI_Migration {

    public function up()
    {
        if (!$this->db->field_exists('can_listen', 'qq_users')) {
            $this->dbforge->add_column('qq_users', [
                'can_listen' => [
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'default' => 'yes',
                ]
            ]);
        }

        if (!$this->db->field_exists('can_download', 'qq_users')) {
            $this->dbforge->add_column('qq_users', [
                'can_download' => [
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'default' => 'yes',
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('can_listen', 'qq_users')) {
            $this->dbforge->drop_column('qq_users', 'can_listen');
        }

        if ($this->db->field_exists('can_download', 'qq_users')) {
            $this->dbforge->drop_column('qq_users', 'can_download');
        }
    }
}
