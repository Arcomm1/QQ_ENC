<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_obsolete_tables extends CI_Migration {

    public function up()
    {
        $tables = [
            "qq_tickets",
            "qq_system_stats",
            "qq_ticket_categories",
            "qq_ticket_comments",
            "qq_ticket_departments",
            "qq_ticket_subcategories",
            "qq_services",
            "qq_service_products",
            "qq_service_product_types",
            "qq_service_product_subtypes",
            "qq_notifications",
            "qq_news",
            "qq_custom_time_intervals",
            "qq_campaigns",
            "qq_call_categories",
            "qq_call_subcategories",
            "qq_call_tags",
        ];

        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->dbforge->drop_table($table, TRUE);
            }
        }
    }

    public function down()
    {
        // Since dropping tables is irreversible, returning true to indicate no rollback action is defined
        return true;
    }
}
