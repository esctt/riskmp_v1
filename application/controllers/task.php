<?php
/*
 * Copyright (c) 2014 ESCTT Inc. All Right Reserved, http://esctt.com/
 * 
 * This source is subject to the ESCTT Inc. Permissive License.
 * All other rights reserved.
 * 
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 * 
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Task extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function view($task_id = null) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($task_id == null) {
            redirect('dashboard');
        } else {
            $this->load->model('Task_model');
            $user_id = $_SESSION['user_id'];
            $permission = $this->Task_model->initialize($task_id, $user_id);
            if ($permission === false) {
                redirect('dashboard');
                return;
            }
            $super = $permission == "Owner" || $permission == "Admin";
            $modify = $permission == "Write" || $super;
            $task_data = $this->Task_model->get();
            $page_data = array('title' => $task_data['task_name'],
                'task_data' => $task_data, 'permission' => $permission,
                'super' => $super, 'modify' => $modify);
            $this->load->view('templates/header', $page_data);
            $this->load->view('project/view_task');
            $this->load->view('templates/footer');
        }
    }

}