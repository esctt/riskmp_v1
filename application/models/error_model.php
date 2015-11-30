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
class Error_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function report_error($user_id, $report_text) {
        $data = array('user_id' => $user_id,
            'report_text' => $report_text);
        return $this->db->insert('error_reports', $data);
    }
    
}
?>