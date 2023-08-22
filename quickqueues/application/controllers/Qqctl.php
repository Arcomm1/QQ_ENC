<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/* Qqctl.php - Command line utilities for Quickqueues */


Class Qqctl extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Run migrations
     */
    public function migrate()
    {
        echo "Are you sure? This action can not be easily reverted! Type 'Y' con CONTINUE, or any other key to CANCEL\n";
        $input = readline("Continue? ");
        if ($input != 'Y') {
            echo "Migration canceled\n";
            exit();
        }
        echo "Performing migration...\n";
        sleep(1);
        $this->load->library('migration');
        if ($this->migration->current() === false) {
            echo $this->migration->error_string();
        }
        echo "\nMigration complere!\n";
    }


    /**
     * Show version
     */
    public function version()
    {
        echo "Quickqueues version: ".get_qq_version()."\n";
    }


    /**
     * Register product
     */
    public function register() {
        echo "Performing product registration\n";
        sleep(2);
        $this->load->model('Bond_model');
        $agents=$this->data['agents']  = $this->Bond_model->countAgents();
        $queues=$this->data['queues']  = $this->Bond_model->countQueues();
        $users=$this->data['users']  = $this->Bond_model->countUsers();
        $qq_version = qq_get_version();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, QQ_REGISTRATION_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(array( 'agents'=>$agents,
                                    'queues'=>$queues,
                                    'users'=>$users,
                                    'qq_version'=>$qq_version)));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        if ($server_output == "OK") {
             echo "Product registered succesfully!\n";
        }
        else {
            echo "Could not register product!\n";
        }
    }

}
