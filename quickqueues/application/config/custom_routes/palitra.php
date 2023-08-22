<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['agent_crm/(.+)'] = 'agent_crm_palitra/$1';
$route['workspace/admin'] = 'workspace/admin_palitra';
$route['recordings'] = 'recordings/index_palitra';
$route['export/recordings'] = 'export/recordings_palitra';

