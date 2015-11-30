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

/*
 * Controller to handle POST requests.
 */

class Data_fetch extends CI_Controller {

    const ERROR_NO_ACCESS_PERMISSION = "You do not have permission to access this resource.";
    const ERROR_NO_EDIT_PERMISSION = "You do not have permission to do this.";
    const ERROR_NOT_LOGGED_IN = "You are not logged in.";
    const ERROR_UNKNOWN = "An error occured. Please try again later.";

    public function __construct() {
        parent::__construct();
    }

    /* ---------------------- BEGIN JTABLE POST FUNCTIONS ---------------------- */

    public function add_project_user($project_id = null) {
        if ($project_id === null) {
            $this->print_jtable_error('Project not specified.');
            return;
        }
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $add_username = $this->input->post('username');
        $user_lookup = $this->User_model->get_by_username($add_username);
        if (!$user_lookup['success']) {
            $this->print_jtable_error($user_lookup['message']);
            return;
        }
        $add_id = $user_lookup['data']['user_id'];
        $user_id = $_SESSION['user_id'];
        $this->load->model('Project_model');
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission !== "Admin") {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        $insert = $this->Project_model->add_user($add_id, $this->input->post('permission'));
        if (!$insert['success']) {
            $this->print_jtable_error($insert['message']);
            return;
        } else {
            $project_users = $this->Project_model->get_all_user_permissions();
            $record;
            foreach ($project_users as &$user) {
                if ($user['user_id'] == $add_id) {
                    $record = $user;
                    break;
                }
            }
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }

    public function projects($offset = 0, $limit = 100, $order_by = 'project_name', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $this->User_model->initialize($_SESSION['user_id']);
        $projects = $this->User_model->get_projects($limit, $offset, $order_by, $direction);
        if (count($projects) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($projects);
    }

    public function delete_project($project_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Project_model');
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission !== 'Owner' && $permission !== 'Admin') {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if ($this->Project_model->delete($project_id, $user_id)) {
            print json_encode(array('Result' => "OK"));
            return;
        } else {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
    }

    public function create_project() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Project_model');
        $data['project_name'] = $this->input->post('project_name');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        $insert_id = $this->Project_model->create($data, $user_id);
        if ($insert_id == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            if ($this->Project_model->initialize($insert_id, $user_id) === false) {
                $this->print_jtable_error('The project was created successfully but the page could not be updated. Please refresh your browser.');
            }
            $record = $this->Project_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }

    public function edit_project() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $project_id = $this->input->post('project_id');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Project_model');
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['project_name'] = $this->input->post('project_name');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Project_model->edit($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => "OK"));
            return true;
        }
    }

    public function tasks($project_id, $offset = 0, $limit = 100, $order_by = 'WBS', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        $tasks = $this->Task_model->get_all($project_id, $user_id, $limit, $offset, $order_by, $direction);
        $this->Task_model->close();
        if ($tasks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($tasks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($tasks);
    }

    public function tasks_with_risks($project_id, $offset = 0, $limit = 100, $order_by = 'WBS', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        $tasks = $this->Task_model->get_all_with_risks($project_id, $user_id, $limit, $offset, $order_by, $direction);
        $this->Task_model->close();
        if ($tasks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($tasks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($tasks);
    }

    public function task_info($task_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        if ($this->Task_model->initialize($task_id, $user_id) === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        $task_info = $this->Task_model->get();
        $this->Task_model->close();
        if ($task_info === false) {
            return $this->print_jtable_no_records();
        }
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['TotalRecordCount'] = 1;
        $jTableResult['Records'] = $task_info;
        print json_encode($jTableResult);
    }

    public function delete_task($task_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        if ($this->Task_model->initialize($task_id, $user_id) === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if ($this->Task_model->delete()) {
            print json_encode(array('Result' => "OK"));
            return;
        } else {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
    }

    public function edit_task() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $task_id = $this->input->post('task_id');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        $permission = $this->Task_model->initialize($task_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['WBS'] = $this->input->post('WBS');
        $data['task_name'] = $this->input->post('task_name');
        $data['duration'] = $this->input->post('duration');
        $data['work'] = $this->input->post('work');
        $data['start_date'] = $this->input->post('start_date');
        $data['finish_date'] = $this->input->post('finish_date');
        $data['fixed_cost'] = $this->input->post('fixed_cost');
        $data['cost'] = $this->input->post('cost');
        $data['price'] = $this->input->post('price');
        $data['resource_names'] = $this->input->post('resource_names');
        $data['vendor'] = $this->input->post('vendor');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Task_model->edit($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => "OK"));
            return true;
        }
    }

    public function create_task($project_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Project_model');
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $this->load->model('Task_model');
        $data['project_id'] = $project_id;
        $data['WBS'] = $this->input->post('WBS');
        $data['task_name'] = $this->input->post('task_name');
        $data['duration'] = $this->input->post('duration');
        $data['work'] = $this->input->post('work');
        $data['start_date'] = $this->input->post('start_date');
        $data['finish_date'] = $this->input->post('finish_date');
        $data['fixed_cost'] = $this->input->post('fixed_cost');
        $data['cost'] = $this->input->post('cost');
        $data['price'] = $this->input->post('price');
        $data['resource_names'] = $this->input->post('resource_names');
        $data['vendor'] = $this->input->post('vendor');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        $insert_id = $this->Task_model->create($data, $user_id);
        if ($insert_id == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            if ($this->Task_model->initialize($insert_id, $user_id) === false) {
                $this->print_jtable_error('The task was created successfully but the page could not be updated. Please refresh your browser.');
            }
            $record = $this->Task_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }

    public function risks_by_user($offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $filter_field = $this->input->post('filter_field');
        $filter_value = $this->input->post('filter_value');
        $this->load->helper('security');
        xss_clean($filter_field);
        xss_clean($filter_value);
        $risks = $this->Risk_model->get_all_by_user($user_id, $limit, $offset, $order_by, $order_by_2 = null, $order_by_3 = null, $direction, $direction_2 = null, $filter_field, $filter_value);
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }

    public function risks_by_project($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_all_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }
    
    public function risks_cause_by_project($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_all_causes_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }

    public function upcoming_risks($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_upcoming($project_id, $user_id);
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }

    public function top_risks($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_top_by_expected_cost($project_id, $user_id);
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }

    public function severe_risks($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_severe_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }

    public function risks_by_task($task_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_all_by_task($task_id, $user_id, $limit, $offset, $order_by, $direction);
        $this->Risk_model->close();
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0) {
            return $this->print_jtable_no_records();
        }
        else
            return $this->print_jtable_result($risks);
    }

    public function delete_risk($risk_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        if ($this->Risk_model->initialize($risk_id, $user_id) === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if ($this->Risk_model->delete()) {
            print json_encode(array('Result' => "OK"));
            return;
        } else {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
    }

    public function edit_risk() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $risk_id = $this->input->post('risk_id');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['event'] = $this->input->post('event');
        $data['date_of_concern'] = $this->input->post('date_of_concern');
        $data['date_identified'] = $this->input->post('date_identified');
        $data['date_closed'] = $this->input->post('date_closed');
        $data['type'] = $this->input->post('type');
        $data['impact'] = $this->input->post('impact');
        $data['probability'] = $this->input->post('probability');
        $data['impact_effect'] = $this->input->post('impact_effect');
        $data['days_delay'] = $this->input->post('days_delay');
        $data['cost_impact'] = $this->input->post('cost_impact');
        $data['impact_discussion'] = $this->input->post('impact_discussion');
        $data['urgent'] = $this->input->post('urgent');
        // $data['img_url'] = $this->input->post('img_url');
            // echo "<script>alert('About to transfer to img url original');</script>";
        $img_url_original = $this->input->post('img_url');
        $img_url_original_array = explode(',', $img_url_original);
            // echo "<script>alert('Transfer complete. About to enter if statement');</script>";
        if ( isset( $img_url_original ) && $img_url_original != "" && !empty($img_url_original) ) {
                // echo "<script>alert('Entered if statement');</script>";
            $previous_risk_data = $this->Risk_model->get();
                // echo "<script>alert('Feteched previous risk data');</script>";
            if ( isset($previous_risk_data['img_url']) && $previous_risk_data['img_url'] != "" && !empty($previous_risk_data['img_url']) ) {
                $previous_media_items = explode(',', $previous_risk_data['img_url']);
            }
            else {
                $previous_media_items = '';
            }
            // echo "<script>alert('Exploded array');</script>";
            if ( sizeof($previous_media_items) == 3 || $previous_media_items == '') {
                $data['img_url'] = $img_url_original;
            }
            else if ( sizeof($previous_media_items) == 2 && sizeof($img_url_original_array) <=1 ) {
                $data['img_url'] = $previous_risk_data['img_url'] . ',' . $img_url_original;
            }
            else if ( sizeof($previous_media_items) == 1 && sizeof($img_url_original_array) <=2 ) {
                $data['img_url'] = $previous_risk_data['img_url'] . ',' . $img_url_original;
            }
            else {
                $data['img_url'] = $img_url_original;
            }
        }
        else {
            // echo "<script>alert('img_url is not set');</script>";
            $previous_risk_data = $this->Risk_model->get();
            $data['img_url'] = $previous_risk_data['img_url']; 
        } 

        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Risk_model->edit($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            $record = $this->Risk_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            // print json_encode(array('Result' => "OK"));
            return true;
        }
    }

    public function update_lessons_learned_risk($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $risk_id = $this->input->post('risk_id');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['event'] = $this->input->post('event');
        $data['date_closed'] = $this->input->post('date_closed');
        $data['probability'] = $this->input->post('probability');
        $cause_str = implode(",", $this->input->post('cause')); 
        $data['cause'] = $cause_str;
        // $data['occurred'] = $this->input->post('occurred');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        
        if ($this->Risk_model->update_lessons_learned($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            $record = $this->Risk_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }

    public function create_risk($task_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        $permission = $this->Task_model->initialize($task_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $this->load->model('Risk_model');
        $data['task_id'] = $task_id;
        $data['event'] = $this->input->post('event');
        $data['date_of_concern'] = $this->input->post('date_of_concern');
        $data['date_identified'] = $this->input->post('date_identified');
        $data['date_closed'] = $this->input->post('date_closed');
        $data['type'] = $this->input->post('type');
        $data['impact'] = $this->input->post('impact');
        $data['probability'] = $this->input->post('probability');
        $data['impact_effect'] = $this->input->post('impact_effect');
        $data['days_delay'] = $this->input->post('days_delay');
        $data['cost_impact'] = $this->input->post('cost_impact');
        $data['impact_discussion'] = $this->input->post('impact_discussion');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        $insert_id = $this->Risk_model->create($data, $user_id);
        if ($insert_id == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            if ($this->Risk_model->initialize($insert_id, $user_id) === false) {
                $this->print_jtable_error('The risk was created successfully but the page could not be updated. Please refresh your browser to see the new risk.');
            }
            $record = $this->Risk_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }
    /*
     * Identifies a risk, called when user selects a risk checkbox on the tasks table
     */
    public function identify_risk($task_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Task_model');
        $permission = $this->Task_model->initialize($task_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $task_data = $this->Task_model->get();
        $this->load->model('Risk_model');
        $data['task_id'] = $task_id;
        $data['event'] = "Not identified";
        $data['date_of_concern'] = $task_data['start_date'];
        // $data['date_of_concern'] = date('Y-m-d');
        if ($this->Risk_model->create($data, $user_id) === false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => 'OK'));
            return;
        }
    }

    // Called when occurred in LessonsLearned table is checked off
    public function occurred_risk($risk_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }

        $data['occurred'] = 'yes';
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }

        if ($this->Risk_model->update_risk_occurrence($data) === false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => 'OK'));
            return;
        }
    }

    // Called when occurred in LessonsLearned table is unchecked
    public function not_occurred_risk($risk_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }

        $data['occurred'] = 'no';
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }

        if ($this->Risk_model->update_risk_occurrence($data) === false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => 'OK'));
            return;
        }
    }

    function response_updates($response_id = null, $offset = 0, $limit = 100, $order_by = 'date_of_update', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        $updates = $this->Response_model->get_updates($limit, $offset, $order_by, $direction);
        if ($updates === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        } else {
            return $this->print_jtable_result($updates);
        }
    }

    function create_response_update($response_id = null) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        if ($response_id == null) {
            $this->print_jtable_error('response_id cannot be null!');
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['response_id'] = $response_id;
        $data['cost'] = $this->input->post('cost');
        $data['post_response'] = $this->input->post('post_response');
        $data['owner'] = $this->input->post('owner');
        $data['release_progress'] = $this->input->post('release_progress');
        $data['current_status'] = $this->input->post('current_status');
        $data['planned_closure'] = $this->input->post('planned_closure');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        $insert_id = $this->Response_model->insert_update($data);
        if ($insert_id == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            $record = $this->Response_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }

    function responses($risk_id = null, $offset = 0, $limit = 100, $order_by = 'response_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $responses = $this->Response_model->get_all($risk_id, $user_id, $limit, $offset, $order_by, $direction);
        if ($responses == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        } else {
            return $this->print_jtable_result($responses);
        }
    }

    function responses_by_project($project_id = null, $offset = 0, $limit = 100, $order_by = 'response_id', $direction = 'ASC') {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $responses = $this->Response_model->get_all_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);
        if ($responses === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($responses) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($responses);
    }

    public function delete_response($response_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        if ($this->Response_model->initialize($response_id, $user_id) === false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if ($this->Response_model->delete()) {
            print json_encode(array('Result' => "OK"));
            return;
        } else {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
    }

    public function delete_response_update($response_update_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $result = $this->Response_model->delete_response_update($response_update_id, $user_id);
        if ($result['success']) {
            print json_encode(array('Result' => 'OK'));
            return;
        }
        $this->print_jtable_error($result['message']);
        return;
    }

    public function edit_response() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $response_id = $this->input->post('response_id');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['date_of_plan'] = $this->input->post('date_of_plan');
        $data['action_plan'] = $this->input->post('action_plan');
        $data['owner'] = $this->input->post('owner');
        $data['cost'] = $this->input->post('cost');
        $data['post_response'] = $this->input->post('post_response');
        $data['release_progress'] = $this->input->post('release_progress');
        $data['action'] = $this->input->post('action');
        $data['current_status'] = $this->input->post('current_status');
        $data['planned_closure'] = $this->input->post('planned_closure');
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Response_model->edit($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            $last_record = $this->Response_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $last_record));
            return true;
        }
    }

    //Lessons Learned reponse child table functionality
    public function update_lessons_learned_response() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $response_id = $this->input->post('response_id');
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['action_plan'] = $this->input->post('action_plan');
        $data['release_progress'] = $this->input->post('release_progress');
        // $data['successful'] = $this->input->post('successful');
        $cause_str = implode(",", $this->input->post('cause')); 
        $data['cause'] = $cause_str;
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Response_model->update_lessons_learned($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            $record = $this->Response_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            // print json_encode(array('Result' => "OK"));
            return true;
        }
    }

    // Called when successful in LessonsLearned response child table is checked off
    public function successful_response($response_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['successful'] = 'yes';
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Response_model->update_response_success($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => "OK"));
            return true;
        }
    }

    // Called when successful in LessonsLearned response child table is unchecked
    public function unsuccessful_response($response_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Response_model');
        $permission = $this->Response_model->initialize($response_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $data['successful'] = 'no';
        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        if ($this->Response_model->update_response_success($data) == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            print json_encode(array('Result' => "OK"));
            return true;
        }
    }

    public function create_response($risk_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $permission = $this->Risk_model->initialize($risk_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $risk_info = $this->Risk_model->get();
        $task_id = $risk_info['task_id'];
        $this->load->model('Task_model');
        $permission = $this->Task_model->initialize($task_id, $user_id);
        if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
            $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
            return;
        }
        $task_data = $this->Task_model->get();
        $task_start_date = $task_data['start_date'];

        $this->load->model('Response_model');
        $data['risk_id'] = $risk_id;
        $data['date_of_plan'] = $this->input->post('date_of_plan');
        $data['action_plan'] = $this->input->post('action_plan');
        $data['owner'] = $this->input->post('owner');
        $data['cost'] = $this->input->post('cost');
        $data['post_response'] = $this->input->post('post_response');
        $data['release_progress'] = $this->input->post('release_progress');
        $data['action'] = $this->input->post('action');
        $data['current_status'] = $this->input->post('current_status');
        $data['planned_closure'] = $this->input->post('planned_closure');

        if( isset($data['planned_closure']) ) {
            $is_date = strtotime($data['planned_closure']);
            if($is_date === false) {
                $data['planned_closure'] = $task_start_date;
            } else {
                $data['planned_closure'] = date('Y-m-d', strtotime($data['planned_closure']));
            }
        } else {
            $data['planned_closure'] = $task_start_date;
        }

        $this->load->helper('security');
        foreach ($data as &$val) {
            xss_clean($val);
        }
        $insert_id = $this->Response_model->create($data, $user_id);
        if ($insert_id == false) {
            $this->print_jtable_error(self::ERROR_UNKNOWN);
            return;
        } else {
            if ($this->Response_model->initialize($insert_id, $user_id) === false) {
                $this->print_jtable_error('The response was created successfully but the page could not be updated. Please refresh your browser to see the new risk.');
            }
            $record = $this->Response_model->get();
            print json_encode(array('Result' => "OK", 'Record' => $record));
            return true;
        }
    }

    public function project_users($project_id = null, $offset = 0, $limit = 100, $order_by = 'last_name', $direction = 'ASC') {
        if ($project_id == null) {
            $this->print_jtable_error('Project id not specified.');
            return;
        }
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission !== "Owner" && $permission !== "Admin") {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        $users = $this->Project_model->get_all_user_permissions($order_by, $direction);
        if ($users == false) {
            $this->print_jtable_error('An error occured while loading project users. Please try again later.');
            return;
        } else {
            return $this->print_jtable_result($users);
        }
    }

    public function delete_project_user($project_id, $delete_user_id) {
        if ($project_id == null) {
            $this->print_jtable_error('Project id not specified.');
            return;
        }
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $permission = $this->Project_model->initialize($project_id, $_SESSION['user_id']);
        if ($permission !== "Owner" && $permission !== "Admin") {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        $remove = $this->Project_model->remove_user($delete_user_id);
        if ($remove['success']) {
            print json_encode(array('Result' => "OK"));
            return;
        } else {
            $this->print_jtable_error($remove['message']);
            return;
        }
    }
    /* ---------------------- END JTABLE FUNCTIONS -------------------- */
    
    /* ---------------------- BEGIN GOOGLE CHARTS FUNCTIONS ------------------------ */
        public function expected_cost_graph($project_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_all_by_project($project_id, $user_id, 0, 0, "date_of_concern", "ASC", "risk_id, date_of_concern, expected_cost, date_of_concern, days_open, event");
        if ($risks == false) {
            $this->print_jtable_error(self::ERROR_NO_ACCESS_PERMISSION);
            return;
        }
        if (count($risks) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($risks);
    }

    public function risk_by_date_data($project_id) {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $data = $this->Risk_model->get_data_by_dates($project_id);
        if (count($data) == 0)
            return $this->print_jtable_no_records();
        else
            return $this->print_jtable_result($data);
    }
    /* ---------------------- END GOOGLE CHARTS FUNCTIONS ------------------------ */    
    
    /* ---------------------- BEGIN MSPROJECT ADD-IN FUNCTIONS ------------------------*/
    public function vb_project_list() {
        $confirm_user = $this->User_model->verify_credentials($this->input->post('password'), $this->input->post('username'));
        if ($confirm_user['success'] == false) {
            print json_encode(array('success' => false, 'message' => "Invalid username or password."));
                return;
        }
        $user_data = $confirm_user['data'];
        $this->User_model->initialize($user_data['user_id']);
        $projects = $this->User_model->get_projects(null, null, "project_name");
        $count = $projects['num_rows'];
        unset($projects['num_rows']);
        print json_encode(array('success' => true, 'data' => json_encode($projects)));
        return;
    }

    public function vb_download_risks() {
        $confirm_user = $this->User_model->verify_credentials($this->input->post('password'), $this->input->post('username'));
        if ($confirm_user['success'] == false) {
            print json_encode(array('success' => false, 'message' => "Invalid username or password."));
                return;
        }
        $user_data = $confirm_user['data'];
        $this->load->model('Risk_model');
        $risks = $this->Risk_model->get_all_by_project($this->input->post('project_id'), $user_data['user_id']);
        if ($risks === false) {
            print json_encode(array('success' => false, 'message' => self::ERROR_NO_ACCESS_PERMISSION));
            return;
        }
        unset($risks['num_rows']);
        print json_encode(array('success' => true, 'data' => json_encode($risks)));
    }
    
    public function vb_download_responses() {
        $confirm_user = $this->User_model->verify_credentials($this->input->post('password'), $this->input->post('username'));
        if ($confirm_user['success'] == false) {
            print json_encode(array('success' => false, 'message' => "Invalid username or password."));
                return;
        }
        $user_data = $confirm_user['data'];
        $this->load->model('Response_model');
        $responses = $this->Response_model->get_all_by_project($this->input->post('project_id'), $user_data['user_id']);
        if ($responses === false) {
            print json_encode(array('success' => false, 'message' => self::ERROR_NO_ACCESS_PERMISSION));
            return;
        }
        unset($responses['num_rows']);
        print json_encode(array('success' => true, 'data' => json_encode($responses)));
    }

    public function import_project() {
        $confirm_user = $this->User_model->verify_credentials($this->input->post('password'), $this->input->post('username'));
        if ($confirm_user['success'] == false) {
            print json_encode(array('success' => false, 'message' => "Invalid username or password"));
            return;
        }
        $user_data = $confirm_user['data'];
        $this->load->model('Project_model');
        $this->load->helper('security');
        if (isset($_POST['project_id'])) {
            //existing project
            $project_id = $this->input->post('project_id');
            if ($this->Project_model->initialize($project_id, $user_data['user_id']) === false) {
                print json_encode(array('success' => false, 'message' => self::ERROR_NO_EDIT_PERMISSION));
                return;
            }
        } else {
            //new project
            $project_data = array('project_name' => $this->input->post('project_name'));
            foreach ($project_data as &$val) {
                xss_clean($val);
            }
            $insert_id = $this->Project_model->create($project_data, $user_data['user_id']);
            if ($insert_id == false) {
                print json_encode(array('success' => false, 'message' => self::ERROR_UNKNOWN));
                return;
            }
            if ($this->Project_model->initialize($insert_id, $user_data['user_id']) === false) {
                print json_encode(array('success' => false, 
                    'message' => "An unknown error has occured. The project was created but the tasks were not. Please try again.",
                    'project_id' => $project_id));
                return;
            }
            $project_id = $insert_id;
        }
        //create associative array for tasks
        $tasks = json_decode($this->input->post('tasks'));
        $fields = json_decode($this->input->post('fields'));
        $task_data = array();
        foreach ($tasks as &$task) {
            $assoc_task = array('project_id' => $project_id);
            $count = 0;
            foreach ($tasks as &$value) {
                xss_clean($value);
            }
            foreach ($fields as $fieldname) {
                $assoc_task[$fieldname] = $task[$count];
                $count++;
            }
            array_push($task_data, $assoc_task);
        }
        $this->load->model('Task_model');
        if ($this->Task_model->create_batch($task_data, $project_id, $user_data['user_id'])) {
            //NOTE: success (true) MUST BE A STRING for the vb extension to properly interpret it
            print json_encode(array('success' => "true", 'message' => "The tasks were successfully imported",
                'project_id' => $project_id));
            return;
        } else {
            print json_encode(array('success' => false, 'message' => "Could not import tasks. Please try again later."));
            return;
        }
    }
/* ---------------------- END MSPROJECT ADD-IN FUNCTIONS ------------------------*/
    
/* ---------------------- BEGIN BILLING FUNCTIONS ------------------------ */

    public function confirm_subscription_amounts() {
        $confirm_member = $this->User_model->confirm_member(false, false);
        if (!$confirm_member['success']) {
            $result = array(
                'success' => false,
                'message' => self::ERROR_NOT_LOGGED_IN
            );
            print json_encode($result);
            return;
        }
        $billingperiod = $this->input->post('billingperiod');
        $province = $this->input->post('province');
        $this->load->model('Billing_model');
        print json_encode($this->Billing_model->calculate_profile_amounts($_SESSION['user_id'], $billingperiod, $province));
    }

    public function create_billing_profile() {
        $confirm_member = $this->User_model->confirm_member(false, false);
        if (!$confirm_member['success']) {
            $result = array(
                'success' => false,
                'message' => self::ERROR_NOT_LOGGED_IN
            );
            print json_encode($result);
            return;
        }
        $billingperiod = $this->input->post('billingperiod');
        $CCDetails = array(
            'creditcardtype' => $this->input->post('creditcardtype'),
            'acct' => $this->input->post('acct'),
            'expdate' => $this->input->post('expdate')
        );
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $province = $this->input->post('province');
        $this->load->model('Billing_model');
        print json_encode($this->Billing_model->create_billing_profile($_SESSION['user_id'], $billingperiod, $CCDetails, $firstname, $lastname, $province));
    }

    public function redeem_promotion() {
        $confirm_member = $this->User_model->confirm_member(false, false);
        if (!$confirm_member['success']) {
            $result = array(
                'success' => false,
                'message' => self::ERROR_NOT_LOGGED_IN
            );
            print json_encode($result);
            return;
        }
        $this->load->helper('security');
        $promotion_code = $this->input->post('promotion_code');
        xss_clean($promotion_code);
        $this->load->model('Billing_model');
        print json_encode($this->Billing_model->redeem_promotion($promotion_code, $_SESSION['user_id']));
    }

    public function cancel_membership() {
        $confirm_member = $this->User_model->confirm_member(false, false);
        if (!$confirm_member['success']) {
            $result = array(
                'success' => false,
                'message' => self::ERROR_NOT_LOGGED_IN
            );
            print json_encode($result);
            return;
        }
        $this->load->model('Billing_model');
        print json_encode($this->Billing_model->cancel_user_profiles($_SESSION['user_id']));
    }

    public function get_invoices() {
        $confirm_member = $this->User_model->confirm_member(false, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
        $this->load->model('Billing_model');
        $this->print_jtable_result($this->Billing_model->get_user_transactions($_SESSION['user_id'], 'transaction_id, amount, order_time'));
    }

    /* ---------------------- END BILLING FUNCTIONS ------------------------ */
    public function forgot_password() {
        $username = $this->input->post('username');
        $this->load->helper('security');
        xss_clean($username);
        print json_encode($this->User_model->forgot_password($username));
    }

    public function error_report() {
        $confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            print 'Please log in to submit a bug report';
            return;
        }
        $error_text = $this->input->post('error_text');
        $this->load->helper('security');
        xss_clean($error_text);
        $this->load->model('Error_model');
        if ($this->Error_model->report_error($_SESSION['user_id'], $this->input->post('error_text'))) {
            print "Bug report successfully submitted.";
        } else {
            print "An unknown error has occurred. Please try again later.";
        }
    }

    private function print_jtable_no_records() {
        print json_encode(array('Result' => 'OK', 'TotalRecordCount' => 0, 'Records' => array()));
    }

    private function print_jtable_result($data, $TotalRecordCount = null) {
        $result = array('Result' => 'OK');
        if ($TotalRecordCount == null) {
            if (isset($data['num_rows'])) {
                $result['TotalRecordCount'] = $data['num_rows'];
                unset($data['num_rows']);
            }
        } else {
            $result['TotalRecordCount'] = $TotalRecordCount;
        }
        $result['Records'] = $data;
        print json_encode($result);
    }

    private function print_jtable_error($error_message) {
        print json_encode(array('Result' => 'ERROR', 'Message' => $error_message));
    }

}

?>