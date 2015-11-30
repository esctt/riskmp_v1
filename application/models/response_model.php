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

class Response_model extends CI_Model {

    private $user_id;
    private $response_id;
    private $project_id;
    private $risk_id;
    private $initialized = false;

    public function __construct() {
        parent::__construct();
    }

    public function initialize($response_id, $user_id) {
        if (!isset($user_id) || !isset($response_id)) {
            return false;
        }
        //check if response exists and lookup project_id
        $query = $this->db->select('project_id, risk_id')->from('view_responses')->where('response_id', $response_id)->limit(1)->get();
        if ($query->num_rows < 1) {
            return false;
        }
        $this->user_id = $user_id;
        $this->response_id = $response_id;
        $row = $query->row_array();
        $this->project_id = $row['project_id'];
        $this->risk_id = $row['risk_id'];
        $permission = $this->Project_model->get_user_permission($user_id, $this->project_id);
        if ($permission == false) {
            $this->close();
            return false;
        }
        $this->initialized = true;
        return $permission;
    }

    /**
     * Returns all fields associated with the response in an associative array. Note that the
     * model must be initialized before this function can be executed.
     * @return An associative array containing the fetched values.
     */
    public function get($select = "*") {
        if (!$this->initialized)
            return false;
        $query = $this->db->select($select)->from('view_responses')->where('response_id', $this->response_id)->limit(1)->get();
        $data = $query->row_array();
        if (isset($data['WBS'])) {
            $this->load->model('Task_model');
            $data['WBS'] = $this->Task_model->normalize_WBS($data['WBS']);
        }
        return $data;
    }

    /*
     * Gets all responses in the specified project
     */

    public function get_all_by_project($project_id, $user_id, $limit = null, $offset = 0, $order_by = null, $direction = 'ASC') {
        //check if user can access project
        if (!$this->Project_model->get_user_permission($user_id, $project_id))
            return false;
        $this->db->select('SQL_CALC_FOUND_ROWS *', false)
                ->from('view_responses')->where('project_id', $project_id);
        if ($limit !== null && $limit != 0) {
            $this->db->limit($limit, $offset);
        }
        if ($order_by !== null) {
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$response) {
            $response['WBS'] = $this->Task_model->normalize_WBS($response['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }

    /**
     * Edits fields associated with the response (action_plan, owner, release_progress, current_status). The model must
     * be initialized with a user_id before this function can be executed.
     * @param array $data An associative array containing the keys and values to be updated.
     * @return type True on successful update, false if otherwise.
     */
    public function edit(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        //filter excess data from array
        $keys = array('action_plan', 'owner', 'release_progress', 'date_of_plan',
            'action', 'current_status', 'planned_closure', 'cost', 'post_response');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        
        // $response_data = $this->get();
        // $this->load->model('Risk_model');
        // $send_risk_id = $response_data['risk_id'];
        // $send_user_id = $this->user_id;
        // $permission = $this->Risk_model->initialize($send_risk_id, $send_user_id);
        // if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
        //     return false;
        // }
        // $send_response_id = $this->response_id;
        // if ($this->Risk_model->update_adjusted_cost($send_response_id, $send_user_id, $send_risk_id) === false) 
        //     return false;
        // $this->Risk_model->close();
    
        $data['date_of_update'] = date('Y-m-d');
        if (isset($data['release_progress']) && $data['release_progress'] === 'Cancelled') {
            $data['post_response'] = 0;
            $data['cost'] = 0;
        } 
        if (isset($data['cost']))
            $data['cost'] = $this->format_currency($data['cost']);
        if (isset($data['post_response']))
            $data['post_response'] = $this->format_currency($data['post_response']);
        // return $this->db->where('response_id', $this->response_id)->update('responses', $data);
        
        if ( $this->db->where('response_id', $this->response_id)->update('responses', $data) ) {
            // Just to make sure that the get doesn't fetch values from before the update
            time_nanosleep(0, 500000000);
            $response_data = $this->get();

            $this->load->model('Risk_model');
            
            $send_risk_id = $response_data['risk_id'];
            $send_user_id = $this->user_id;
            
            $permission = $this->Risk_model->initialize($send_risk_id, $send_user_id);
            if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
                return false;
            }
            
            if ($this->Risk_model->update_adjusted_cost($send_user_id, $send_risk_id) === false) 
                return false;
            $this->Risk_model->close();

            return true;
        }
        else {
            return false;
        }    
    }

    public function update_lessons_learned(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        //filter excess data from array
        $keys = array('action_plan', 'release_progress', 'cause');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        $data['date_of_update'] = date('Y-m-d');
        
        
        if (isset($data['release_progress']) && $data['release_progress'] === 'Cancelled') {
            $data['post_response'] = 0;
            $data['cost'] = 0;
        }
        if (isset($data['cost']))
            $data['cost'] = $this->format_currency($data['cost']);
        if (isset($data['post_response']))
            $data['post_response'] = $this->format_currency($data['post_response']);


        if ( is_null($data['cause']) || empty($data['cause']) || !isset($data['cause']) ) {
            unset($data['cause']);
        } 
        return $this->db->where('response_id', $this->response_id)->update('responses', $data);
    }

    public function update_response_success(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        //filter excess data from array
        $keys = array('successful');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        $data['date_of_update'] = date('Y-m-d');
        //release progress is set to completed if action plan is successful is set 
        if ( isset($data['successful']) ) {
            if ($data['successful'] === 'yes' || $data['successful'] === 'no') {
                $data['release_progress'] = 'Complete';    
            }
        } 
        return $this->db->where('response_id', $this->response_id)->update('responses', $data);
    }

    /**
     * Deletes the response and all associated updates. Also uninitializes the Model since the
     * response no longer exists. The model must be initialized with a user_id before this function can be executed.
     * @return boolean True if successful, false if unsuccessful.
     */
    public function delete() {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        
        $response_data = $this->get();
        $send_risk_id = $response_data['risk_id'];
        $send_user_id =  $this->user_id;
        
        // return $this->db->from('responses')->where('response_id', $this->response_id)->limit(1)->delete();
        if($this->db->from('responses')->where('response_id', $this->response_id)->limit(1)->delete()) {
            
            $this->load->model('Risk_model');
            $permission = $this->Risk_model->initialize($send_risk_id, $send_user_id);
            if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
                return false;
            }
            if ($this->Risk_model->update_adjusted_cost($send_user_id, $send_risk_id) === false) 
                return false;
            $this->Risk_model->close();
            
            return true;
        }
    }

    /*
     * As the name implies, this function deletes a response update
     */
    public function delete_response_update($response_update_id, $user_id) {
        $query = $this->db->select('project_id')->from('view_response_updates')
                        ->where('response_update_id', $response_update_id)->limit(1)->get();
        if ($query->num_rows < 1)
            return array('success' => false, 'message' => 'This update no longer exists!');
        $data = $query->row_array();
        if (!$this->Project_model->modify($user_id, $data['project_id']))
            return array('success' => false, 'message' => 'You do not have permission to do this');
        if ($this->db->where('response_update_id', $response_update_id)->limit(1)->delete('response_updates'))
            return array('success' => true, 'message' => 'The item was successfully deleted.');
        else
            return array('success' => false, 'message' => 'This item could not be deleted. It may no longer exist.');
    }

    /**
     * Creates a new response.
     * @param array $data The values to be inserted to the response. Note that the keys
     * 'risk_id' and 'action_plan' must be included.
     * @param integer $user_id The id of the user creating the response.
     * @return boolean true if successful, false if unsuccessful.
     */
    public function create(array $data, $user_id) {
        if (!isset($data['risk_id']) || !isset($user_id) ||
                !isset($data['action_plan'])) {
            return false;
        }
        //get project_id and check if user can modify project
        $this->load->model('Risk_model');
        if ($this->Risk_model->initialize($data['risk_id'], $user_id) === false)
            return false;
        $project_id = $this->Risk_model->get_project_id();
        $this->Risk_model->close();
        if (!$this->Project_model->modify($user_id, $project_id))
            return false;
        //remove excess data from array
        $keys = array('risk_id', 'action_plan', 'owner', 'action',
            'release_progress', 'current_status', 'date_of_plan',
            'planned_closure', 'cost', 'post_response');
        foreach ($data as $key => $val)
            if (!in_array($key, $keys))
                unset($data[$key]);
        if (isset($data['release_progress']) && $data['release_progress'] === 'Cancelled') {
            $data['post_response'] = 0;
            $data['cost'] = 0;
        }

        if (isset($data['cost']))
            $data['cost'] = $this->format_currency($data['cost']);
        if (isset($data['post_response']))
            $data['post_response'] = $this->format_currency($data['post_response']);
        
        $data['date_of_update'] = date("Y-m-d");
        if ($this->db->insert('responses', $data)) {
            $insert_id = $this->db->insert_id();
            // return $insert_id;

            $send_risk_id = $data['risk_id'];
            $send_user_id = $user_id;
            
            $this->load->model('Risk_model');
            $permission = $this->Risk_model->initialize($send_risk_id, $send_user_id);
            if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
                return false;
            }
            if ($this->Risk_model->update_adjusted_cost($send_user_id, $send_risk_id) === false) 
                return false;
            $this->Risk_model->close();
            
            return $insert_id;
        } else {
            return false;
        }
    }

    /*
     * Retrieves all responses associated with the risk
     */

    public function get_all($risk_id, $user_id, $limit = null, $offset = 0, $order_by = null, $direction = 'ASC') {
        //load risk model to check if user can read from project
        $this->load->model('Risk_model');
        if ($this->Risk_model->initialize($risk_id, $user_id) == false) {
            return false;
        }
        if ($limit !== null && $limit != 0) {
            $this->db->limit($limit, $offset);
        }
        if ($order_by !== null) {
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->select('SQL_CALC_FOUND_ROWS *', false)->from('view_responses')->where('risk_id', $risk_id)->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$response)
            $response['WBS'] = $this->Task_model->normalize_WBS($response['WBS']);
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }

    /**
     * Gets all previous updates associated with the response, ordered in descending order by date. This
     * function will not restore the most recent update, which is stored in the response table. The
     * model must be initialized with a user_id before this function can be executed.
     * @return array A two-dimensional array containing the updates (numeric, associative), or an empty
     * array if there are no updates.
     */
    public function get_updates($limit = null, $offset = 0, $order_by = null, $direction = 'ASC') {
        if (!$this->initialized)
            return false;
        $this->db->select('SQL_CALC_FOUND_ROWS *', false)->from('response_updates')->
                where('response_id', $this->response_id)->
                order_by('date_of_update', 'desc');
        if (isset($limit) && $limit != 0)
            $this->db->limit($limit, $offset);
        if ($order_by !== null)
            $this->db->order_by($order_by, $direction);
        $query = $this->db->get();
        if ($query->num_rows < 1)
            return array('num_rows' => 0);
        $data = $query->result_array();
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }

    /**
     * Creates a new update for the response. The model must be initialized
     * with a user_id before executing this function.
     * @param string $current_status The current status of the response.
     * @return mixed Integer containing the id of the inserted update on success, or false if
     * the insert was unsuccessful.
     */
    public function insert_update($data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        $keys = array('current_status', 'cost', 'post_response', 'owner', 'release_progress', 'planned_closure', 'date_of_update');
        foreach ($data as $key => $val)
            if (!in_array($key, $keys))
                unset($data[$key]);
        
        if (isset($data['release_progress']) && $data['release_progress'] === 'Cancelled') {
            $data['post_response'] = 0;
            $data['cost'] = 0;
        }

        if (isset($data['cost']))
            $data['cost'] = $this->format_currency($data['cost']);
        if (isset($data['post_response']))
            $data['post_response'] = $this->format_currency($data['post_response']);
        if (!isset($data['date_of_update']))
            $data['date_of_update'] = date("Y-m-d"); {
            //copy old update data from response table, save to update table 
            $old_data = $this->get($keys);
            //check if critical data was entered in previous update
            if (isset($old_data['current_status']) || isset($old_data['cost']) || isset($old_data['post_response']) ||
                    isset($old_data['owner']) || isset($old_data['release_progress']) ||
                    isset($old_data['planned_closure'])) {
                //transfer content
                $old_data['response_id'] = $this->response_id;
                if (!$this->db->insert('response_updates', $old_data)) {
                    return false;
                }
            }
        }
        $insert_id = $this->db->insert_id();
        //update risk table with new update data
        if ($this->db->update('responses', $data, array('response_id' => $this->response_id))) {
            
            $response_data = $this->get();
            $send_risk_id = $response_data['risk_id'];
            $send_user_id = $this->user_id;

            $this->load->model('Risk_model');
            $permission = $this->Risk_model->initialize($send_risk_id, $send_user_id);
            if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
                return false;
            }
            if ($this->Risk_model->update_adjusted_cost($send_user_id, $send_risk_id) === false) 
                return false;
            $this->Risk_model->close();

            return $insert_id;
        }    
        else {
            return false;
        }
    }

    /**
     * Returns the project_id associated with the response. The model must be initialized
     * before this function can be executed.
     * @return integer The project_id associated with the response.
     */
    public function get_project_id() {
        if (!$this->initialized)
            return false;
        return $this->project_id;
    }

    /**
     * Returns the risk_id associated with the response. The model must be initialized
     * before this function can be executed.
     * @return integer The project_id associated with the response.
     */
    public function get_risk_id() {
        if (!$this->initialized)
            return false;
        return $this->risk_id;
    }

    /*
     * Removes association with a particular response
     */

    public function close() {
        unset($this->project_id);
        unset($this->risk_id);
        unset($this->response_id);
        unset($this->user_id);
        $this->initialized = false;
    }

    /*
     * Removes commas from user-entered currency
     */
    private function format_currency($string) {
        return str_replace(",", "", $string);
    }

}

?>
