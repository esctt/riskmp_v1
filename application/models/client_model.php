<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Client_model extends CI_Model {

    private $client_id;
    private $initialized = false;

    public function __construct() {
        parent::__construct();
    }

    public function initialize($client_id) {
        $this->client_id = $client_id;
        $this->initialized = true;
        return true;
    }
    /*
     * Checks whether the client has an active base_membership subscription.
     */
    public function confirm_subscription($client_id) {
        $this->load->model('Billing_model');
        $subscriptions = $this->Billing_model->get_client_subscription($client_id);
        if ($subscriptions == null) {
            return false;
        } else {
            //base membership could be larger than one if old subscription and new subscription by < 24 hours upon renewal
            return intval($subscriptions['base_membership']) >= 1;
        }
    }

    /**
     * Returns all users registered with the client.
     * @param integer $limit Specifies a limit on the number of fetched results.
     * @param integer $offset Specifies an offset in the results. Results retrived before this value will not be returned.
     * @param string $orderby Specifies a field name by which results will be sorted.
     * @param string $direction Specifies the direction (asc/dsc) in which the sorted results will be returned.
     * @return array A two-dimensional array (numeric->row, associative->fields).
     */
    public function get_users(array $where = null, $limit = null, $offset = null, $orderby = false, $direction = "asc") {
        if ($this->initialized !== true) {
            return false;
        }
        if ($orderby !== false) {
            $this->db->order_by($orderby, $direction);
        }
        $query = $this->db->select('*')->from('view_users')->where('client_id = ' . $this->client_id)->get();
        return $query->result_array();
    }

    /**
     * Updates client data in the database.
     * @param array $data An associative array containing the values to be updated, using field names as keys.
     * @return boolean True on success, false on failure.
     */
    public function update(array $data) {
        if ($this->initialized !== true) {
            return false;
        }
        foreach ($data as $key => $val) {
            $this->db->set($key, $val);
        }
        $this->db->where('client_id', $this->client_id);
        return $this->db->limit(1)->update('clients');
    }

    /**
     * Retrives the client's project
     * @param array $where Specifies a where clause for the query using an associative array.
     * @param integer $limit Specifies a limit on the number of fetched results.
     * @param integer $offset Specifies an offset in the results. Results retrived before this value will not be returned.
     * @param string $orderby Specifies a field name by which results will be sorted.
     * @param string $direction Specifies the direction (asc/dsc) in which the sorted results will be returned.
     * @return array A two-dimensional array (numeric->row, associative->fields).
     */
    public function get_projects(array $where = null, $limit = null, $offset = null, $orderby = false, $direction = "asc") {
        
    }

    /**
     * Gets fields associated with the client account.
     * @param string $select Specify the fields to be retrieved. Default: All.
     * @return type an associative array containing the fetched data.
     */
    public function get($select = "*") {
        if (!$this->initialized)
            return false;
        $query = $this->db->select($select)->get_where('view_clients', array('client_id' => $this->client_id), 1);
        return $query->row_array();
    }
    /*
     * Creates a new client account
     */
    public function insert_client(array $data) {
        $keys = array('company_name', 'phone', 'first_name', 'last_name', 'email', 'username', 'password', 'coupon_code');
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys))
                unset($data[$key]);
        }
        //check coupon code
        $coupon_query = $this->db->select('*')->from('promotion_codes')->where('code', $data['coupon_code'])
                        ->where('used', 0)->get();
        if ($coupon_query->num_rows() < 1)
            return array('success' => false, 'message' => "You have entered an invalid coupon code.");
        $coupon_code = $data['coupon_code'];
        unset($data['coupon_code']);
        $user_data = array('username' => $data['username'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'user_type' => 'Owner');
        if (!$this->User_model->username_unique($data['username']))
            return array('success' => false, 'message' => "The username is already in use. Please try a different username.");
        if (!$this->User_model->email_unique($data['email']))
            return array('success' => false, 'message' => "The email address is already in use. Please try a different email address.");
        $client_data = array('company_name' => $data['company_name'],
            'phone' => $data['phone']);
        if (!$this->db->insert('clients', $client_data))
            return array('success' => false, 'message' => "An unknown error has occurred. Please try again later.");
        $client_id = $this->db->insert_id();
        $user_data['salt'] = uniqid();
        $user_data['password'] = $this->User_model->get_digest($user_data['password'], $user_data['salt']);
        $user_data['client_id'] = $client_id;
        if ($this->db->insert('users', $user_data)) {
            //invalidate coupon code
            $this->db->update('promotion_codes', array('used' => 1), array('code' => $coupon_code), 1);
            return array('success' => true, 'data' => array('client_id' => $this->db->insert_id()));
        } else {
            $this->db->delete('clients', 'client_id = ' . $client_id, 1);
            return array('success' => false, 'message' => "An unknown error has occurred. Please try again later.");
        }
    }
    /*
     * Gets information about a client's active billing profile, if any. If the $lookup_paypal parameter is true
     * (or is not set), this function will also make a PayPal API call to obtain additional fields about the billing profile.
     */
    function get_client_profile($client_id, $lookup_paypal = true) {
        $query = $this->db->select('profile_id, amount, base_membership, num_additional_users, billingperiod')
                ->from('billing_profiles')
                ->where('client_id', $client_id)
                ->where('profile_status', 'Active')
                ->get();
        if ($query->num_rows < 1) {
            return null;
        } else {
            $row = $query->row_array();
            if (!$lookup_paypal) {
                return array('success' => true, 'data' => $row);
            }
            $this->load->library('paypal/Paypal_pro');
            $GRPPDFields = array('profileid' => $row['profile_id']);   // Profile ID of the profile you want to get details for.
            $PayPalRequestData = array('GRPPDFields' => $GRPPDFields);
            $PayPalResult = $this->paypal_pro->GetRecurringPaymentsProfileDetails($PayPalRequestData);
            if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
                return array('success' => false, 'errors' => $PayPalResult['ERRORS']);
            } else {
                return array('success' => true, 'data' => array_merge($row, $PayPalResult));
            }
        }
    }
    /*
     * Gets the client's current subscription, including base_membership and number of users
     */
    function get_client_subscription($client_id) {
        $query = $this->db->select('*')
                ->from('view_client_subscriptions')
                ->where('client_id', $client_id)
                ->limit(1)
                ->get();
        if ($query->num_rows() < 1) {
            return false;
        }
        return $query->row_array();
    }
    /*
     * Gets a client's current subscription that has been paid for and not received through promotions.
     */
    function get_client_paid_subscription($client_id) {
        $query = $this->db->select('*')
                ->from('view_client_paid_subscriptions')
                ->where('client_id', $client_id)
                ->limit(1)
                ->get();
        return $query->row_array();
    }

    /*
     * Checks if the user current has more users than is allowed under their current subscription. If so,
     * disables most recently registered accounts. Runs every time a user logs in for that client.
     */
    function verify_num_users($client_id) {
        $this->initialize($client_id);
        $cdata = $this->get('num_users');
        $subscription = $this->get_client_subscription($client_id);
        $user_diff = $cdata['num_users'] - ($subscription['num_additional_users'] + 1);
        if ($user_diff <= 0) {
            return;
        } else {
            //must disable users
            $this->db->where('client_id', $client_id)
                    ->where("user_type != 'Owner'")
                    ->order_by('user_date_registered', 'DESC')
                    ->set('status', 'Disabled')
                    ->limit($user_diff)
                    ->update('users');
        }
    }

    /*
     * Calculates how much the initial subscription charge will be for a client,
     * the regular monthly charge, the profile start date, and also their 
     * initial additional number of users and base membership (for pro-rating).
     * Also calculates regular base_membership, since it may be covered by an existing promotion.
     */
    function calculate_profile_amounts($client_id, $billingperiod, $num_additional_users) {
        $this->load->model('Billing_model');
        //lookup all promotions redeemed by the client, subtract from membership features
        $promotions = $this->Billing_model->get_client_promotions($client_id, false, false);
        $base_membership = 1;
        foreach ($promotions as $row) {
            //$num_additional_users-=$row['num_additional_users'];
            $base_membership -= $row['base_membership'];
        }
        if ($num_additional_users < 0) {
            $num_additional_users = 0;
        }
        if ($base_membership < 0) { //unnecessary check, should not be more than one base_membership in promotions.
            $base_membership = 0;
        }
        //check if membership has been completely covered by promotions
        if ($num_additional_users == 0 && $base_membership == 0) {
            //should check to make sure no billing profiles exist for the client
            return array(
                'success' => true,
                'profilestartdate' => null,
                'initamt' => 0,
                'base_membership' => 0,
                'num_users' => 0, 
                'init_base_membership' => 0,
                'init_num_users' => 0,
                'message' => 'No change to membership.');
        }
        $new_charge; //new monthly/annual charge
        if ($billingperiod == "Month") {
            $new_charge = $base_membership * MONTHLY_BASE_PRICE + $num_additional_users * MONTHLY_USER_PRICE;
        } else if ($billingperiod == "Year") {
            $new_charge = $base_membership * ANNUAL_BASE_PRICE + $num_additional_users * ANNUAL_USER_PRICE;
        } else {
            return array('success' => false, 'message' => 'Invalid billing period.');
        }
        //lookup existing recurring billing profile for the client, if any
        $existing_profile = $this->get_client_profile($client_id);
        $existing_paid_subscription = $this->get_client_paid_subscription($client_id);
        if ($existing_paid_subscription == null) { //if no existing profile
            return array(
                'success' => true,
                'amt' => $new_charge,
                'profilestartdate' => date("Y-m-d", strtotime("+1 " . $billingperiod)),
                'base_membership' => $base_membership,
                'num_additional_users' => $num_additional_users,
                'initamt' => $new_charge,
                'init_base_membership' => $base_membership,
                'init_num_additional_users' => $num_additional_users
            );
        } else {
            //check difference in features between new profile and old profile
            if ($existing_profile != null && $base_membership - $existing_profile['data']['base_membership'] == 0 && $num_additional_users - $existing_profile['data']['num_additional_users'] == 0) {
                //no change to billing profile
                return array(
                    'success' => true,
                    'profilestartdate' => null,
                    'initamt' => 0,
                    'base_membership' => 0,
                    'num_additional_users' => 0,
                    'init_base_membership' => 0,
                    'init_num_users' => 0,
                    'message' => "No changes made to membership.");
            }
            $base_membership_diff = $base_membership - $existing_paid_subscription['base_membership'];
            $num_users_diff = $num_additional_users - $existing_paid_subscription['num_additional_users'];
            //set negative feature differences equal to zero
            if ($base_membership_diff < 0) {
                $base_membership_diff = 0;
            }
            if ($num_users_diff < 0) {
                $num_users_diff = 0;
            }
            if ($num_users_diff || $base_membership_diff) { //check if feature difference not zero
                //initial amount and partial subscription required
                $time_diff = strtotime($existing_paid_subscription['expiry_date']) - time();
                if ($billingperiod == "Month") {
                    $month_diff = round($time_diff / (60 * 60 * 24 * 30.5), 1); //average month is 30.5 days
                    $initial_amt = round($month_diff * ($base_membership_diff * MONTHLY_BASE_PRICE + $num_users_diff * MONTHLY_USER_PRICE), 2);
                } else if ($billingperiod == "Year") {
                    $year_diff = round($time_diff / (60 * 60 * 24 * 365), 2);
                    $initial_amt = round($year_diff * ($base_membership_diff * ANNUAL_BASE_PRICE + $num_users_diff * ANNUAL_USER_PRICE), 2);
                }
                $start_date = date("Y-m-d", strtotime($existing_paid_subscription['expiry_date']));
                return array(
                    'success' => true,
                    'amt' => $new_charge,
                    'profilestartdate' => $start_date,
                    'base_membership' => $base_membership,
                    'num_additional_users' => $num_additional_users,
                    'initamt' => $initial_amt,
                    'init_base_membership' => $base_membership_diff,
                    'init_num_additional_users' => $num_users_diff
                );
            } else { //client is removing features, no need to prorate
                $start_date = date("Y-m-d", strtotime($existing_paid_subscription['expiry_date']));
                return array(
                    'success' => true,
                    'amt' => $new_charge,
                    'profilestartdate' => $start_date,
                    'base_membership' => $base_membership,
                    'num_additional_users' => $num_additional_users,
                    'initamt' => 0.00, //zero since remainder of this month is already covered by current subscription
                    'init_base_membership' => 0,
                    'init_num_additional_users' => 0
                );
            }
        }
    }
    /*
     * Updates a client's subscription
     */
    function update_subscription($client_id, $billingperiod, $num_additional_users, array $CCDetails, $firstname, $lastname) {
        $this->load->model('Billing_model');
        //check if a billing profile is pending for the client (ie. has been created but is still pending).
        if ($this->Billing_model->client_pending_profile($client_id)) {
            return array('success' => false, 'message' => 'You currently have a subscription request that is'
                . ' still pending. Please try again later.');
        }
        //calculate initial and regular payment amounts
        $amounts = $this->calculate_profile_amounts($client_id, $billingperiod, $num_additional_users);
        if (!$amounts['success']) { //check if error
            return array('success' => false, 'message' => $amounts['message']);
        } else if ($amounts['profilestartdate'] == null) {
            return array('success' => true, 'message' => $amounts['message']);
        }
        //prepare profile data
        $profile_data = array(
            'profilestartdate' => $amounts['profilestartdate'] . "T00:00:00\Z", //format for paypal
            'initamt' => $amounts['initamt'],
            'amt' => $amounts['amt'],
            'billingperiod' => $billingperiod,
            'amt' => $amounts['amt'],
            'CCDetails' => $CCDetails,
            'firstname' => $firstname,
            'lastname' => $lastname
        );
        //store initial values for prorated payment
        $init = array(
            'base_membership' => $amounts['init_base_membership'],
            'num_additional_users' => $amounts['init_num_additional_users']
        );
        //store base membership
        $base_membership = $amounts['base_membership'];
        //create profile
        $create = $this->Billing_model->CreateRecurringPaymentsProfile($profile_data);
        if (!$create['success']) { //check if failure
            log_message('error', 'Could not create billing profile.');
            return array('success' => false, 'message' => 'An error has occurred.', 'errors' => $create['errors']);
        } else {
            $profile_id = $create['data']['PROFILEID'];
            //get details about the profile
            $profile_lookup = $this->Billing_model->Get_recurring_payments_profile_details($profile_id);
            if (!$profile_lookup['success']) {//if unable to lookup newly created profile
                log_message('error', 'Could not look up newly created profile.');
                //cancel profiles to ensure client not billed
                $this->Billing_model->cancel_profile($profile_id, 'Cancelled due to server error');
                return array('success' => false, 'message' => 'A server error has occurred.');
            } else {
                $PayPalResult = $profile_lookup['PayPalResult'];
                //store new profile
                if (!$this->Billing_model->store_billing_profile($client_id, $PayPalResult, $base_membership, $num_additional_users, $init)) {
                    log_message('error', 'Could not store new billing profile.');
                    return array('success' => false, 'message' => 'A server error has occurred.');
                }
                if ($PayPalResult['STATUS'] == "Active") { //check if profile marked as active
                    if ($profile_data['initamt'] == 0) { //check if not waiting for an initial payment
                        //cancel old active profiles
                        $this->Billing_model->cancel_other_profiles($client_id, $PayPalResult['PROFILEID']);
                        return array('success' => true, 'message' => 'Your billing profile has been successfully updated.');
                    } else {
                        //old active profiles are NOT cancelled here since they will be cancelled by the initial transaction
                        //process the transaction
                        $data['initial_payment_txn_id'] = $create['data']['TRANSACTIONID'];
                        $data['recurring_payment_id'] = $profile_id;
                        $data['next_payment_date'] = $PayPalResult['NEXTBILLINGDATE'];
                        $this->Billing_model->process_initial_recurring_transaction($data);
                        return array('success' => true, 'message' => 'Your billing profile has been successfully updated.');
                    }
                } else if ($PayPalResult['STATUS'] == "Pending") { //check if profile pending
                    return array('success' => true, 'message' => 'Your payment is pending. This could take some time. When it is completed, your subscription will automatically be updated.', 'data' => $PayPalResult);
                } else { //error has occurred.
                    return array('success' => false, 'message' => 'An error has occurred.', 'errors' => $PayPalResult['ERRORS']);
                }
            }
        }
    }
    /*
     * Sends an email to the owner of the client account
     */
    function send_email($subject, $message_body, $client_id) {
        $this->initialize($client_id);
        $client_info = $this->get('company_name, first_name, last_name, email');
        $to = $client_info['email'];
        $message_top = "Dear " . $client_info['first_name'] . " " . $client_info['last_name'] . ",\n\n";
        $message_bottom = "\n\nRegards,\nThe RiskMP Team\n\nThis email was sent from an unmonitored mailbox." .
                " Please do not reply to this message.";
        $message = $message_top . $message_body . $message_bottom;
        exec("php /var/www.v1.riskmp.com/EmailSender.php \"" . $to . "\" \"" . 'admin@riskmp.com' . "\" \"" . $subject . "\" \"" . $message . "\"  > /dev/null 2>&1 &");
    }

}

?>
