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
class Risk extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function report($risk_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $_SESSION['user_id']);
        if ($permission === false) {
            redirect('dashboard');
            return;
        }
        $risk_data = $this->Risk_model->get();
        $this->Risk_model->close();
        $page_data = array('risk_data' => $risk_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/risk_analysis_report');
    }

    public function view($risk_id = null) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($risk_id == null) {
            redirect('dashboard');
            return;
        }
        $this->load->model('Risk_model');
        $user_id = $_SESSION['user_id'];
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission === false) {
            redirect('dashboard');
        }
        $super = $permission == "Owner" || $permission == "Admin";
        $modify = $permission == "Write" || $super;
        $risk_data = $this->Risk_model->get();
        //get updates
        $current_update = array('date_of_update' => $risk_data['date_of_update'],
            'impact' => $risk_data['impact'], 'probability' => $risk_data['probability'],
            'impact_effect' => $risk_data['impact_effect'], 'days_delay' => $risk_data['days_delay'], 'cost_impact' => $risk_data['cost_impact'],
            'overall_impact' => $risk_data['overall_impact'], 'expected_cost' => $risk_data['expected_cost'], 'expected_delay' => $risk_data['expected_delay'],
            'impact_discussion' => $risk_data['impact_discussion'], 'adjusted_cost' => $risk_data['adjusted_cost'],
            'priority_effect' => $risk_data['priority_effect'], 'priority_monetary' => $risk_data['priority_monetary'], 'priority_days' => $risk_data['priority_days']);
        unset($risk_data['date_of_update']);
        unset($risk_data['impact']);
        unset($risk_data['probability']);
        unset($risk_data['impact_effect']);
        unset($risk_data['days_delay']);
        unset($risk_data['cost_impact']);
        unset($risk_data['overall_impact']);
        unset($risk_data['expected_cost']);
        unset($risk_data['expected_delay']);
        unset($risk_data['impact_discussion']);
        // unset($risk_data['adjusted_cost']);
        unset($risk_data['priority_effect']);
        unset($risk_data['priority_monetary']);
        unset($risk_data['priority_days']);
        $risk_updates = $this->Risk_model->get_updates();
        $this->Risk_model->close();
        array_unshift($risk_updates, $current_update); //prepend current update to beginning of update array
        $risk_data['updates'] = $risk_updates; //add updates array to risk_data array
        $page_data = array('title' => 'Risk Analysis',
            'risk_data' => $risk_data, 'permission' => $permission,
                'modify' => $modify, 'super' => $super);
        $this->load->view('templates/header', $page_data);
        $this->load->view('project/view_risk');
        $this->load->view('templates/footer');
    }

    function edit($risk_id = null) {
        if ($risk_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        //lookup risk id and permission
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Write" && $permission != "Owner" && $permission != "Admin") {
            redirect('dashboard');
        }
        $risk_data = $this->Risk_model->get();
        $this->Risk_model->close();
        if ($risk_data['event'] == "Not identified") {
            $risk_data['event'] = "";
        }
        $this->load->model('Task_model');
        $this->Task_model->initialize($risk_data['task_id'], $user_id);
        $task_data = $this->Task_model->get();
        $this->Task_model->close();
        $this->load->helper(array('form'));
        $_SESSION['task_data'] = $task_data; //store task data in session in case form must be reloaded
        $data = array('title' => 'Edit Risk',
            'task_data' => $task_data,
            'risk_data' => $risk_data,
            'edit_mode' => true);
        $this->load->view('templates/header', $data);
        $this->load->view('project/risk_wizard');
        $this->load->view('templates/footer');
    }

    function edit_wizard_form($risk_id = null) {
        if ($risk_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('risk_type', 'Risk Type (start)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('event', 'Event (step 1)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('impact', 'Impact (step 1)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_of_concern', 'Date of Concern (step 1)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_impact_effect', 'Impact Effect (step 3)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_days_delay', 'Impact - Days Delay (step 3)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_cost_impact', 'Cost Impact (step 3)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_probability', 'Probability (step 4)', 'trim|xss_clean');
        $this->form_validation->set_rules('impact_discussion', 'Impact Discussion (step 6)', 'trim|xss_clean|callback_edit_risk[' . $risk_id . ']');
        if ($this->form_validation->run() == false) {
            $data = array('title' => 'Edit Risk',
                'task_data' => $_SESSION['task_data'],
                'risk_data' => $this->get_risk_wizard_form_data(),
                'edit_mode' => true);
            $data['risk_data']['risk_id'] = $risk_id;
            $this->load->view('templates/header', $data);
            $this->load->view('project/risk_wizard');
            $this->load->view('templates/footer');
        } else {
            redirect('risk/view/' . $risk_id);
        }
    }

    function edit_risk($impact_discussion, $risk_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $risk_data = $this->get_risk_wizard_form_data();
        if ($risk_data['type'] != "Threat" && $risk_data['type'] != "Opportunity") {
            $this->form_validation->set_message("Error on page. Please try again later.");
            return false;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if (!$permission) {
            $this->form_validation->set_message('edit_risk', 'You do not have permission to access this resource.');
            $this->Risk_model->close();
            return false;
        }
        if ($this->Risk_model->edit($risk_data) === false) {
            $this->form_validation->set_message('edit_risk', 'You do not have permission to access this resource.');
            $this->Risk_model->close();
            return false;
        }
        $this->Risk_model->close();
        return true;
    }

    function update($risk_id = null) {
        if ($risk_id == null)
            redirect('dashboard');
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message']; redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        //lookup risk id and permission
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Write" && $permission != "Owner" && $permission != "Admin") {
            redirect('dashboard');
        }
        $risk_data = $this->Risk_model->get();
        $this->Risk_model->close();
        $this->load->helper(array('form'));
        $data = array('title' => 'Risk Update',
            'risk_data' => $risk_data);
        $this->load->view('templates/header', $data);
        $this->load->view('project/risk_update');
        $this->load->view('templates/footer');
    }

    function new_update($impact_discussion, $risk_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $impact = $this->input->post('impact');
        $impact_effect = $this->input->post('rng_impact_effect');
        $days_delay = $this->input->post('txt_days_delay');
        $cost_impact = $this->input->post('txt_cost_impact');
        $probability = $this->input->post('rng_probability');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if (!$permission) {
            $this->form_validation->set_message('update_risk', 'You do not have permission to access this resource.');
            $this->Risk_model->close();
            return false;
        }
        $update_data = array(
            'impact' => $impact, 'probability' => $probability,
            'impact_effect' => $impact_effect, 'days_delay' => $days_delay, 'cost_impact' => $cost_impact,
            'impact_discussion' => $impact_discussion);
        if ($this->Risk_model->insert_update($update_data) === false) {
            $this->form_validation->set_message('update_risk', 'You do not have permission to access this resource.');
            $this->Risk_model->close();
            return false;
        }
        $this->Risk_model->close();
        return true;
    }

    function update_form($risk_id = null) {
        if ($risk_id == null) {
            redirect('dashboard');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('impact', 'Impact', 'trim|required|xss_clean');
        $this->form_validation->set_rules('rng_impact_effect', 'Impact Effect', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_days_delay', 'Impact - Days Delay', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_cost_impact', 'Cost Impact', 'trim|required|xss_clean');
        $this->form_validation->set_rules('rng_probability', 'Probability', 'trim|xss_clean');
        $this->form_validation->set_rules('impact_discussion', 'Impact Discussion', 'trim|xss_clean|callback_new_update[' . $risk_id . ']');
        if ($this->form_validation->run()) {
            redirect('risk/view/' . $risk_id);
        } else {
            redirect('risk/update/' . $risk_id);
        }
    }

    function wizard($task_id = null) {
        if ($task_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        //lookup task id
        $this->load->model('Task_model');
        if ($this->Task_model->initialize($task_id, $user_id) === false) {
            redirect('dashboard');
        }
        $task_data = $this->Task_model->get();
        $this->Task_model->close();
        $this->load->helper(array('form'));
        $_SESSION['task_data'] = $task_data; //store task data in session in case form must be reloaded
        $data = array('title' => 'Risk Identification',
            'task_id' => $task_id,
            'task_data' => $task_data,
            'edit_mode' => false);
        $this->load->view('templates/header', $data);
        $this->load->view('project/risk_wizard');
        $this->load->view('templates/footer');
    }

    function wizard_form($task_id = null) {
        if ($task_id == null) {
            redirect('dashboard');
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('risk_type', 'Risk Type (start)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('event', 'Event (step 1)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('impact', 'Impact (step 1)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_of_concern', 'Date of Concern (step 1)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_impact_effect', 'Impact Effect (step 3)', 'trim|required|xss_clean');
        // Days Delay 
        $this->form_validation->set_rules('txt_days_delay', 'Impact - Days Delay (step 3)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_cost_impact', 'Cost Impact (step 3)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_probability', 'Probability (step 4)', 'trim|required|xss_clean');
        $this->form_validation->set_rules('impact_discussion', 'Impact Discussion (step 6)', 'trim|xss_clean|callback_insert_risk[' . $task_id . ']');
        if ($this->form_validation->run() == false) {
            $data = array('title' => 'Risk Identification',
                'task_data' => $_SESSION['task_data'],
                'risk_data' => $this->get_risk_wizard_form_data(),
                'edit_mode' => false);
            $this->load->view('templates/header', $data);
            $this->load->view('project/risk_wizard');
            $this->load->view('templates/footer');
        } else {
            redirect('task/view/' . $task_id);
        }
    }

    private function get_risk_wizard_form_data() {
        $risk_data = array('event' => $this->input->post('event'),
            'date_of_concern' => $this->input->post('date_of_concern'),
            'type' => $this->input->post('risk_type'),
            'impact' => $this->input->post('impact'),
            'probability' => $this->input->post('txt_probability'),
            'impact_effect' => $this->input->post('txt_impact_effect'),
            'days_delay' => $this->input->post('txt_days_delay'),
            'cost_impact' => $this->input->post('txt_cost_impact'),
            'impact_discussion' => $this->input->post('impact_discussion'));
        return $risk_data;
    }

    function insert_risk($impact_discussion, $task_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risk_data = $this->get_risk_wizard_form_data();
        if ($risk_data['type'] != "Threat" && $risk_data['type'] != "Opportunity") {
            $this->form_validation->set_message("Error on page. Please try again later.");
            return false;
        }
        $risk_data['task_id'] = $task_id;
        $risk_id = $this->Risk_model->create($risk_data, $user_id);
        if ($risk_id === false) {
            $this->form_validation->set_message('insert_risk', 'You do not have permission to access this resource.');
            return false;
        }
        return $risk_id;
    }

    function create_batch() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $task_ids = func_get_args();
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        //lookup project_id of first task
        $this->load->model('Task_model');
        if (!$this->Task_model->initialize($task_ids[0], $user_id)) {
            redirect('dashboard');
        }
        $project_id = $this->Task_model->get_project_id();
        if (!$this->Risk_model->create_batch($task_ids, $user_id)) {
            print("<script type='text/javascript'>alert('An error occured when identifiying the risks. Please try again later');window.location.href='" .
                    base_url('project/view/' . $project_id)) . "';</script>";
        }
        redirect('project/view/' . $project_id);
    }

}