<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_agent_config_per_agent extends CI_Migration {


    public function up()
    {
        foreach ($this->Agent_model->get_all() as $a) {
            $this->Agent_model->set_default_settings($a->id);
        }

    }


    public function down()
    {
        return true;
    }

}
