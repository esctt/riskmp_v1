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
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function dump() {
        log_message('error', "DUMP URL CALLED");
    }

    function login_view() {
        $confirm_member = $this->User_model->confirm_member(false);
        if ($confirm_member['success']) {
            redirect('dashboard');
        }
        $this->load->helper(array('form'));
        $body_background = "background:url(" . base_url('assets/images/basejumping.jpg') . ") no-repeat fixed;background-size: cover;";
        $data = array(
            'title' => "Login",
            'body_background' => $body_background,
            'opacity' => true,
            'hide_maximize' => true);
        $this->load->view('templates/header', $data);
        $this->load->view('user/login');
        $this->load->view('templates/footer');
    }

    // Policy agreement ~ users get redirected to agreement view if agreement hasn't been accepted
    // User regstration can not be complted without accepting the agreement. Manual registration 
    // of users by ESCTT requires the agreement acceptance value in DB to be reset.
    function agreement() {
        $confirm_member = $this->User_model->confirm_member(false);
        $user_data = $this->User_model->get();
        if ($confirm_member['success'] && $user_data['agreement_acceptance'] === 'accepted') {
            redirect('dashboard');
        }
        $this->load->helper(array('form'));
        $body_background = "background:url(" . base_url('assets/images/basejumping.jpg') . ") no-repeat fixed;background-size: cover;";
        $data = array(
            'title' => "User Agreement",
            'body_background' => $body_background,
            'opacity' => true,
            'hide_maximize' => true);
        $this->load->view('templates/header', $data);
        $this->load->view('user/agreement');
        $this->load->view('templates/footer');
    }

    function accepted_agreement() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $this->form_validation->set_message('accepted_agreement', "You are not logged in. Please log in and try again.");
            return false;
        }
        $data = array(
            'agreement_acceptance' => $this->input->post('agreement_acceptance')
        );
        $response = $this->User_model->user_agreement($_SESSION['user_id'], $data);
        if ($response['success']) {
            redirect('dashboard');
            return true;
        } else {
            $this->logout();
            redirect('login');
            // $this->form_validation->set_message('insert_user', $response['message']);
            // return false;
        }

    }

    function dashboard() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        //load user data
        $data['title'] = "My Dashboard";
        $this->User_model->initialize($_SESSION['user_id']);
        $data['userdata'] = $this->User_model->get('first_name ,last_name, email, username, phone, company_name');
        $this->load->view('templates/header', $data);
        $this->load->view('user/dashboard');
        $this->load->view('templates/footer');
    }

    public function global_risk_report() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->view('reports/report_header');
        $this->load->view('reports/global_risks_report');
    }
    
    public function short_global_risk_report() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->view('reports/report_header');
        $this->load->view('reports/short_global_risks_report');
    }

    public function short_global_risk_pdf_report($project_id, $offset = 0, $limit = 100, $order_by = 'project_name', $order_by_2 = 'occurred', $order_by_3 = 'expected_cost', $direction = 'ASC', $direction_2 = 'DESC') {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $permission = $this->Project_model->initialize($project_id, $_SESSION['user_id']);
        if ($permission === false) {
            redirect('dashboard');
            return;
        }
        
        $user_id = $_SESSION['user_id'];
        $this->load->model('Risk_model');
        $filter_field = $this->input->post('filter_field');
        $filter_value = $this->input->post('filter_value');
        $this->load->helper('security');
        xss_clean($filter_field);
        xss_clean($filter_value);
        $risks = $this->Risk_model->get_all_by_user($user_id, $limit, $offset, $order_by, $order_by_2, $order_by_3, $direction, $direction_2, $filter_field, $filter_value);
        
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->library('fpdf');
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(10,5,'Global Risk Report');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        // $pdf->Write(15, "Project Name:");
        // $pdf->Write(15, "  " . $project_data['project_name']);
        $pdf->Write(15, "     " . date('Y-m-d'));
        //set table header and body fonts
        $thfont = array('family' => 'Arial', 'style' => 'B', 'size' => 11);
        $tbfont = array('family' => 'Arial', 'style' => '', 'size' => 11);
        $pdf->Ln(17);
        $twidth = array(25, 42, 22, 22, 52, 23, 48, 22, 22);
        $theader = array('Project Name', 'Risk Event', 'Date of Concern', 'Occurred', 'Cause', 'Probability', 'Impact', 'Overall Impact', 'Expected Cost');
        $tdata = array();
        
        $count = sizeof($risks);
        foreach (array_slice($risks, 0, $count - 1) as $item) {
            array_push($tdata, array($item['project_name'], $item['event'], $item['date_of_concern'], $item['occurred'], $item['cause'], $item['probability'], $item['impact'], $item['overall_impact'], '$' . (string)intval($item['expected_cost'])));
        }
        
        $pdf->create_table($theader, $tdata, $twidth, 'L', 'L', $thfont, $tbfont);
        return $pdf->Output();
        
    }

    function do_login() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_verify_credentials');
        if ($this->form_validation->run() == false) {
            $body_background = "background:url(" . base_url('assets/images/basejumping.jpg') . ") no-repeat fixed;background-size: cover;";
            $data = array(
                'title' => "Login",
                'body_background' => $body_background,
                'opacity' => true,
                'hide_maximize' => true);
            $this->load->view('templates/header', $data);
            $this->load->view('user/login');
            $this->load->view('templates/footer');
        } else {
            if (isset($_SESSION['login_message'])) {
                unset($_SESSION['login_message']);
            }
            $this->User_model->initialize($_SESSION['user_id']);
            $this->load->model('User_model');
            $user_data = $this->User_model->get();
            if ($user_data['agreement_acceptance'] === 'accepted') {
                
                if (isset($_SESSION['last_uri'])) {
                    $uri = $_SESSION['last_uri'];
                    unset($_SESSION['last_uri']);
                    redirect($uri);
                } else {
                    redirect('dashboard');
                }
            }
            else {
                redirect('agreement'); 
            }    
        }
    }

    function logout() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            redirect('login');
        }
        $this->User_model->logout();
        redirect('login');
    }

    function verify_credentials($password) {
        $username = $this->input->post('username');
        $login = $this->User_model->login($username, $password);
        if ($login['success']) {
            return true;
        } else {
            $this->form_validation->set_message('verify_credentials', 'Invalid username or password');
            $_SESSION['login_message'] = $login['message'];
            return false;
        }
    }
public function register() {
        $this->load->helper(array('form'));
        $body_background = "background:url(" . base_url('assets/images/toronto.jpg') . ") no-repeat fixed;background-size: cover;";
        $data = array('title' => 'Register',
            'body_background' => $body_background,
            'opacity' => true,
            'hide_maximize' => true);
        $this->load->view('templates/header', $data);
        $this->load->view('user/register');
        $this->load->view('templates/footer');
    }

    public function insert_user($username) {
        $data = array(
            'username' => $username,
            'password' => $this->input->post('txt_password'),
            'first_name' => $this->input->post('txt_first_name'),
            'last_name' => $this->input->post('txt_last_name'),
            'email' => $this->input->post('txt_email'),
            'company_name' => $this->input->post('txt_company_name'),
            'phone' => $this->input->post('phone'),
            'agreement_acceptance' => $this->input->post('agreement_acceptance')
        );
        $response = $this->User_model->create($data);
        if ($response['success']) {
            return true;
        } else {
            $this->form_validation->set_message('insert_user', $response['message']);
            return false;
        }
    }

    function register_form() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('txt_first_name', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_last_name', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('txt_password', 'Password', 'trim|required|xss_clean|matches[txt_password_conf]');
        $this->form_validation->set_rules('txt_password_conf', 'Password Confirmation', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_username', 'Username', 'trim|required|xss_clean|callback_insert_user');
        $this->form_validation->set_rules('txt_company_name', 'Company Name', 'trim|xss_clean');
        $this->form_validation->set_rules('txt_phone', 'Phone', 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            session_start();
            $_SESSION['login_message'] = 'Account successfully created';
            redirect('login');
        } else {
                $this->load->helper(array('form'));
        $body_background = "background:url(" . base_url('assets/images/toronto.jpg') . ") no-repeat fixed;background-size: cover;";
            $data = array('title' => 'Register',
            'body_background' => $body_background,
            'opacity' => true,
            'hide_maximize' => true);
            $this->load->view('templates/header', $data);
            $this->load->view('user/register');
            $this->load->view('templates/footer');
        }
    }
    public function edit() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $data = array('title' => 'Edit User Information');
        $this->load->helper(array('form'));
        $this->User_model->initialize($_SESSION['user_id']);
        $data['user_data'] = $this->User_model->get('username, email, first_name, last_name, company_name, phone');
        $this->load->view('templates/header', $data);
        $this->load->view('user/edit_user');
        $this->load->view('templates/footer');
    }

    public function update_user($username) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $this->form_validation->set_message('update_user', "You are not logged in. Please log in and try again.");
            return false;
        }
        $data = array(
            'first_name' => $this->input->post('txt_first_name'),
            'last_name' => $this->input->post('txt_last_name'),
            'email' => $this->input->post('txt_email'),
            'phone' => $this->input->post('txt_phone'),
            'company_name' => $this->input->post('txt_company_name'),
            'username' => $username
        );
        //user editing own information
        $result = $this->User_model->edit($_SESSION['user_id'], $data);
        if ($result['success']) {
            return true;
        } else {
            $this->form_validation->set_message('update_user', $result['message']);
            return false;
        }
    }

    function edit_form() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('txt_first_name', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_last_name', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('txt_company_name', 'Company Name', 'trim|xss_clean');
        $this->form_validation->set_rules('txt_phone', 'Phone Number', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_username', 'Username', 'trim|required|xss_clean|callback_update_user');
        if ($this->form_validation->run()) {
            $_SESSION['first_name'] = $this->input->post('txt_first_name');
            $_SESSION['last_name'] = $this->input->post('txt_last_name');
            redirect('dashboard');
        } else {
            redirect('user/edit');
        }
    }

    function change_password() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        //user changing own password
        $this->load->helper(array('form'));
        $data = array('title' => 'Change Password');
        $this->load->view('templates/header', $data);
        $this->load->view('user/change_password');
        $this->load->view('templates/footer');
    }

    function forgot_password() {
        $data = array('title' => 'Forgot Password');
        $this->load->view('templates/header', $data);
        $this->load->view('user/forgot_password');
        $this->load->view('templates/footer');
    }

    function reset_password($reset_code) {
        $this->load->helper(array('form'));
        $data = array('title' => 'Reset Password',
            'reset_code' => $reset_code);
        $this->load->view('templates/header', $data);
        $this->load->view('user/reset_password');
        $this->load->view('templates/footer');
    }

    function reset_pwd_form($reset_code) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('set_pwd', 'Password', 'trim|required|xss_clean|matches[set_pwd_conf]');
        $this->form_validation->set_rules('set_pwd_conf', 'Password Confirmation', 'trim|required|xss_clean|callback_reset_pwd[' . $reset_code . ']');
        if ($this->form_validation->run()) {
            redirect('login');
        } else {
            $this->load->helper(array('form'));
            $data = array('title' => 'Reset Password',
                'reset_code' => $reset_code);
            $this->load->view('templates/header', $data);
            $this->load->view('user/reset_password');
            $this->load->view('templates/footer');
        }
    }

    function reset_pwd($password, $reset_code) {
        $reset = $this->User_model->reset_password($reset_code, $password);
        if ($reset['success']) {
            return true;
        } else {
            $this->form_validation->set_message('reset_pwd', $reset['message']);
            return false;
        }
    }

    function change_pwd_form() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('set_pwd', 'Password', 'trim|required|xss_clean|matches[set_pwd_conf]');
        $this->form_validation->set_rules('old_pwd', 'Old Password', 'trim|required|xss_clean|callback_verify_old_pwd');
        $this->form_validation->set_rules('set_pwd_conf', 'Password Confirmation', 'trim|required|xss_clean|callback_change_pwd');
        if ($this->form_validation->run() == false) {
            $data = array('title' => 'Change Password');
            $this->load->view('templates/header', $data);
            $this->load->view('user/change_password');
            $this->load->view('templates/footer');
        } else {
            redirect('dashboard');
        }
    }

    function verify_old_pwd($old_pwd) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $this->form_validation->set_message('verify_old_pwd', "You are not logged in. Please log in and try again.");
            return false;
        }
        $verify_credentials = $this->User_model->verify_credentials($old_pwd, null, $_SESSION['user_id']);
        if ($verify_credentials['success'] === false) {
            $this->form_validation->set_message('verify_old_pwd', $verify_credentials['message']);
            return false;
        } else {
            return true;
        }
    }

    function change_pwd($pwd) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $this->form_validation->set_message('change_pwd', "You are not logged in. Please log in and try again.");
            return false;
        }
        $edit = $this->User_model->edit($_SESSION['user_id'], array('password' => $pwd));
        if ($edit['success']) {
            return true;
        } else {
            $this->form_validation->set_message('change_pwd', $edit['message']);
            return false;
        }
    }

    public function subscription_details() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->model('Billing_model');
        $subscriptions = $this->Billing_model->get_all_user_subscriptions($_SESSION['user_id']);
        $active_profiles = $this->Billing_model->get_user_active_profiles($_SESSION['user_id']);
        if (count($active_profiles)) {
            $has_profile = true;
            $profile = $active_profiles[0];
        } else {
            $has_profile = false;
            $profile = null;
        }
        $data = array(
            'title' => 'View Subscriptions',
            'subscriptions' => $subscriptions,
            'has_profile' => $has_profile,
            'profile' => $profile
        );
        if (!$this->User_model->verify_subscribed($_SESSION['user_id'])) {
            $data['notification'] = "You do not currently have an active membership. Please purchase a subscription or "
                    . "redeem a promotion to continue using RiskMP.";
        }
        $this->load->view('templates/header', $data);
        $this->load->view('user/view_subscriptions');
        $this->load->view('templates/footer');
    }

    public function new_subscription() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->model('Billing_model');
        if (count($this->Billing_model->get_user_active_profiles($_SESSION['user_id'])) > 0) {
            redirect('user/subscription_details');
        }
        $promotions = $this->Billing_model->get_user_promotions($_SESSION['user_id'], false);
        if (count($promotions)) {
            $promotion_expiry = $promotions[0]['expiry_date'];
        } else {
            $promotion_expiry = null;
        }
        $data = array(
            'title' => 'Update Billing Information',
            'promotion_expiry' => $promotion_expiry)
        ;
        $this->load->view('templates/header', $data);
        $this->load->view('user/new_subscription');
        $this->load->view('templates/footer');
    }

    public function receipt($transaction_id) {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->model('Billing_model');
        //verify owner of transaction
        $lookup = $this->Billing_model->get_transaction($transaction_id, $select = "user_id");
        if ($lookup['user_id'] !== $_SESSION['user_id']) {
            redirect('dashboard');
        }
        $this->Billing_model->get_receipt($transaction_id)->output();
    }

    public function cancel_membership() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $data = array(
            'title' => 'Cancel Membership'
        );
        $this->load->view('templates/header', $data);
        $this->load->view('user/cancel_membership');
        $this->load->view('templates/footer');
    }
    
}
?>