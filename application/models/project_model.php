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

class Project_model extends CI_Model {

    private $project_id;
    private $user_id;
    private $initialized = false; //denotes whether the model has been associated with a specific project

    public function __construct() {
        parent::__construct();
    }

    public function initialize($project_id, $user_id) {
        $this->project_id = $project_id;
        if (!isset($user_id)) {
            return false;
        }
        $permission = $this->get_user_permission($user_id, $project_id);
        if ($permission === false) {
            return false;
        }
        $this->user_id = $user_id;
        $this->initialized = true;
        return $permission;
    }

    /**
     * Gets all fields associated with the project.
     * @return type an associative array containing the fetched data.
     */
    public function get($select = "*") { 
        if (!$this->initialized) {
            return false;
        }
        $this->db->select($select);
        $query = $this->db->get_where('view_projects', array('project_id' => $this->project_id));
        return $query->row_array();
    }

    public function get_expected_delay($select = "*") { 
        if (!$this->initialized) {
            return false;
        }
        $this->db->select($select);
        $query = $this->db->get_where('view_projects_risk_costs_delay', array('project_id' => $this->project_id));
        return $query->row_array();
    }

    /**
     * Updates project data in the database.
     * @param array $data An associative array containing the values to be updated, using field names as keys.
     * @return boolean True on success, false on failure.
     */
    public function edit(array $data) {
        if (!$this->initialized) {
            return false;
        }
        if (!$this->modify($this->user_id)) {
            return false;
        }
        //keys are the values which can be updated
        $keys = array('date_created', 'status', 'date_completed',
            'project_name');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        return $this->db->where('project_id', $this->project_id)->limit(1)->update('projects', $data);
    }
    /*
     * Deletes the project and all associated (child) data
     */
    public function delete() {
        if (!$this->initialized) {
            return false;
        }
        if (!$this->modify($this->user_id)) {
            return false;
        }
        return $this->db->from('projects')->where('project_id', $this->project_id)->limit(1)->delete();
    }

    /*
     * Creates a new projects
     */
    public function create(array $data, $user_id) {
        if (!isset($data['project_name'])) {
            return false;
        }
        //lookup user
        if (!$this->db->from('users')
                        ->where('user_id', $user_id)
                        ->limit(1)->count_all_results()) {
            return false;
        }
        //add values to new array to filter unwanted fields
        $rowdata = array('project_name' => $data['project_name'], 'date_modified' => null, 'last_modifier_id' => $user_id);
        if (!$this->db->insert('projects', $rowdata)) {
            return false; //insert failed
        }
        $insert_id = $this->db->insert_id();
        //add creator to project users
        $this->add_user($user_id, "Admin", $insert_id);
        return $insert_id; //return id of created project
    }

    /*
     * Uninitializes the model
     */
    public function close() {
        unset($this->project_id);
        unset($this->user_id);
        $this->initialized = false;
    }

    /**
     * Checks whether the user is permitted to modify the project. If so, the function also
     * sets the date_modified and last_modifier_id fields for the project.
     * @param string $user_id The id of the user who is modifying the project.
     * @param int $project_id Optional: Specifies the project_id. If this parameter is not set,
     * the id of the initialized project will be used. If the model is also not initialized,
     * then false will be returned.
     * @return boolean True if the user is permitted to modify the project, false if otherwise.
     */
    public function modify($user_id = null, $project_id = null) {
        if (!isset($user_id)) {
            if ($this->initialized == true) {
                if (isset($this->user_id)) {
                    $user_id = $this->user_id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        if (!isset($project_id)) {
            if ($this->initialized == true) {
                $project_id = $this->project_id;
            } else {
                return false;
            }
        }
        //check user permission
        $permission = $this->get_user_permission($user_id, $project_id);
        if (!($permission === 'Admin' ||
                $permission === 'Write')) {
            return false;
        }
        //update date modified
        $data = array('last_modifier_id' => $user_id, 'date_modified' => date("Y-m-d"));
        return $this->db->where('project_id', $project_id)->update('projects', $data);
    }

    /**
     * Returns the type of permission that the user has on the project.
     * @param string $user_id The id of the user.
     * @param int $project_id Optional: Specifies the project_id. If this parameter is not set,
     * the id of the initialized project will be used. If the model is also not initialized,
     * then false will be returned.
     * @return mixed String describing the user's permission type, or false if the user
     * has no permissions for the project.
     */
    public function get_user_permission($user_id, $project_id) {
        if (!isset($user_id)) {
            return false;
        }
        if (!isset($project_id)) {
            return false;
        }
        //get user data
        $query = $this->db->select('permission')
                        ->from('project_users')
                        ->where('project_id', $project_id)
                        ->where('user_id', $user_id)
                        ->limit(1)->get();
        if ($query->num_rows < 1) {
            return false;
        }
        $row = $query->row_array();
        return $row['permission'];
    }

    /*
     * Retrieves all users who have permissions on this project.
     */
    public function get_all_user_permissions($orderby = 'last_name', $direction = 'ASC') {
        if (!$this->initialized) {
            return false;
        }
        //get all users in the project_users table
        $query = $this->db->select('user_id, first_name, last_name, username, permission')->
                from('view_project_users')->
                where('project_id', $this->project_id)->
                order_by($orderby, $direction)->
                get();
        $project_users = $query->result_array();
        return $project_users;
    }

    /*
     * Removes a non-super user's permission for this project
     */
    public function remove_user($user_id) {
        if (!isset($user_id)) {
            return false;
        }
        if (!$this->modify($this->user_id)) {
            return array('success' => false, 'message' => 'You do not have permission to this.');
        }
        //ensure that not last project user
        if ($this->db->where('project_id', $this->project_id)->count_all_results('project_users') <= 1) {
            return array('success' => false, 'message' => 'You cannot remove the last user from the project. Instead, go to your dashboard and delete the project.');
        }
        $query = $this->db->from('project_users')
                ->where('project_id', $this->project_id)
                ->where('user_id', $user_id)
                ->limit(1)
                ->delete();
        if ($query == false || $this->db->affected_rows() < 1) {
            return array('success' => false, 'message' => 'An unknown error has occurred.');
        }
        return array('success' => true);
    }

    /*
     * Grants a user permissions for the project
     */
    public function add_user($user_id = null, $permission = "Read", $project_id = null) {
        if (!isset($user_id)) {
            return array('success' => false, 'message' => 'User not specified');
        }
        if (in_array($permission, array("Read", "Write", "Admin")) === false) {
            return array('success' => false, 'message' => 'Invalid permission type.');
        }
        if (!$this->initialized) {
            if ($project_id == null) {
                return array('success' => false, 'message' => 'Project not specified');
            }
        } else {
            $project_id = $this->project_id;
        }
        //check if user already has permission
        if ($this->get_user_permission($user_id, $project_id) !== false) {
            return array('success' => false, 'message' => 'The user already has permission for this project.');
        }
        //ensure that user exists
        if (!$this->db->from('users')->where('user_id', $user_id)->limit(1)->count_all_results()) {
            return false;
        }
        //add user permission
        $data = array('project_id' => $project_id, 'user_id' => $user_id,
            'permission' => $permission);
        if ($this->db->insert('project_users', $data)) {
            return array('success' => true);
        } else {
            return array('success' => false, 'message' => 'An unknown error has occurred.');
        }
    }

    /*
     * Imports tasks from comma-delimited data.
     * THIS FUNCTION IS NO LONGER USED.
     */

    public function import_tasks($csv_data) {
        $data = explode("\n", $csv_data); //create array from each line in data
        $numlines = count($data);
        $errors = array();
        //set
        $fields = array('WBS', 'task_name', 'duration', 'work', 'start_date',
            'finish_date', 'fixed_cost', 'cost', 'resource_names', 'vendor',
            'price');
        $this->load->model('Task_model');
        for ($i = 1; $i < $numlines; $i++) { //loop through all tasks
            $csv_array = explode(",", $data[$i]); //create array of task data
            $insert_data = array('project_id' => $this->project_id); //add project id to array
            //add csv data to array
            for ($i2 = 0; $i2 < count($fields); $i2++) {
                $insert_data[$fields[$i2]] = $csv_array[$i2];
            }
            //attempt to create task
            if (!$this->Task_model->create($insert_data, $this->user_id)) {
                //generate error message, add to error array
                array_push($errors, "There was an error importing data in line " . $i . ". ");
            }
        }
        return $errors;
    }

}

?>