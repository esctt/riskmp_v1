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
class Page_model extends CI_Model {
    
    private static $risk_course_id = 43;
    private static $risk_industry_id = 9;
    
    public function __construct() {
        $this->db_esctt = $this->load->database('esctt', true);
        // Pass reference of database to the CI-instance
        $CI =& get_instance();
        $CI->db_esctt =& $this->db_esctt;  

        parent::__construct();
    }
    
    /*
     * Gets the schedule of risk management courses from the ESCTT database
     */
    public function get_risk_course_schedule() {
	//$this->output->enable_profiler(TRUE);
        $query = $this->db_esctt->select(
                'cities.name as cityname,'
                . 'locations.name as locationname,'
                . 'locations.address as locationaddress,'
                . 'locations.url as locationurl,'
                . 'dates.date as date,'
                . 'sessions.sessionid as sessionid')
		->distinct()
                ->from('sessions')
		->where('(sessions.courseid=43 OR sessions.courseid=48 OR sessions.courseid=49)')
                ->where('sessions.display', 1)
                ->where('dates.date >= CURDATE()')
                ->join('locations', 'locations.locationid = sessions.locationid')
                ->join('cities', 'cities.cityid = locations.cityid')
                ->join('dates', 'dates.sessionid = sessions.sessionid')
                ->order_by('cityname', 'ASC')
                ->order_by('dates.date', 'ASC')
		->group_by('cityname')
                ->get();
        $data = $query->result_array();
        $schedule = array();
        foreach($data as $row) {
            $session = array(
                'city' => $row['cityname'],
                'location' => array(
                    'name' => $row['locationname'],
                    'address' => $row['locationaddress'],
                    'url' => $row['locationurl']
                ),
                'date' => date('M jS, Y',strtotime($row['date'])),
                'register_url' => "https://escomputertraining.com/register/index/industry/9/session/".$row['sessionid']."/lang/en"
                );
            array_push($schedule, $session);
        }
        return $schedule;
    }
}
