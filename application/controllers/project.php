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

class Project extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function executive_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_executive_report');
    }
    
    public function risks_with_responses_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_risks_with_responses_report');
    }

    public function lessons_learned_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/lessons_learned_report');
    }

    public function short_lessons_learned_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/short_lessons_learned_report');
    }

    public function lessons_learned_pdf_risk_report($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
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
        $risks = $this->Risk_model->get_all_by_project($project_id, $user_id, $limit, $offset, $order_by = 'occurred', $direction = 'DESC');
        
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->library('fpdf');
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(40,10,'Lessons Learned Risk Report');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(15, "Project Name:");
        $pdf->Write(15, "  " . $project_data['project_name']);
        $pdf->Write(15, "                                                                                                                             " . date('Y-m-d'));
        //set table header and body fonts
        $thfont = array('family' => 'Arial', 'style' => 'B', 'size' => 11);
        $tbfont = array('family' => 'Arial', 'style' => '', 'size' => 11);
        $pdf->Ln(17);
        $twidth = array(50, 26, 40, 55, 22, 74);
        $theader = array('Risk Event', 'Date Closed', 'Impact', 'Impact Discussion', 'Occurred', 'Cause');
        $tdata = array();
        //Last item of array removed as it is not a risk
        $count = sizeof($risks);
        foreach (array_slice($risks, 0, $count - 1) as $item) {
            array_push($tdata, array($item['event'], $item['date_closed'], $item['impact'], $item['impact_discussion'], $item['occurred'], $item['cause']));
        }
        
        $pdf->create_table($theader, $tdata, $twidth, 'L', 'L', $thfont, $tbfont);
        return $pdf->Output();
        
    }

    public function task_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_tasks_report');
    }
    public function short_task_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_short_tasks_report');
    }

    public function short_task_pdf_report($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
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
        $risks = $this->Risk_model->get_all_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);

        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->library('fpdf');
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(40,10,'Tasks With Risks');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(15, "Project Name:");
        $pdf->Write(15, "  " . $project_data['project_name']);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Write(15, "                                                                                                                             " . date('Y-m-d'));
        //set table header and body fonts
        $thfont = array('family' => 'Arial', 'style' => 'B', 'size' => 11);
        $tbfont = array('family' => 'Arial', 'style' => '', 'size' => 11);
        $pdf->Ln(17);
        $twidth = array(40,50, 26, 45, 25, 25, 23, 20, 23);
        $theader = array('Task Name', 'Risk Event', 'Date Of Concern', 'Impact', 'Probability', 'Overall Impact', 'Expected Cost', 'Priority Effect', 'Priority ($)');
        $tdata = array();
        $count = sizeof($risks);
        foreach (array_slice($risks, 0, $count - 1) as $item) {
            array_push($tdata, array($item['task_name'], $item['event'], $item['date_of_concern'], $item['impact'], $item['probability'], $item['overall_impact'], '$' . (string)intval($item['expected_cost']), $item['priority_effect'], $item['priority_monetary'] ));
        }
        
        $pdf->create_table($theader, $tdata, $twidth, 'L', 'L', $thfont, $tbfont);
        return $pdf->Output();
        
    }

    public function risk_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_risks_report');
    }
    
    public function short_risk_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_short_risks_report');

    }

    public function short_risk_pdf_report($project_id, $offset = 0, $limit = 100, $order_by = 'risk_id', $direction = 'ASC') {
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
        $risks = $this->Risk_model->get_all_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);

        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->library('fpdf');
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(40,10,'Risk Identification Report');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(15, "Project Name:");
        $pdf->Write(15, "  " . $project_data['project_name']);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Write(15, "                                                                                                                             " . date('Y-m-d'));
        //set table header and body fonts
        $thfont = array('family' => 'Arial', 'style' => 'B', 'size' => 11);
        $tbfont = array('family' => 'Arial', 'style' => '', 'size' => 11);
        $pdf->Ln(17);
        $twidth = array(65, 26, 60, 25, 25, 23, 23, 23);
        $theader = array('Risk Event', 'Date Of Concern', 'Impact', 'Probability', 'Overall Impact', 'Expected Cost', 'Priority Effect', 'Priority ($)');
        $tdata = array();
        $count = sizeof($risks);
        foreach (array_slice($risks, 0, $count - 1) as $item) {
            array_push($tdata, array($item['event'], $item['date_of_concern'], $item['impact'], $item['probability'], $item['overall_impact'], '$' . (string)intval($item['expected_cost']), $item['priority_effect'], $item['priority_monetary'] ));
        }
        
        $pdf->create_table($theader, $tdata, $twidth, 'L', 'L', $thfont, $tbfont);
        return $pdf->Output();
        
    }

    public function response_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_responses_report');
    }
    
    public function short_response_report($project_id) {
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
        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->view('reports/report_header', $page_data);
        $this->load->view('reports/project_short_responses_report');
    }

    public function short_response_pdf_report($project_id = null, $offset = 0, $limit = 100, $order_by = 'response_id', $direction = 'ASC') {
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
        $this->load->model('Response_model');
        $responses = $this->Response_model->get_all_by_project($project_id, $user_id, $limit, $offset, $order_by, $direction);

        $project_data = $this->Project_model->get();
        $page_data = array('project_data' => $project_data);
        $this->load->library('fpdf');
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(40,10,'Response Planning Report');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Write(15, "Project Name:");
        $pdf->Write(15, "  " . $project_data['project_name']);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Write(15, "                                                                                                                             " . date('Y-m-d'));
        //set table header and body fonts
        $thfont = array('family' => 'Arial', 'style' => 'B', 'size' => 11);
        $tbfont = array('family' => 'Arial', 'style' => '', 'size' => 11);
        $pdf->Ln(17);
        $twidth = array(75, 70, 40, 25, 65);
        $theader = array('Risk Statement', 'Action Plan', 'Owner', 'Planned Closure', 'Current Status');
        $tdata = array();
        $count = sizeof($responses);
        foreach (array_slice($responses, 0, $count - 1) as $item) {
            array_push($tdata, array($item['risk_statement'], $item['action_plan'], $item['owner'], $item['planned_closure'], $item['current_status'] ));
        }
        
        $pdf->create_table($theader, $tdata, $twidth, 'L', 'L', $thfont, $tbfont);
        return $pdf->Output();
        
    }

    public function view($project_id = null, $tab = 1) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        if ($project_id == null) {
            redirect('dashboard');
        } else {
            $permission = $this->Project_model->initialize($project_id, $_SESSION['user_id']);
            if ($permission === false) {
                redirect('dashboard');
                return;
            }
            $admin = $permission == "Admin"; //denotes whether user has admin privledges for project
            $modify = $permission == "Write" || $admin; //denotes whether user can modify the project
            $project_data = $this->Project_model->get();

            $project_delay_data = $this->Project_model->get_expected_delay();
            
            $page_data = array('title' => $project_data['project_name'],
                'project_data' => $project_data,
                'project_delay_data' => $project_delay_data,
                'permission' => $permission,
                'modify' => $modify,
                'admin' => $admin,
                'tab' => $tab);
            $this->load->view('templates/header', $page_data);
            $this->load->view('project/view_project');
            $this->load->view('templates/footer');
        }
    }

    function create() {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $this->load->helper(array('form'));
        $data = array('title' => 'New Project');
        $this->load->view('templates/header', $data);
        $this->load->view('project/create_project');
        $this->load->view('templates/footer');
    }

    function create_form() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_name', 'Project Name', 'trim|xss_clean|required|callback_insert_project');
        if ($this->form_validation->run() == false) {
            $data = array('title' => 'New Project');
            $this->load->view('templates/header', $data);
            $this->load->view('project/create_project');
            $this->load->view('templates/footer');
        } else {
            redirect('dashboard');
        }
    }

    function insert_project($project_name) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        $data = array('project_name' => $project_name);
        if ($this->Project_model->create($data, $user_id) === false) {
            $this->form_validation->set_message('You do not have permission to create a new project.');
            return false;
        } else {
            return true;
        }
    }

    function edit($project_id) {
        if (!isset($project_id)) {
            return false;
        }
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission != "Owner" && $permission != "Admin" && $permission != "Write") {
            redirect('dashboard');
        }
        $project_data = $this->Project_model->get();
        $this->Project_model->close();
        $this->load->helper(array('form'));
        $data = array('title' => 'Edit Project', 'project_data' => $project_data);
        $this->load->view('project/edit_project', $data);
    }

    function edit_form($project_id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_name', 'Project Name', 'trim|xss_clean|required');
        $this->form_validation->set_rules('date_completed', 'date_completed', 'trim|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean|required|callback_edit_project[' . $project_id . ']');
        if ($this->form_validation->run() == false) {
            redirect('project/view/' . $project_id);
        } else {
            redirect('project/view/' . $project_id);
        }
    }

    function edit_project($status, $project_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $user_id = $_SESSION['user_id'];
        $permission = $this->Project_model->initialize($project_id, $user_id);
        if ($permission != "Owner" && $permission != "Admin" && $permission != "Write") {
            redirect('dashboard');
        }
        $project_name = $this->input->post('project_name');
        $date_completed = $this->input->post('date_completed');
        $data = array('project_name' => $project_name, 'status' => $status,
            'date_completed' => $date_completed);
        if ($this->Project_model->edit($data) === false) {
            $this->form_validation->set_message('You do not have permission to edit this project.');
            $this->Project_model->close();
            return false;
        } else {
            $this->Project_model->close();
            return true;
        }
    }

    public function import($project_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $permission = $this->Project_model->initialize($project_id, $_SESSION['user_id']);
        if ($permission != "Owner" && $permission != "Admin" &&
                $permission != "Write") {
            redirect('dashboard');
        }
        $project_data = $this->Project_model->get();
        $this->load->helper(array('form'));
        $data = array('project_data' => $project_data);
        $this->load->view('project/import', $data);
    }

    public function do_import($project_id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('pasteddata', '', 'callback_execute_import[' . $project_id . ']');
        if ($this->form_validation->run() == false) {
            $project_data = $this->Project_model->get();
            $this->load->helper(array('form'));
            $data = array('title' => 'Import Tasks', 'project_data' => $project_data);
            $this->load->view('templates/header', $data);
            $this->load->view('project/import');
            $this->load->view('templates/footer');
        } else {
            redirect('project/view/' . $project_id, 'refresh');
        }
    }

    function execute_import($pasteddata, $project_id) {
        $confirm_member = $this->User_model->confirm_member();
        if (!$confirm_member['success']) {
            $_SESSION['last_uri'] = $this->uri->uri_string(); $_SESSION['login_message'] = $confirm_member['message'];
            redirect('login');
        }
        $permission = $this->Project_model->initialize($project_id, $_SESSION['user_id']);
        if ($permission != "Owner" && $permission != "Admin" &&
                $permission != "Write") {
            redirect('dashboard');
        }
        $errors = $this->Project_model->import_tasks($pasteddata);
        if (count($errors) == 0) {
            return true;
        } else {
            var_dump($errors);
            $message = "Data successfully imported to database with some errors. \n";
            $message.= count($errors) . " errors occured. Please review errors below and resubmit these tasks.";
            foreach ($errors as $error) {
                $message.= "\n" . $error;
            }
            $this->form_validation->set_message('pasteddata', $message);
            return false;
        }
    }

}
