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

class Task_model extends CI_Model {

    private $task_id;
    private $project_id;
    private $user_id;
    private $initialized = false;

    public function __construct() {
        parent::__construct();
    }

    public function initialize($task_id, $user_id) {
        if (!isset($user_id) || !isset($task_id)) {
            return false;
        }
        //check if task exists and lookup project_id
        $query = $this->db->select('project_id')->from('view_tasks')->
                        where('task_id', $task_id)->limit(1)->get();
        if ($query->num_rows < 1) {
            return false;
        }
        $this->user_id = $user_id;
        $this->task_id = $task_id;
        $row = $query->row_array();
        $this->project_id = $row['project_id'];
        $permission = $this->Project_model->get_user_permission($user_id, $this->project_id);
        if ($permission == false) {
            $this->close();
            return false;
        }
        $this->initialized = true;
        return $permission;
    }

    /**
     * Returns all fields associated with the task in an associative array. Note that the
     * model must be initialized before this function can be executed.
     * @return array An associative array containing the fetched values, or false if unsuccessful.
     */
    public function get($select = "*") {
        if ($this->initialized == false) {
            return false;
        }
        $query = $this->db->select($select)->from('view_tasks')->
                        where('task_id', $this->task_id)->limit(1)->get();
        if ($query->num_rows < 1) {
            return false;
        }
        $array = $query->row_array();
        if (isset($array['WBS'])) {
            $array['WBS'] = $this->normalize_WBS($array['WBS']);
        }
        return $array;
    }

    /**
     * Edits fields associated with the task. The model must
     * be initialized with a user_id before this function can be executed.
     * @param array $data An associative array containing the keys and values to be updated.
     * @return boolean True on successful update, false if otherwise.
     */
    public function edit(array $data) {
        if ($this->initialized == false) {
            return false;
        }
        if ($this->Project_model->modify($this->user_id, $this->project_id) == false) {
            return false;
        }
        $keys = array('WBS', 'task_name', 'duration', 'work', 'start_date', 'finish_date',
            'fixed_cost', 'cost', 'price', 'resource_names', 'vendor');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        if (isset($data['cost'])) {
            $data['cost'] = $this->format_currency($data['cost']);
        }
        if (isset($data['fixed_cost'])) {
            $data['fixed_cost'] = $this->format_currency($data['fixed_cost']);
        }
        if (isset($data['price'])) {
            $data['price'] = $this->format_currency($data['price']);
        }
        if (isset($data['WBS'])) {
            $data['WBS'] = $this->convert_WBS($data['WBS']); //convert WBS
        }
        return $this->db->where('task_id', $this->task_id)->update('tasks', $data);
    }

    /*
     * Deletes the task and all associated data
     */

    public function delete() {
        if ($this->initialized == false) {
            return false;
        }
        if ($this->Project_model->modify($this->user_id, $this->project_id) == false) {
            return false;
        }
        return $this->db->from('tasks')->where('task_id', $this->task_id)->limit(1)->delete();
    }

    /*
     * Creates a new task
     */

    public function create(array $data, $user_id) {
        if (!isset($data['project_id']) || !isset($user_id)) {
            return false;
        }
        //check if project_id exists
        if ($this->db->where('project_id', $data['project_id'])->count_all_results('projects') < 1) {
            return false;
        }
        //check if user can modify project
        if ($this->Project_model->modify($user_id, $data['project_id']) == false) {
            return false;
        }
        $keys = array('project_id', 'WBS', 'task_name', 'duration', 'work', 'start_date',
            'finish_date', 'fixed_cost', 'cost', 'price', 'resource_names', 'vendor');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        if (isset($data['cost'])) {
            $data['cost'] = $this->format_currency($data['cost']);
        }
        if (isset($data['fixed_cost'])) {
            $data['fixed_cost'] = $this->format_currency($data['fixed_cost']);
        }
        if (isset($data['price'])) {
            $data['price'] = $this->format_currency($data['price']);
        }
        if (isset($data['WBS'])) {
            $data['WBS'] = $this->convert_WBS($data['WBS']); //convert WBS
        }
        $data['WBS'] = $this->convert_WBS($data['WBS']);
        if ($this->db->insert('tasks', $data)) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return false;
        }
    }

    /*
     * Creates multiple tasks in a single SQL query. Primarily used for uploading
     * tasks from MSProject
     */

    public function create_batch(array $data, $project_id, $user_id) {
        if (!$this->Project_model->modify($user_id, $project_id))
            return false;
        $keys = array('project_id', 'WBS', 'task_name', 'duration', 'work', 'start_date',
            'finish_date', 'fixed_cost', 'cost', 'price', 'resource_names', 'vendor');
        foreach ($data as &$task) {
            if (isset($task['WBS'])) {
                $task['WBS'] = $this->convert_WBS($task['WBS']); //convert WBS
            }
            foreach ($task as $key => $val) {
                if (!in_array($key, $keys)) {
                    unset($data[$key]);
                }
            }
        }
        if ($this->db->insert_batch('tasks', $data))
            return true;
        else
            return false;
    }

    /*
     * Gets all tasks in the project
     */

    public function get_all($project_id, $user_id, $limit, $offset = 0, $orderby = "WBS", $direction = "asc") {
        if ($this->Project_model->get_user_permission($user_id, $project_id) == false) {
            return false;
        }
        $this->db->select('SQL_CALC_FOUND_ROWS *', false)->from('view_tasks')->
                where('project_id', $project_id)->
                order_by($orderby, $direction);
        if ($limit !== null && $limit != 0) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        foreach ($data as &$task) {
            $task['WBS'] = $this->normalize_WBS($task['WBS']);
        }
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }

    /*
     * Gets all tasks that have risks in the project
     */

    public function get_all_with_risks($project_id, $user_id, $limit, $offset = 0, $orderby = "WBS", $direction = "asc") {
        if ($this->Project_model->get_user_permission($user_id, $project_id) == false) {
            return false;
        }
        $this->db->select('SQL_CALC_FOUND_ROWS *', false)->from('view_tasks')->
                where('project_id', $project_id)->
                where('(active_risks > 0 OR closed_risks > 0)')->
                order_by($orderby, $direction);
        if ($limit !== null && $limit != 0) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        foreach ($data as &$task) {
            $task['WBS'] = $this->normalize_WBS($task['WBS']);
        }
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }

    /*
     * Gets the id the task's parent project
     */

    public function get_project_id() {
        if ($this->initialized == false) {
            return false;
        }
        return $this->project_id;
    }

    /*
     * Uninitializes the model
     */

    public function close() {
        unset($this->project_id);
        unset($this->task_id);
        unset($this->user_id);
        $this->initialized = false;
    }

    /*
     * Normalizes a WBS retrieved from SQL Database by removing leading zeros from
     * each outline level.
     * @param string $WBS The WBS to be normalized.
     * @return string The normalized WBS.
     */
    public function normalize_WBS($WBS) {
        $array = array();
        $temp = $WBS;
        while (($pos = strpos($temp, ".")) !== false) { //find first '.'
            array_push($array, substr($temp, 0, $pos)); //add characters before '.' to array
            $temp = substr($temp, $pos + 1); //continue with string remaining after '.'
        }
        array_push($array, $temp); //add last outline level

        $num = count($array);
        $normWBS = "";
        for ($i = 0; $i < $num; $i++) {
            $normWBS .= intval($array[$i]); //convert to int to remove leading zeros, append.
            if ($i < ($num - 1)) { //check if last outline level
                $normWBS.= "."; //more levels, append '.'
            }
        }
        return $normWBS;
    }

    /**
     * Converts WBS for storage in SQL Database by appending zeros to each outline level
     * such that each outline level has four digits. Allows for SQL sorting.
     * @param string $WBS The WBS to be converted.
     * @return string The converted WBS.
     */
    public function convert_WBS($WBS) {
        $array = array();
        $temp = $WBS;
        while (($pos = strpos($temp, ".")) !== false) { //find first '.'
            array_push($array, substr($temp, 0, $pos)); //add characters before '.' to array
            $temp = substr($temp, $pos + 1); //continue with string remaining after '.'
        }
        array_push($array, $temp); //add last outline level

        $num = count($array);
        $converted_WBS = "";
        for ($i = 0; $i < $num; $i++) {
            $chars = strlen($array[$i]);
            for ($i2 = $chars; $i2 < 4; $i2++) {
                $converted_WBS.= "0";
            }
            $converted_WBS.= $array[$i];
            if ($i < ($num - 1)) { //check if last outline level
                $converted_WBS.= "."; //more levels, append '.'
            }
        }
        return $converted_WBS;
    }

    /*
     * Removes commas from user-entered currency
     */
    private function format_currency($string) {
        return str_replace(",", "", $string);
    }

}

?>
