<?php

class Upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index()
	{
		$this->load->view('upload_form', array('error' => ' ' ));
	}

	function do_upload($risk_id)
	{       
//                $upload_path = realpath(APPPATH . '../uploads') /var/www/shhasan.ddns.net/uploads/;
		$config['upload_path'] = '/var/www.v1.riskmp.com/public_html/assets/images/uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10000KB';
		$config['max_width']  = '102400';
		$config['max_height']  = '76800';
    //$config['file_name'] = 'name';

		$confirm_member = $this->User_model->confirm_member(true, false);
        if (!$confirm_member['success']) {
            $this->print_jtable_error(self::ERROR_NOT_LOGGED_IN);
            return;
        }
    $user_id = $_SESSION['user_id'];
    $this->load->model('Risk_model');
    $permission = $this->Risk_model->initialize($risk_id, $user_id);
    if ($permission != "Admin" && $permission != "Owner" && $permission != "Write") {
      $this->print_jtable_error(self::ERROR_NO_EDIT_PERMISSION);
      return;
    }

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload()) {
			$error = array('error' => $this->upload->display_errors());

			$this->load->view('upload_form', $error);
		}
		else {
			$data = array('upload_data' => $this->upload->data());
               	// $this->load->view('upload_success', $data);
   			
   			$this->load->helper('security');
        	foreach ($data as &$val) {
            	xss_clean($val);
        	}
        	$previous_data = $this->Risk_model->get();
        	$media_items = explode(',', $previous_data['img_url']);
        	if ( sizeof($media_items) == 3) {
        		echo "<script>alert('More than 3 images for a risk are not permitted.'); window.location = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
            	return;
        	} 
        	if ( isset($previous_data['img_url']) && $previous_data['img_url'] != null && $previous_data['img_url'] != '') {
        		$risk_data['img_url'] = $previous_data['img_url'] . ',' . $data['upload_data']['file_name'];
        	}
        	else {
        		$risk_data['img_url'] = $data['upload_data']['file_name'];
        	}
        	if ($this->Risk_model->upload_media($risk_data) == false) {
            	echo "<script>alert('An ERROR occurred while uploading your file. Please try again.'); window.location = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
            	return;
        	} else {
            	echo "<script>alert('Upload Successful!'); window.location = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
            	return;	
        	}
		}
	}
}
?>
