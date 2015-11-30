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
class Risk_model extends CI_Model {

    private $risk_id;
    private $task_id;
    private $project_id;
    private $user_id;
    private $initialized = false;

    public function __construct() {
        parent::__construct();
    }

    /*
     * Associates the model with a specific risk
     * @param $risk_id The id of the risk
     * @param $user_id The id of the user accessing the risk
     * @return false if failure, otherwise returns string containing user's permission
     */

    public function initialize($risk_id, $user_id) {
        if (!isset($user_id) || !isset($risk_id))
            return false;
        //check if risk exists and lookup project_id and task_id
        $query = $this->db->select('project_id, task_id')->from('view_risks')->
                        where('risk_id', $risk_id)->limit(1)->get();
        if ($query->num_rows < 1)
            return false;
        $this->user_id = $user_id;
        $this->risk_id = $risk_id;
        $row = $query->row_array();
        $this->project_id = $row['project_id'];
        $this->task_id = $row['task_id'];
        //lookup user permission
        $permission = $this->Project_model->get_user_permission($user_id, $this->project_id);
        if ($permission === false) {
            $this->close();
            return false;
        }
        $this->initialized = true;
        return $permission;
    }

    /**
     * Returns all fields associated with the risk in an associative array. Note that the
     * model must be initialized before this function can be executed.
     * @return array An associative array containing the fetched values.
     */
    public function get($select = "*") {
        if (!$this->initialized)
            return false;
        $query = $this->db->select($select)->from('view_risks_test')->where('risk_id', $this->risk_id)->limit(1)->get();
        $data = $query->row_array();
        if (isset($data['WBS'])) {
            $this->load->model('Task_model');
            $data['WBS'] = $this->Task_model->normalize_WBS($data['WBS']);
        }
        return $data;
    }

    public function get_all_media($select = "*") {
        if (!$this->initialized)
            return false;
        $query = $this->db->select($select)->from('view_risks_test')->where('risk_id', $this->risk_id)->get();
        $data = $query->row_array();
        if (isset($data['WBS'])) {
            $this->load->model('Task_model');
            $data['WBS'] = $this->Task_model->normalize_WBS($data['WBS']);
        }
        return $data;
    }

    /**
     * Edits fields associated with the risk (event, date of concern, date closed). The model must
     * be initialized with a user_id before this function can be executed.
     * @param array $data An associative array containing the keys and values to be updated.
     * @return boolean True on successful update, false if otherwise.
     */
    public function edit(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        //remove excess data from array
        $keys = array('event', 'date_of_concern', 'date_closed', 'type', 'impact',
            'probability', 'impact_effect', 'days_delay', 'cost_impact', 'impact_discussion',
            'date_identified', 'urgent', 'img_url');
        foreach ($data as $key => $val)
            if (!in_array($key, $keys))
                unset($data[$key]);
        $data['date_of_update'] = date('Y-m-d');
        if (isset($data['cost_impact']))
            $data['cost_impact'] = $this->format_currency($data['cost_impact']);
        
        if ( isset($data['date_closed']) && !strtotime($data['date_closed']) ) {
            if ( $data['date_closed'] == "0000-00-00" || $data['date_closed'] == "") {
                unset($data['date_closed']);
                $data['date_closed'] = null;
            }
        }

        if ( isset($data['date_of_concern']) && !strtotime($data['date_of_concern']) ) {
            if ( $data['date_of_concern'] == "0000-00-00" || $data['date_of_concern'] == "") {
                unset($data['date_of_concern']);
                $data['date_of_concern'] = null;
            }
        }
        
        if ( isset($data['date_closed']) && isset($data['date_identified']) && strtotime($data['date_closed']) < strtotime($data['date_identified']) ) {
            unset($data['date_closed']);
            $data['date_closed'] = date('Y-m-d');
        }

        if ( isset($data['probability']) ) { 
            if ( $data['probability'] > 100 || $data['probability'] < 0 )  {
                $data['probability'] = 0;
            }    
        }

        if ( isset($data['impact_effect']) ) { 
            if ( $data['impact_effect'] > 100 || $data['impact_effect'] < 0 )  {
                $data['impact_effect'] = 0;
            }    
        }

        //recalculate field values as necessary
        if (isset($data['impact_effect']) && isset($data['probability']))
            $data['expected_cost'] = ($data['probability'] * $data['cost_impact']) / 100;
        if (isset($data['cost_impact']) && isset($data['probability']))
            $data['overall_impact'] = ($data['probability'] * $data['impact_effect']) / 100;
        
        if (isset($data['probability']) && $data['probability'] != 0) {
            $num_days_delay = floatval($data['days_delay']);
            $num_probability = floatval($data['probability']) / 100;
            $data['expected_delay'] = $num_days_delay * $num_probability;
        }

        if ($data['probability'] == 0) {
            $data['expected_delay'] = 0;
        }
        // $num_days_delay = floatval($data['days_delay']);
        // $num_probability = floatval($data['probability']) / 100;
        // $data['expected_delay'] = $num_days_delay * $num_probability;

        if (!$this->db->where('risk_id', $this->risk_id)->update('risks', $data))
            return false;
        $this->update_priority_effect();
        $this->update_priority_monetary();
        $this->update_priority_days();
        return true;
    }

    public function update_adjusted_cost($user_id, $risk_id) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;

        $response_data = $this->Response_model->get_all($risk_id, $user_id);
        if ($responses_data === false)
            return false;
        
        $risk_data = $this->get();
        $risk_probability = $risk_data['probability'] / 100;
        $risk_cost_impact = $risk_data['cost_impact'];
        $sum_of_all_post_response_times_probability = 0;
        $factor = 0;
        $count = sizeof($response_data);
        foreach (array_slice($response_data, 0, $count - 1) as $item) {
            if ($item['release_progress'] !== 'Cancelled' && $item['post_response'] != 0) {
                $factor = $risk_cost_impact - $item['post_response'];
                $sum_of_all_post_response_times_probability += ( $risk_probability * $factor ); 
            }
        }

        $data['adjusted_cost'] = $risk_data['expected_cost'] - $sum_of_all_post_response_times_probability;
        if (isset($data['adjusted_cost']))
            $data['adjusted_cost'] = $this->format_currency($data['adjusted_cost']);
        
        if (!$this->db->where('risk_id', $this->risk_id)->update('risks', $data))
            return false;
        return true;
    }

    public function update_lessons_learned(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        
        //remove excess data from array
        $keys = array('event', 'date_closed', 'probability', 'cause');
        foreach ($data as $key => $val)
            if (!in_array($key, $keys))
                unset($data[$key]);
        $data['date_of_update'] = date('Y-m-d');
        
        if(isset($data['date_closed'])) {
            $is_date = strtotime($data['date_closed']);
            if($is_date === false) {
                $data['date_closed'] = date('Y-m-d');
            } else {
                $data['date_closed'] = date('Y-m-d', strtotime($data['date_closed']));
            }
        } else {
            $data['date_closed'] = date('Y-m-d');
        }

        if ( is_null($data['cause']) || empty($data['cause']) || !isset($data['cause']) ) {
            unset($data['cause']);
        }   
        //FOR OUTPUTTING TO NETWORK PREVIEW CHROME        
        //ob_start();
        //var_dump("Line: " . $data['date_closed']);
        //$out = ob_get_clean();
        //echo $out;
                    
        if (isset($data['probability']) && $data['probability'] != 0) {
            $num_days_delay = floatval($data['days_delay']);
            $num_probability = floatval($data['probability']) / 100;
            $data['expected_delay'] = $num_days_delay * $num_probability;
        }

        if ($data['probability'] == 0) {
            $data['expected_delay'] = 0;
        }
        // $num_days_delay = floatval($data['days_delay']);
        // $num_probability = floatval($data['probability']) / 100;
        // $data['expected_delay'] = $num_days_delay * $num_probability;

        if (!$this->db->where('risk_id', $this->risk_id)->update('risks', $data))
            return false;
        $this->update_priority_effect();
        $this->update_priority_monetary();
        $this->update_priority_days();
        return true;
    }

    public function update_risk_occurrence(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        
        //remove excess data from array
        $keys = array('occurred');
        foreach ($data as $key => $val)
            if (!in_array($key, $keys))
                unset($data[$key]);
        $data['date_of_update'] = date('Y-m-d');

        //probablity is set to 100 if risk occurred  
        if (isset($data['occurred']) && $data['occurred'] === 'yes')
            $data['probability'] = 100;
        //probablity is set to 100 if risk occurred
        if (isset($data['occurred']) && $data['occurred'] === 'no')
            $data['probability'] = 0;

        if (isset($data['probability']) && $data['probability'] != 0) {
            $num_days_delay = floatval($data['days_delay']);
            $num_probability = floatval($data['probability']) / 100;
            $data['expected_delay'] = $num_days_delay * $num_probability;
        }

        if ($data['probability'] == 0) {
            $data['expected_delay'] = 0;
        }
        // $num_days_delay = floatval($data['days_delay']);
        // $num_probability = floatval($data['probability']) / 100;
        // $data['expected_delay'] = $num_days_delay * $num_probability;

        if (!$this->db->where('risk_id', $this->risk_id)->update('risks', $data))
            return false;
        $this->update_priority_effect();
        $this->update_priority_monetary();
        $this->update_priority_days();
        return true;
    }

    public function upload_media(array $data) {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        
        //remove excess data from array
        $keys = array('img_url');

        foreach ($data as $key => $val)
            if (!in_array($key, $keys))
                unset($data[$key]);
        $data['date_of_update'] = date('Y-m-d');
            
        if (!$this->db->where('risk_id', $this->risk_id)->update('risks', $data))
            return false;
        
        return true;
    }

    /**
     * Deletes the risk and all associated updates, responses, and response updates.
     * Also uninitializes the Model since the risk no longer exists. The model must
     * be initialized with a user_id before this function can be executed.
     * @return boolean True if successful, false if unsuccessful.
     */
    public function delete() {
        if (!$this->initialized)
            return false;
        if (!$this->Project_model->modify($this->user_id, $this->project_id))
            return false;
        return $this->db->from('risks')->where('risk_id', $this->risk_id)->limit(1)->delete();
    }

    /**
     * Creates a new risk.
     * @param array $data The values to be inserted to the risk. Note that the keys
     * 'task_id' must be included.
     * @param integer $user_id The id of the user creating the risk.
     * @return boolean true if successful, false if unsuccessful.
     */
    public function create(array $data, $user_id) {
        if (!isset($data['task_id']) || !isset($user_id))
            return false;
        //check if task_id exists
        $query = $this->db->from('tasks')->where('task_id', $data['task_id'])->limit(1)->get();
        if ($query->num_rows < 1)
            return false;
        $row = $query->row_array();
        //check if user can modify project
        if ($this->Project_model->modify($user_id, $row['project_id']) == false)
            return false;
        //remove excess data from array
        $keys = array('task_id', 'event', 'date_of_concern', 'date_closed',
            'type', 'impact', 'probability', 'impact_effect', 'days_delay' , 'cost_impact', 'impact_discussion');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        if (isset($data['cost_impact']))
            $data['cost_impact'] = $this->format_currency($data['cost_impact']);
        //set dates
        $data['date_identified'] = date('Y-m-d');
        $data['date_of_update'] = date('Y-m-d');

        if ( isset($data['date_closed']) && strtotime($data['date_closed']) < strtotime($data['date_identified']) ) {
            unset($data['date_closed']);
            $data['date_closed'] = date('Y-m-d');
        }

        if ( isset($data['date_closed']) && !strtotime($data['date_closed']) ) {
            if ( $data['date_closed'] == "0000-00-00" || $data['date_closed'] == "") {
                unset($data['date_closed']);
                $data['date_closed'] = null;
            }
        }

        if ( isset($data['probability']) ) { 
            if ( $data['probability'] > 100 || $data['probability'] < 0 )  {
                $data['probability'] = 0;
            }    
        }

        if ( isset($data['impact_effect']) ) { 
            if ( $data['impact_effect'] > 100 || $data['impact_effect'] < 0 )  {
                $data['impact_effect'] = 0;
            }    
        }
        
        if (isset($data['probability']) && $data['probability'] != 0) {
            $num_days_delay = floatval($data['days_delay']);
            $num_probability = floatval($data['probability']) / 100;
            $data['expected_delay'] = $num_days_delay * $num_probability;
        }

        if ($data['probability'] == 0) {
            $data['expected_delay'] = 0;
        }
        // $num_days_delay = floatval($data['days_delay']);
        // $num_probability = floatval($data['probability']) / 100;
        // $data['expected_delay'] = $num_days_delay * $num_probability;

        if ($this->db->insert('risks', $data)) {
            $insert_id = $this->db->insert_id();
            $this->initialize($insert_id, $user_id);
            $this->update_priority_effect();
            $this->update_priority_monetary();
            $this->update_priority_days();
            return $insert_id;
        } else {
            return false;
        }
    }

    /*
     * Retrieves all risks associated with a particular task
     */

    public function get_all_by_task($task_id, $user_id, $limit = null, $offset = 0, $order_by = null, $direction = 'ASC') {
        //load task model to check if user can access project
        $this->load->model('Task_model');
        if (!$this->Task_model->initialize($task_id, $user_id))
            return false;
        $this->db->select('SQL_CALC_FOUND_ROWS * ', false)->from('view_risks')->where('task_id', $task_id);
        if (isset($limit) && $limit != 0)
            $this->db->limit($limit, $offset);
        if (isset($order_by)) {
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        foreach ($data as &$risk) {
            $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of risks retrieved
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        $this->Task_model->close();
        return $data;
    }
    /*
     * Gets up to three risks whose date of concern is closest to today's date (in the future) in the project
     */
    public function get_upcoming($project_id, $user_id) {
        //check if user can access project
        if (!$this->Project_model->get_user_permission($user_id, $project_id))
            return false;
        $query = $this->db->select('*', false)->from('view_risks')->where('project_id', $project_id)
                        ->limit(3)->order_by('date_of_concern')->where('date_of_concern >=', date('Y-m-d'))->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$risk) {
            $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = 3;
        return $data;
    }
    /*
     * Gets up to three risks with the highest expected cost in the project
     */
    public function get_top_by_expected_cost($project_id, $user_id) {
        //check if user can access project
        if (!$this->Project_model->get_user_permission($user_id, $project_id))
            return false;
        $query = $this->db->select('*', false)->from('view_risks')->where('project_id', $project_id)
                        ->limit(3)->order_by('expected_cost', 'DESC')->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$risk) {
            $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = 3;
        return $data;
    }

    /*
     * Retrieves all risks associated with a particular project
     */

    public function get_all_by_project($project_id, $user_id, $limit = null, $offset = 0, $order_by = null, $direction = 'ASC', $select = "*") {
        //check if user can access project
        if (!$this->Project_model->get_user_permission($user_id, $project_id))
            return false;
        $this->db->select('SQL_CALC_FOUND_ROWS ' . $select, false)->from('view_risks_test')->where('project_id', $project_id);
        if (isset($limit) && $limit != 0)
            $this->db->limit($limit, $offset);
        if (isset($order_by)) {
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$risk) {
            if (isset($risk['WBS']))
                $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }
    public function get_all_causes_by_project($project_id, $user_id, $limit = null, $offset = 0, $order_by = null, $direction = 'ASC', $select = "*") {
        //check if user can access project
        if (!$this->Project_model->get_user_permission($user_id, $project_id))
            return false;
        $this->db->select('SQL_CALC_FOUND_ROWS ' . $select, false)->from('view_risks_by_cause')->where('project_id', $project_id);
        if (isset($limit) && $limit != 0)
            $this->db->limit($limit, $offset);
        if (isset($order_by)) {
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$risk) {
            if (isset($risk['WBS']))
                $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }
    /*
     * Gets all severe risks in a project
     */
    public function get_severe_by_project($project_id, $user_id, $limit = null, $offset = 0, $order_by = null, $direction = 'ASC') {
        //check if user can access project
        if (!$this->Project_model->get_user_permission($user_id, $project_id))
            return false;
        $this->db->select('SQL_CALC_FOUND_ROWS *', false)->from('view_risks')->where('project_id', $project_id)
                ->where('urgent', 1);
        if (isset($limit) && $limit != 0)
            $this->db->limit($limit, $offset);
        if (isset($order_by)) {
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$risk) {
            $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }
    /*
     * Gets all risks across all projects for which a user has permissions.
     * This function allows filtering by keywords. If the like_field and like_value parameters are set,
     * then this function will only return risks that contain the like_value string in the specified like_field.
     */
    public function get_all_by_user($user_id, $limit = null, $offset = 0, $order_by = null, $order_by_2 = null, $order_by_3 = null, $direction = 'ASC', $direction_2 = null, $like_field = null, $like_value = null) {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false)->from('view_risks_by_user_updated')->where('user_id', $user_id);
        if (isset($limit) && $limit != 0)
            $this->db->limit($limit, $offset);
        if (isset($order_by)) {
            $this->db->order_by($order_by, $direction);
            
            if (isset($order_by_2) && !isset($direction_2)) {
                $this->db->order_by($order_by_2, $direction);                   
            }
            
            if (isset($order_by_2) && isset($direction_2)) {
                $this->db->order_by($order_by_2, $direction_2);
            }

            if (isset($order_by_3)) {
                $this->db->order_by($order_by_3, $direction);
            }            
        }
        
        if (isset($like_field) && isset($like_value) && ($like_field == "event" || $like_field == "impact" || $like_field == "impact_discussion")) {
            $this->db->like($like_field, $like_value);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $this->load->model('Task_model');
        foreach ($data as &$risk) {
            $risk['WBS'] = $this->Task_model->normalize_WBS($risk['WBS']);
        }
        //get number of rows retrieved
        $data['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $data;
    }
    
    public function get_data_by_dates($project_id) {
        $query = $this->db->select('date_of_concern, COUNT(*) as risk_count, SUM(expected_cost) expected_cost')
                ->from('view_risks')
                ->where('project_id', $project_id)
                ->where('date_of_concern IS NOT NULL')
                ->where('date_of_concern != \'0000-00-00\'')
                ->group_by('date_of_concern')
                ->get();
        return $query->result_array();
    }

    /**
     * Gets all previous updates associated with the risk, ordered in descending order by date. This
     * function will not restore the most recent update, which is stored in the risks table. The
     * model must be initialized before this function can be executed.
     * @return array A two-dimensional array containing the updates (numeric, associative), or an empty
     * array if there are no updates.
     */
    public function get_updates($limit = null, $offset = 0) {
        if (!$this->initialized)
            return false;
        if (isset($limit))
            $this->db->limit($limit, $offset);
        $query = $this->db->select('*')->from('risk_updates')->
                        where('risk_id', $this->risk_id)->
                        order_by('date_of_update', 'desc')->get();
        return $query->result_array();
    }

    /**
     * Creates a new update for the risk. The model must
     * be initialized with a user_id before this function can be executed.
     * @param array The data to be inserted
     * @return mixed Integer containing the id of the inserted update on success, or false if
     * the insert was unsuccessful.
     */
    public function insert_update(array $data) {
        if (!$this->initialized) {
            return false;
        }
        if (!$this->Project_model->modify($this->user_id, $this->project_id)) {
            return false;
        }
        $keys = array('impact', 'probability', 'impact_effect', 'days_delay', 'cost_impact', 'impact_discussion');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        if (isset($data['cost_impact'])) {
            $data['cost_impact'] = $this->format_currency($data['cost_impact']);
        }
        $data['probability'] = intval($data['probability']);
        $data['cost_impact'] = intval($data['cost_impact']);
        $data['days_delay'] = floatval($data['days_delay']);
        $data['impact_effect'] = intval($data['impact_effect']);
        $data['date_of_update'] = date("Y-m-d");
        { //copy old update data from risk table, save to update table 
            $old_data = $this->get('date_of_update, impact, probability, impact_effect, cost_impact, days_delay,' .
                    'overall_impact, expected_cost, expected_delay, impact_discussion, priority_effect, priority_monetary, priority_days');
            //check if critical data was entered in previous update
            if (isset($old_data['impact']) || isset($old_data['probability']) ||
                    isset($old_data['impact_effect']) || isset($old_data['cost_impact']) || isset($old_data['days_delay']) ||
                    isset($old_data['overall_impact']) || isset($old_data['impact_discussion'])) {
                //transfer content
                $old_data['risk_id'] = $this->risk_id;
                if ($this->db->insert('risk_updates', $old_data) === false) {
                    return false;
                }
            }
        }
        if (isset($data['probability']) && $data['probability'] != 0) {
            $num_days_delay = floatval($data['days_delay']);
            $num_probability = floatval($data['probability']) / 100;
            $data['expected_delay'] = $num_days_delay * $num_probability;
        }

        if ($data['probability'] == 0) {
            $data['expected_delay'] = 0;
        }
        // $num_days_delay = floatval($data['days_delay']);
        // $num_probability = floatval($data['probability']) / 100;
        // $data['expected_delay'] = $num_days_delay * $num_probability;
        //update risk table with new update data
        if ($this->db->update('risks', $data, array('risk_id' => $this->risk_id))) {
            $insert_id = $this->db->insert_id();
            $this->update_priority_effect();
            $this->update_priority_monetary();
            $this->update_priority_days();
            return $insert_id;
        }
        else
            return false;
    }

    /**
     * Returns the project_id associated with the response. The model must be initialized
     * before this function can be executed.
     * @return integer The project_id associated with the response.
     */
    public function get_project_id() {
        if ($this->initialized === false) {
            return false;
        }
        return $this->project_id;
    }

    public function close() {
        unset($this->project_id);
        unset($this->task_id);
        unset($this->risk_id);
        unset($this->user_id);
        $this->initialized = false;
    }
    /*
     * Recalculate the priority_monetary field for all risks in the same project
     */
    private function update_priority_monetary() {
        return $this->prioritizer("expected_cost", "priority_monetary");
    }
    /*
     * Recalculate the priority_effect field for all risks in the same project
     */
    private function update_priority_effect() {
        return $this->prioritizer("overall_impact", "priority_effect");
    }
    /*
     * Recalculate the priority_days field for all risks in the same project
     */
    private function update_priority_days() {
        return $this->prioritizer("expected_delay", "priority_days");
    }
    /*
     * Calculates and updates priority ranks on a linear scale.
     */
    private function prioritizer($fieldname, $priority_name) {
        if (!$this->initialized)
            return false;
        $query = $this->db->
                        select('MAX(' . $fieldname . ') as max,
                        MIN(' . $fieldname . ') as min')->
                        from('view_risks')->
                        where('project_id', $this->project_id)->get();
        $extremes = $query->row_array();
        if ($extremes['max'] == $extremes['min']) {
            $this->db->where('project_id', $this->project_id)->update('view_risks', array($priority_name => 10));
        } else {
            /*
             * Custom query written because codeigniter functions will automatically add
             * escape characters around the calculation which invalidates the SQL query
             */
            $slope = 9 / ($extremes['max'] - $extremes['min']);
            $query = $this->db->query('UPDATE `view_risks` SET `' . $priority_name . '` = ' .
                    $slope . ' * (' . $fieldname . ' -' . $extremes['min'] . ') + 1 WHERE project_id = ' . $this->project_id);
        }
    }

    /*
     * Removes commas from user-entered currency
     */
    private function format_currency($string) {
        return str_replace(",", '', $string);
    }

}

?>
