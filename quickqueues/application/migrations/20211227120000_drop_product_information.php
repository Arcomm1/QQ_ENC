<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_drop_product_information extends CI_Migration {


    public function up()
    {
        $this->dbforge->drop_table('qq_product_information',TRUE);
    }


    public function down()
    {
        return true;
    }

}
