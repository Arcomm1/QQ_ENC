<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/** Queue_model.php - Quickqueues Queue abstration */


class Queue_model extends MY_Model {


    public function __construct()
    {
        $this->_table_prefix = 'qq_';
        $this->_soft_delete = true;
        parent::__construct();

        $this->_queue_names = 'qq_queues';
        $this->_queue_agents_table = 'qq_queue_agents';
        $this->_queue_config_table = 'qq_queue_config';
        $this->_freepbx_queue_config_table = 'queues_config';
        $this->_freepbx_queue_details_table = 'queues_details';
        $this->_freepbx_devices_table = 'devices';


        $this->pbx_bridge_url = 'http://localhost/pbx-bridge/queues/';

    }


    /**
     * Add agent to the queue
     *
     * @param int $id Queue ID
     * @param in $agent_id Agent ID
     * @return bool True or false based on whether queue was added to queue or not
     */
    public function add_agent($id = false, $agent_id = false)
    {
        if (!$id || !$agent_id) {
            return false;
        }
        $q = 'INSERT IGNORE INTO '.$this->_queue_agents_table.' (queue_id, agent_id) ';
        $q .= 'VALUES('.$id.','.$agent_id.');';
        $this->db->query($q);
        return true;
    }


    /**
     * Remove agent from the queue
     *
     * @param int $id Queue ID
     * @param in $agent_id Agent ID
     * @return bool True or false based on whether queue was added to queue or not
     */
    public function remove_agent($id = false, $agent_id = false)
    {
        if (!$id || !$agent_id) {
            return false;
        }
        $this->db->where('queue_id', $id);
        $this->db->where('agent_id', $agent_id);
        $this->db->delete($this->_queue_agents_table);
        return true;
    }


    /**
     * Get agents belonging to specific queue
     *
     * @param int $id Queue ID
     * @return array List of agents
     */
    public function get_agents($id)
    {
        $agents = array();
        if (!isset($agents)) {
            return $agents;
        }
        foreach ($this->db->get_where($this->_queue_agents_table, array('queue_id' => $id))->result() as $agent_id) {
            $agents[$agent_id->agent_id] = $this->Agent_model->get($agent_id->agent_id);
        }
        return $agents;
    }


    /**
     * Get queue configuration
     *
     * @param int $id Queue ID
     * @param string $name Configuration name
     * @return mixed List of configuration values or false on error
     */
    public function get_config($id = false, $name = false)
    {
        if (!$id) {
            return false;
        }

        if ($name) {
            $this->db->where('name', $name);
        }

        $this->db->where('queue_id', $id);

        $config = $this->db->get($this->_queue_config_table)->result();

        if (count($config) == 0) {
            // Queue has no configuration, get default config

            $config = $this->Config_model->get_many_by('category', 'queue');

            foreach ($config as $c) {
                $this->db->insert($this->_queue_config_table, array(
                    'queue_id'  => $id,
                    'name'      => $c->name,
                    'value'     => $c->value,
                    'default'   => $c->default
                ));
            }

        }

        $r = array();
        foreach ($config as $c) {
            $r[$c->name] = $c;
        }

        return $r;

    }


    /**
     * Get queue configuration
     *
     * @param int $id Queue ID
     * @param string $name Configuration item name
     * @param string $value Configuration item value
     *
     * @return mixed List of configuration values or false on error
     */
    public function set_config($id = false, $name = false, $value = false)
    {
        if (!$id || !$name || !$value) {
            return false;
        }

        $this->db->where('queue_id', $id);
        $this->db->where('name', $name);
        $this->db->update($this->_queue_config_table, array('value' => $value));

        return true;
    }


    /**
     * Get FreePBX queues and ingest them if they don't exist
     *
     * @param void
     * @return bool Always TRUE
     */
    public function ingest_freepbx_queues()
    {
        $freepbx_queues = $this->db->get($this->_freepbx_queue_config_table)->result();

        foreach ($freepbx_queues as $fq) {
            if ($this->get_by('name', $fq->extension)) {
                log_to_file('NOTICE', 'Queue_model->ingset_freepbx_queues: Not creating queue '.$fq->extension.' since it already exists');
            } else {
                log_to_file('NOTICE', 'Queue_model->ingset_freepbx_queues: Creating queue '.$fq->extension);
                $this->create(
                    array(
                        'name' => $fq->extension,
                        'display_name' => $fq->extension,
                    )
                );
            }
        }
        return true;
    }


    /**
     * Get agents based on FreePBX, not based on QQ relations
     *
     * @param bool $id Queue ID
     * @return mixed False on error, array of agents on success
     */
    public function get_freepbx_agents($id = false)
    {
        if (!$id) {
            return false;
        }

        $queue = $this->get($id);

        if (!$queue) {
            return false;
        }

        $this->db->where('keyword', 'member');
        $this->db->where('id', $queue->name);
        $freepbx_members = $this->db->get($this->_freepbx_queue_details_table)->result();

        $agents = array();

        foreach ($freepbx_members as $fm) {
            if (substr($fm->data, 0, 6) != 'Local/') {
                continue;
            }
            $num = explode('@', $fm->data);
            $num = explode('/', $num[0]);
            $num = $num[1];

            $agent = $this->Agent_model->get_by('extension', $num);
            if ($agent) {
                $agents[$agent->id] = $agent;
            }
            unset($num);
            unset($agent);
        }

        return $agents;
    }


    /**
     * Get agents based on FreePBX, not based on QQ relations
     *
     * @return mixed False on error, array of agents on success
     */
    public function get_all_freepbx_agents($id = false)
    {
        $agents = array();

        $this->db->where('keyword', 'member');
        $freepbx_members = $this->db->get($this->_freepbx_queue_details_table)->result();

        $temp = array();
        foreach ($freepbx_members as $fm) {
            if (!in_array($fm->data, $temp)) {
                $temp[] = $fm->data;
            }
        }

        foreach ($temp as $t) {
            if (substr($t, 0, 6) != 'Local/') {
                continue;
            }
            $num = explode('@', $t);
            $num = explode('/', $num[0]);
            $num = $num[1];

            $agent = $this->Agent_model->get_by('extension', $num);
            if ($agent) {
                $agents[] = $agent;
            }
            unset($num);
            unset($agent);
        }

        return $agents;
    }

    public function get_queue_entries()
    {
        $this->db->select('id');
        $this->db->select('name');
        $this->db->select('display_name');
        return $this->db->get($this->_table)->result_array();
    }
}
