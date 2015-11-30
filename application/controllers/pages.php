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

class Pages extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }
    
    public function home() {
        $this->User_model->confirm_member(false);
        $this->load->model('Page_model');
        $risk_schedule = $this->Page_model->get_risk_course_schedule();
        $page_data = array(
            'risk_schedule' => $risk_schedule);
        $this->load->view('frontend/home', $page_data);
    }
    
    public function about() {
        redirect('home');
        $this->User_model->confirm_member(false);
        $body_background = "background:url(".base_url('assets/images/city-background.jpg').") no-repeat fixed;background-size: cover;";
        $page_data = array(
            'title' => 'About',
            'body_background' => $body_background,
            'opacity' => true,
            'hide_maximize' => true);
        $this->load->view('templates/header', $page_data);
        $this->load->view('pages/about');
        $this->load->view('templates/footer');
    }
    
    public function installing() {
        $this->User_model->confirm_member(false);
        $page_data = array('title' => 'Installation Instructions');
        $this->load->view('templates/header', $page_data);
        $this->load->view('pages/installing');
        $this->load->view('templates/footer');
    }
}
?>