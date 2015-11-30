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
class Response extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function report($response_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $_SESSION['user_id']);
        if ($permission === false) {
            redirect('dashboard');
            return;
        }
        $response_data = $this->Response_model->get();
        $this->Response_model->close();
        $page_data = array('response_data' => $response_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/response_tracking_report');
    }

    public function view($response_id = null) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($response_id == null) {
            redirect('dashboard');
            return;
        }
        $this->load->model('Response_model');
        $user_id = $_SESSION['user_id'];
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission === false) {
            redirect('dashboard');
        }
        $super = $permission == "Owner" || $permission == "Admin";
        $modify = $permission == "Write" || $super;
        $response_data = $this->Response_model->get();
        if ($this->Project_model->initialize($response_data['project_id'], $user_id) === false) {
            redirect('dashboard');
            return;
        }
        $page_data = array('title' => 'Response Planning',
            'response_data' => $response_data, 'modify' => $modify,
            'super' => $super);
        $this->load->view('templates/header', $page_data);
        $this->load->view('project/view_response');
        $this->load->view('templates/footer');
    }

    function edit($response_id = null) {
        if ($response_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        //lookup response id and permission
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission !== "Write" && $permission !== "Owner" && $permission !== "Admin") {
            redirect('dashboard');
        }
        $response_data = $this->Response_model->get();
        $this->Response_model->close();
        $this->load->model('Risk_model');
        if (!$this->Risk_model->initialize($response_data['risk_id'], $user_id))
            redirect('dashboard');
        $risk_data = $this->Risk_model->get('risk_statement, cost_impact');
        $this->load->helper(array('form'));
        $_SESSION['risk_data'] = $risk_data; //store risk data in session in case form must be reloaded
        $data = array('title' => 'Edit Response',
            'response_data' => $response_data, 'risk_data' => $risk_data,
            'edit_mode' => true);
        $this->load->view('templates/header', $data);
        $this->load->view('project/response_wizard');
        $this->load->view('templates/footer');
    }

    function edit_form($response_id = null) {
        if ($response_id == null) {
            redirect('dashboard');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('action_plan', 'Action Plan', 'trim|required|xss_clean');
        $this->form_validation->set_rules('rng_action', 'Action', 'trim|required|xss_clean');
        $this->form_validation->set_rules('owner', 'Owner', 'trim|xss_clean');
        $this->form_validation->set_rules('cost', 'Cost', 'trim|xss_clean|required');
        $this->form_validation->set_rules('post_response', 'Post Response $', 'trim|xss_clean|required');
        $this->form_validation->set_rules('date_of_plan', 'Date of Plan', 'trim|xss_clean');
        $this->form_validation->set_rules('release_progress', 'Release Progress', 'trim|required|xss_clean');
        $this->form_validation->set_rules('planned_closure', 'Planned Closure', 'trim|required|xss_clean');
        $this->form_validation->set_rules('current_status', 'Current Status', 'trim|xss_clean|callback_edit_response[' . $response_id . ']');
        if ($this->form_validation->run() == false) {
            $data = array('title' => 'Edit Response',
                'risk_data' => $_SESSION['risk_data'],
                'edit_mode' => true,
                'response_data' => $this->get_response_wizard_form_data());
            $data['response_data']['response_id'] = $response_id;
            $this->load->view('templates/header', $data);
            $this->load->view('project/response_wizard');
            $this->load->view('templates/footer');
        } else {
            unset($_SESSION['risk_data']);
            redirect('response/view/' . $response_id);
        }
    }

    private function get_response_wizard_form_data() {
        $data = array('action_plan' => $this->input->post('action_plan'),
            'owner' => $this->input->post('owner'),
            'date_of_plan' => $this->input->post('date_of_plan'),
            'release_progress' => $this->input->post('release_progress'),
            'planned_closure' => $this->input->post('planned_closure'),
            'current_status' => $this->input->post('current_status'),
            'cost' => $this->input->post('cost'),
            'post_response' => $this->input->post('post_response'));
        if ($data['release_progress'] != "Ongoing" &&
                $data['release_progress'] != "Planning" &&
                $data['release_progress'] != "Complete" &&
                $data['release_progress'] != "Cancelled") {
            return false;
        }
        if (!isset($data['date_of_plan'])) {
            $data['date_of_plan'] = date("Y-m-d");
        }
        $action_index = $this->input->post('rng_action');
        $data['action'] = null;
        switch ($action_index) {
            case 0:
                $data['action'] = "Pursue";
                break;
            case 1:
                $data['action'] = "Accept";
                break;
            case 2:
                $data['action'] = "Mitigate";
                break;
            case 3:
                $data['action'] = "Transfer";
                break;
            case 4:
                $data['action'] = "Avoid";
                break;
        }
        return $data;
    }

    function edit_response($current_status, $response_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $data = $this->get_response_wizard_form_data();
        if ($data === false)
            return false;
        $data['response_id'] = $response_id;
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if (!$permission) {
            $this->form_validation->set_message('edit_response', 'You do not have permission to access this resource.');
            $this->Response_model->close();
            return false;
        }
        if ($this->Response_model->edit($data) === false) {
            $this->form_validation->set_message('edit_response', 'You do not have permission to access this resource.');
            $this->Response_model->close();
            return false;
        }
        $this->Response_model->close();
        return true;
    }

    function create($risk_id = null) {
        if ($risk_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        if ($this->Risk_model->initialize($risk_id, $user_id) === false) {
            redirect('dashboard');
        }
        $risk_data = $this->Risk_model->get('risk_statement, task_id, project_id, task_name, project_name, cost_impact');
        $risk_data['risk_id'] = $risk_id;
        $this->load->helper(array('form'));
        $_SESSION['risk_data'] = $risk_data; //store risk data in session in case form must be reloaded
        $data = array('title' => 'New Response',
            'risk_data' => $risk_data,
            'edit_mode' => false);
        $this->load->view('templates/header', $data);
        $this->load->view('project/response_wizard');
        $this->load->view('templates/footer');
    }

    function create_form($risk_id = null) {
        if ($risk_id == null) {
            redirect('dashboard');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('action_plan', 'Action Plan', 'trim|required|xss_clean');
        $this->form_validation->set_rules('rng_action', 'Action', 'trim|required|xss_clean');
        $this->form_validation->set_rules('owner', 'Owner', 'trim|xss_clean');
        $this->form_validation->set_rules('cost', 'Cost', 'trim|xss_clean|required');
        $this->form_validation->set_rules('post_response', 'Post Response $', 'trim|xss_clean|required');
        $this->form_validation->set_rules('date_of_plan', 'Date of Plan', 'trim|xss_clean');
        $this->form_validation->set_rules('release_progress', 'Release Progress', 'trim|required|xss_clean');
        $this->form_validation->set_rules('planned_closure', 'Planned Closure', 'trim|required|xss_clean');
        $this->form_validation->set_rules('current_status', 'Current Status', 'trim|xss_clean|callback_insert_response[' . $risk_id . ']');
        if ($this->form_validation->run() == false) {
            $data = array('title' => 'Create Response',
                'risk_data' => $_SESSION['risk_data'],
                'edit_mode' => false,
                'response_data' => $this->get_response_wizard_form_data());
            $this->load->view('templates/header', $data);
            $this->load->view('project/response_wizard');
            $this->load->view('templates/footer');
        } else {
            redirect('risk/view/' . $risk_id);
        }
    }

    function insert_response($current_status, $risk_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $data = $this->get_response_wizard_form_data();
        $data['risk_id'] = $risk_id;
        if ($data === false)
            return false;
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        if ($this->Response_model->create($data, $user_id) === false) {
            return false;
        } else {
            return true;
        }
    }

    function update($response_id = null) {
        if ($response_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        if ($this->Response_model->initialize($response_id, $user_id) === false) {
            redirect('dashboard');
        }
        $response_data = $this->Response_model->get();
        $this->load->helper(array('form'));
        $data = array('title' => 'Response Update', 'response_data' => $response_data);
        $this->load->view('templates/header', $data);
        $this->load->view('project/response_update');
        $this->load->view('templates/footer');
    }

    function update_form($response_id = null) {
        if ($response_id == null) {
            redirect('dashboard');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('owner', 'Owner', 'trim|xss_clean');
        $this->form_validation->set_rules('cost', 'Cost', 'trim|xss_clean|required');
        $this->form_validation->set_rules('post_response', 'Post Response $', 'trim|xss_clean|required');
        $this->form_validation->set_rules('release_progress', 'Release Progress', 'trim|required|xss_clean');
        $this->form_validation->set_rules('planned_closure', 'Planned Closure', 'trim|xss_clean');
        $this->form_validation->set_rules('current_status', 'Current Status', 'trim|xss_clean|callback_insert_update[' . $response_id . ']');
        if ($this->form_validation->run() == false) {
            redirect('response/update/' . $response_id);
        } else {
            redirect('response/view/' . $response_id);
        }
    }

    function insert_update($current_status, $response_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $owner = $this->input->post('owner');
        $release_progress = $this->input->post('release_progress');
        $planned_closure = $this->input->post('planned_closure');
        $cost = $this->input->post('cost');
        $post_response = $this->input->post('post_response');
        if ($release_progress != "Ongoing" &&
                $release_progress != "Planning" &&
                $release_progress != "Complete" &&
                $release_progress != "Cancelled") {
            return false;
        }
        $data = array('response' => $response_id, 'owner' => $owner,
            'release_progress' => $release_progress,
            'planned_closure' => $planned_closure, 'current_status' =>
            $current_status, 'cost' => $cost, 'post_response' => $post_response);
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission !== "Owner" && $permission !== "Admin" && $permission !== "Write") {
            return false;
        }
        if ($this->Response_model->insert_update($data) === false) {
            return false;
        } else {
            return true;
        }
    }

}