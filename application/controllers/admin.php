<?php

/*
 * CONTROLLER USED FOR ADMINISTRATION PURPOSES 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    /*
     * FUNCTION THAT CHECKS EVERY BILLING PROFILE IN THE DATABASE AND ENSURES THAT ITS 
     * STATUS MATCHES THE DATABASE
     */

    public function verify_profile_statuses() {
        print "<pre>\nVERIFYING THAT PROFILE STATUSES IN DATABASE MATCH PAYPAL STATUSES...";
        $query = $this->db->select('*')
                ->from('billing_profiles')
                ->get();
        $profile = $query->result_array();
        $this->load->model('Billing_model');
        foreach ($profile as $p) {
            $result = $this->Billing_model->Get_recurring_payments_profile_details($p['profile_id']);
            if (!$result['success']) {
                print "\nERROR LOOKING UP PROFILE: " . $p['profile_id'];
            } else {
                if ($result['PayPalResult']["STATUS"] != $p['profile_status']) {
                    print "\nSTATUS MISMATCH FOR PROFILE ID #" . $p['profile_id'] . ": PayPal=" . $result['PayPalResult']["STATUS"] . ", DB=" . $p['profile_status'];
                }
            }
        }
        print "\nDONE!</pre>";
    }

    /*
     * FUNCTION THAT VERIFIES THAT EACH CLIENT ONLY HAS A MAXIMUM OF ONE ACTIVE PROFILE
     */
    public function verify_client_active_profiles() {
        print "<pre>\nVERIFYING THAT EACH CLIENT ONLY HAS A MAXIMUM OF ONE ACTIVE PROFILE.";
        $query = $this->db->select('client_id, COUNT(*) AS num_active')
                ->from('billing_profiles')
                ->where('profile_status', 'Active')
                ->group_by('client_id')
                ->get();
        $result = $query->result_array();
        foreach ($result as $row) {
            if ($row['num_active'] > 1) {
                print "\nCLIENT WITH ID" . $row['client_id'] . " has " . $row['num_active'] . " ACTIVE BILLING PROFILES";
            }
        }
        print "\nDONE!</pre>";
    }

}
