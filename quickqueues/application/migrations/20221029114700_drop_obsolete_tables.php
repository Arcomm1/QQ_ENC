<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_drop_obsolete_tables extends CI_Migration {


    public function up()
    {
        $this->dbforge->drop_table("qq_tickets");
        $this->dbforge->drop_table("qq_system_stats");
        $this->dbforge->drop_table("qq_ticket_categories");
        $this->dbforge->drop_table("qq_ticket_comments");
        $this->dbforge->drop_table("qq_ticket_departments");
        $this->dbforge->drop_table("qq_ticket_subcategories");
        $this->dbforge->drop_table("qq_services");
        $this->dbforge->drop_table("qq_service_products");
        $this->dbforge->drop_table("qq_service_product_types");
        $this->dbforge->drop_table("qq_service_product_subtypes");
        $this->dbforge->drop_table("qq_notifications");
        $this->dbforge->drop_table("qq_news");
        $this->dbforge->drop_table("qq_custom_time_intervals");
        $this->dbforge->drop_table("qq_campaigns");
        $this->dbforge->drop_table("qq_call_categories");
        $this->dbforge->drop_table("qq_call_subcategories");
        $this->dbforge->drop_table("qq_call_tags");
    }

    public function down()
    {
        return true;
    }

}
