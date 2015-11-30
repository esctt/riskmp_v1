<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function register() {
        $confirm_member = $this->User_model->confirm_member();
        if ($confirm_member['success']) {
            redirect('dashboard');
        }
        $this->load->helper(array('form'));
        $body_background = "background:url(" . base_url('assets/images/toronto.jpg') . ") no-repeat fixed;background-size: cover;";
        $data = array('title' => 'Registration',
            'body_background' => $body_background,
            'opacity' => true,
            'hide_maximize' => true);
        $this->load->view('templates/header', $data);
        $this->load->view('client/register');
        $this->load->view('templates/footer');
    }

    public function register_client($username) {
        $confirm_member = $this->User_model->confirm_member();
        if ($confirm_member['success']) {
            redirect('dashboard');
        }
        $data = array(
            'company_name' => $this->input->post('txt_company_name'),
            'phone' => $this->input->post('txt_phone'),
            'username' => $username,
            'password' => $this->input->post('txt_password'),
            'first_name' => $this->input->post('txt_first_name'),
            'last_name' => $this->input->post('txt_last_name'),
            'email' => $this->input->post('txt_email'),
            'coupon_code' => $this->input->post('txt_coupon_code')
        );
        $this->load->model('Client_model');
        $response = $this->Client_model->insert_client($data);
        if ($response['success'] === true) {
            return true;
        } else {
            $this->form_validation->set_message('register_client', $response['message']);
            return false;
        }
    }

    function register_form() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('txt_company_name', 'Company Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_phone', 'Company Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_first_name', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_last_name', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('txt_password', 'Password', 'trim|required|xss_clean|matches[txt_password_conf]');
        $this->form_validation->set_rules('txt_password_conf', 'Password Confirmation', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_coupon_code', 'Coupon Code', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txt_username', 'Username', 'trim|required|xss_clean|callback_register_client');
        if ($this->form_validation->run()) {
            redirect('login');
        } else {
            $body_background = "background:url(" . base_url('assets/images/toronto.jpg') . ") no-repeat fixed;background-size: cover;";
            $data = array('title' => 'Registration',
                'body_background' => $body_background,
                'opacity' => true,
                'hide_maximize' => true);
            $this->load->helper(array('form'));
            $this->load->view('templates/header', $data);
            $this->load->view('client/register');
            $this->load->view('templates/footer');
        }
    }

    public function view_subscriptions() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($_SESSION['user_type'] != 'Owner') {
            redirect('dashboard');
        }
        $this->load->model('Billing_model');
        $subscriptions = $this->Billing_model->get_all_client_subscriptions($_SESSION['client_id']);
        $data = array(
            'title' => 'View Subscriptions',
            'subscriptions' => $subscriptions
        );
        if (!$this->User_model->verify_subscribed()) {
            $data['notification'] = "You do not currently have an active membership. Please purchase a subscription or "
                    . "redeem a promotion to continue using RiskMP.";
        }
        $this->load->view('templates/header', $data);
        $this->load->view('client/view_subscriptions');
        $this->load->view('templates/footer');
    }

    public function cancel_membership() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($_SESSION['user_type'] != 'Owner') {
            redirect('dashboard');
        }
        $data = array(
            'title' => 'Cancel Membership'
        );
        $this->load->view('templates/header', $data);
        $this->load->view('client/cancel_membership');
        $this->load->view('templates/footer');
    }

    public function update_billing() {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($_SESSION['user_type'] != 'Owner') {
            redirect('dashboard');
        }
        $this->load->model('Billing_model');
        $this->load->model('Client_model');
        $promotions = $this->Billing_model->get_client_promotions($_SESSION['client_id'], false, false);
        $existing_profile = $this->Client_model->get_client_profile($_SESSION['client_id'], false);
        $promo_base_membership = false; //denotes whether user already has a base_membership from a promotion
        $promo_num_users = 0;
        foreach ($promotions as $row) {
            if ($row['base_membership'] == 1) {
                $promo_base_membership = true;
            }
            $promo_num_users += intval($row['num_additional_users']);
        }
        $data = array('title' => 'Update Billing Information',
            'promotions' => $promotions,
            'existing_profile' => $existing_profile['success'] ? $existing_profile['data'] : null,
            'promo_base_membership' => $promo_base_membership,
            'promo_num_users' => $promo_num_users);
        $this->load->view('templates/header', $data);
        $this->load->view('client/update_billing');
        $this->load->view('templates/footer');
    }

    public function receipt($transaction_id) {
        $confirm_member = $this->User_model->confirm_member(false);
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string();
            $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($_SESSION['user_type'] != 'Owner') {
            redirect('dashboard');
        }
        //verify owner of transaction
        $this->load->model('Billing_model');
        $this->Billing_model->get_receipt($transaction_id)->output();
    }

}
