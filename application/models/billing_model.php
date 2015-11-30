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

class Billing_model extends CI_Model {

    function __construct() {
        parent::__construct();
        // Load helpers
        $this->load->helper('url');

        // Load PayPal library
        $this->config->load('paypal');

        $config = array(
            'Sandbox' => $this->config->item('Sandbox'), // Sandbox / testing mode option.
            'APIUsername' => $this->config->item('APIUsername'), // PayPal API username of the API caller
            'APIPassword' => $this->config->item('APIPassword'), // PayPal API password of the API caller
            'APISignature' => $this->config->item('APISignature'), // PayPal API signature of the API caller
            'APISubject' => '', // PayPal API subject (email address of 3rd party user that has granted API permission for your app)
            'APIVersion' => $this->config->item('APIVersion')  // API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
        );

        // Show Errors
        if ($config['Sandbox']) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }

        $this->load->library('paypal/Paypal_pro', $config);
    }

    /*
     * Calculates how much the initial subscription charge will be for a user,
     * the regular monthly charge, the profile start date.
     */

    function calculate_profile_amounts($user_id, $billingperiod, $province) {
        //check if user already has billing profile
        if (count($this->Billing_model->get_user_active_profiles($user_id))) {
            return array(
                'success' => false,
                'profilestartdate' => null,
                'initamt' => 0,
                'message' => 'You already have an active subscription. Please cancel it before attempting to start a new one.'
            );
        }
        $new_charge; //new monthly/annual charge
        if ($billingperiod == "Month") {
            $new_charge = MONTHLY_PRICE;
        } else if ($billingperiod == "Year") {
            $new_charge = ANNUAL_PRICE;
        } else {
            return array('success' => false, 'message' => 'Invalid billing period.');
        }
        $tax_amts = unserialize(TAXAMT);
        if (!in_array($province, array_keys($tax_amts))) {
            $tax_percent = DEFAULT_TAX;
        } else {
            $tax_percent = $tax_amts[$province];
        }
        //lookup all subscriptions held by the user
        $exp_date = $this->get_user_subscription_expiry($user_id);
        if ($exp_date != null && $exp_date !== date("Y-m-d")) { //expiry is not today
            $start_date = $exp_date;
            $initamt = 0;
        } else {
            $start_date = date("Y-m-d", strtotime("+1 " . $billingperiod));
            $initamt = $new_charge;
        }
        return array(
            'success' => true,
            'amt' => $new_charge,
            'taxamt' => round($new_charge * $tax_percent, 2),
            'profilestartdate' => $start_date,
            'initamt' => round($initamt * (1 + $tax_percent), 2)
        );
    }

    /*
     * Updates a user's subscription
     */

    function create_billing_profile($user_id, $billingperiod, array $CCDetails, $firstname, $lastname, $province) {
        //check if user already has a billing profile
        $existing_profile = $this->get_user_active_profiles($user_id);
        if (count($existing_profile)) {
            return array('success' => 'false', 'message' => 'You already have an active subscription. Please cancel it before starting a new one.');
        }
        //check if a billing profile is pending for the user (ie. has been created but is still pending).
        if ($this->user_pending_profile($user_id)) {
            return array('success' => false, 'message' => 'You currently have a subscription request that is'
                . ' still pending. Please try again later.');
        }
        //calculate initial and regular payment amounts
        $amounts = $this->calculate_profile_amounts($user_id, $billingperiod, $province);
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
            'taxamt' => $amounts['taxamt'],
            'CCDetails' => $CCDetails,
            'firstname' => $firstname,
            'lastname' => $lastname
        );
        //create profile
        $create = $this->CreateRecurringPaymentsProfile($profile_data);
        if (!$create['success']) {
            //profile not created successfully
            log_message('error', 'Could not create billing profile.');
            return array('success' => false, 'message' => 'An error has occurred.', 'errors' => $create['errors']);
        } else {
            //profile successfully created. Lookup profile information
            $profile_id = $create['data']['PROFILEID'];
            $profile_lookup = $this->Get_recurring_payments_profile_details($profile_id);
            if (!$profile_lookup['success']) {
                //error getting profile information from paypal server
                log_message('error', 'Could not look up newly created profile.');
                //cancel profiles to ensure user not billed
                $this->cancel_profile($profile_id, 'Cancelled due to server error');
                return array('success' => false, 'message' => 'A server error has occurred.');
            } else {
                $PayPalResult = $profile_lookup['PayPalResult'];
                //store new profile
                if (!$this->store_billing_profile($user_id, $PayPalResult)) {
                    log_message('error', 'Could not store new billing profile.');
                    return array('success' => false, 'message' => 'A server error has occurred.');
                }
                //check if profile is active (ie. initial transaction has been completed)
                if ($PayPalResult['STATUS'] == "Active") {
                    if ($profile_data['initamt'] == 0) { //check if not waiting for an initial payment
                        return array('success' => true, 'message' => 'Your billing profile has been successfully updated.');
                    } else {
                        //process the transaction
                        $data['initial_payment_txn_id'] = $create['data']['TRANSACTIONID'];
                        $data['recurring_payment_id'] = $profile_id;
                        $data['next_payment_date'] = $PayPalResult['NEXTBILLINGDATE'];
                        $this->process_initial_recurring_transaction($data);
                        return array('success' => true, 'message' => 'Your billing profile has been successfully updated.');
                    }
                } else if ($PayPalResult['STATUS'] == "Pending") {
                    //profile still pending
                    return array('success' => true, 'message' => 'Your payment is pending. This could take some time. When it is completed, your subscription will automatically be updated.', 'data' => $PayPalResult);
                } else {
                    //profile not created successfully
                    return array('success' => false, 'message' => 'An error has occurred.', 'errors' => $PayPalResult['ERRORS']);
                }
            }
        }
    }

    /*
     * Redeems a promotional code
     */

    public function redeem_promotion($code, $user_id) {
        $query = $this->db->select('*')
                ->from('promotion_codes')
                ->where('code', $code)
                ->limit(1)
                ->get();
        if ($query->num_rows() < 1) {
            return array('success' => false, 'message' => 'You have entered an invald code.');
        }
        $promotion = $query->row_array();
        if ($promotion['used'] != 0) {
            return array('success' => false, 'message' => 'Code has already been redeemed.');
        }
        //prepare data for subscription
        $insert_data = array(
            'user_id' => $user_id,
            'coupon_id' => $promotion['coupon_id'],
            'months' => $promotion['months']
        );
        //check if user is already paying for a membership
        $profile = $this->db->select('*')
                ->from('billing_profiles')
                ->where('user_id', $user_id)
                ->where('profile_status', 'Active')
                ->get();
        if ($profile->num_rows() > 0) {
            return array('success' => false,
                'message' => 'You are currently paying for a membership with automatic billing. Please cancel this subscription, and then try using this code again.');
        }
        //check if user already has membership (from promotion or cancelled subscription)
        $subscription = $this->db->select('*')
                ->from('subscriptions')
                ->where('user_id', $user_id)
                ->where('expiry_date >= CURDATE()')
                ->order_by('expiry_date', 'DESC')
                ->get();
        if ($subscription->num_rows() > 0) {
            //already has membership
            $sub_data = $subscription->row_array();
            //append after the end of the last subscription
            $insert_data['date_of_redemption'] = $sub_data['expiry_date'];
            return $this->do_promotion_redeem($insert_data);
        } else {
            //user does not already have a subscription.
            $insert_data['date_of_redemption'] = date('Y-m-d');
            return $this->do_promotion_redeem($insert_data);
        }
    }

    /*
     * array $data must contain the following keys:
     * -user_id
     * -coupon_id
     * -date_of_redemption
     * -months
     */

    private function do_promotion_redeem(array $data) {
        $data['expiry_date'] = date('Y-m-d', strtotime($data['date_of_redemption'] . '+ ' . $data['months'] . ' month'));
        unset($data['months']);
        $data['amount_paid'] = 0.00;
        if ($this->db->insert('subscriptions', $data)) {
            //invalidate promotion code
            $this->db->where('coupon_id', $data['coupon_id'])
                    ->limit(1)
                    ->update('promotion_codes', array('used' => 1));
            return array('success' => true, 'message' => 'Promotion successfully redeemed.');
        } else {
            return array('success' => false, 'message' => 'An error has occurred. Please try again later.');
        }
    }

    /*
     * Gets information about a particular subscription from the database
     */

    public function get_subscription($subscription_id) {
        $query = $this->db->select('*')
                ->from('subscriptions')
                ->where('subscription_id', $subscription_id)
                ->limit(1)
                ->get();
        return $query->row_array();
    }

    /*
     * Gets information about a subscription by its transaction_id 
     */

    public function get_subscription_by_transaction($transaction_id) {
        $query = $this->db->select('*')
                ->from('subscriptions')
                ->where('billing_transaction_id', $transaction_id)
                ->limit(1)
                ->get();
        return $query->row_array();
    }

    //returns the date on which the last subscription currently held by the user will expire, or
    //null if the user does not have any active subscriptions
    public function get_user_subscription_expiry($user_id) {
        $query = $this->db->select('expiry_date')
                ->from('subscriptions')
                ->where('user_id', $user_id)
                ->where('expiry_date >= CURDATE()')
                ->order_by('expiry_date', 'DESC')
                ->limit(1)
                ->get();
        if ($query->num_rows()) {
            $row = $query->row_array();
            return $row['expiry_date'];
        } else {
            return null;
        }
    }

    /*
     * Returns all individual subscriptions held by the user, including those that have
     * not yet started.
     * $expired denotes whether or not to include subscriptions that have already expired
     */

    public function get_all_user_subscriptions($user_id, $select = "*", $expired = false) {
        $this->db->select($select)
                ->from('subscriptions')
                ->where('user_id', $user_id)
                ->order_by('expiry_date', 'DESC');
        if (!$expired) {
            $this->db->where('expiry_date >= CURDATE()');
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Gets all transactions for a user
     */

    public function get_user_transactions($user_id, $select = "*") {
        $this->db->select($select)
                ->from('billing_transactions')
                ->where('user_id', $user_id)
                ->order_by('order_time', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Gets any active promotions redeemed by the user
     * If user has subscription, returns details about subscription
     * $expired => denotes whether or not to include promotions that have expired
     * $redeemed => denotes whether or to include promotions that have not yet started.
     * 
     * Orders by expiry date descending.
     */

    function get_user_promotions($user_id, $expired = false, $redeemed = true) {
        $this->db->select('coupon_id, date_of_redemption, expiry_date')
                ->from('view_promotion_subscriptions')
                ->where('user_id', $user_id);
        if (!$expired) {
            $this->db->where('expiry_date >= CURDATE()');
        }
        if ($redeemed) {
            $this->db->where('date_of_redemption >= CURDATE()');
        }
        $this->db->order_by('expiry_date', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Determines whether the user has a pending billing profile (ie. a billing profile that has been completed
     * but not yet been marked as active by PayPal). Used to prevent duplicate profiles.
     * Returns true/false
     */

    function user_pending_profile($user_id) {
        return ($this->db->from('billing_profiles')
                        ->where('user_id', $user_id)
                        ->where('profile_status', 'Pending')
                        ->count_all_results() > 0);
    }

    /*
     * Gets information about the specified billing profile from the database
     */

    public function get_profile($profile_id, $select = "*") {
        $query = $this->db->select($select)
                ->from('billing_profiles')
                ->where('profile_id', $profile_id)
                ->limit(1)
                ->get();
        return $query->row_array();
    }

    /*
     * Updates a billing profile in the database
     * $data => an associative array that specifies update data
     */

    public function update_profile($profile_id, array $data) {
        return $this->db->where('profile_id', $profile_id)
                        ->limit(1)
                        ->update('billing_profiles', $data);
    }

    /*
     * Gets information from the database about a specified transaction
     * $paypal_lookup => Specifies whether or not the function should also look up
     * details about the transaction with PayPal. If true, then these details will be included
     * in the return array as an sub-array with key 'PayPaylResult'
     */

    public function get_transaction($transaction_id, $select = "*", $paypal_lookup = false) {
        $query = $this->db->select($select)
                ->from('billing_transactions')
                ->where('transaction_id', $transaction_id)
                ->limit(1)
                ->get();
        $data = $query->row_array();
        if ($paypal_lookup) {
            $p = $this->Get_transaction_details($transaction_id);
            if ($p['success']) {
                $data['PayPalResult'] = $p['PayPalResult'];
            }
        }
        return $data;
    }

    /*
     * Emails a transaction receipt to the owner of the user account
     */

    public function email_transaction_receipt($transaction_id) {
        $this->load->library('fpdf');
        $transaction_details = $this->get_transaction($transaction_id);
        $this->load->model('User_model');
        $user_id = $transaction_details['user_id'];
        $this->User_model->initialize($user_id);
        $user_details = $this->User_model->get('first_name, last_name, email');
        //save receipt to server
        $pdf = $this->get_receipt($transaction_id);
        $attachment = '/var/www.v1.riskmp.com/transaction_receipts/' . $user_id . "_" . $transaction_id . ".pdf";
        $pdf->output($attachment, 'F');
        $to = $user_details['email'];
        $subject = "Your RiskMP Receipt";
        $message_top = "Dear " . $user_details['first_name'] . " " . $user_details['last_name'] . ",\n\n";
        $message_body = "This is to notify you that your subscription has been processed and your credit card "
                . "has been charged. Please find your receipt attached. Thank you for your business.";
        $message_bottom = "\n\nRegards,\nThe RiskMP Team\n\nThis email was sent from an unmonitored mailbox." .
                " Please do not reply to this message.";
        $message = $message_top . $message_body . $message_bottom;
        $command = "php /var/www.v1.riskmp.com/RichEmailSender.php \"$to\" \"admin@riskmp.com\" \"The RiskMP Team\" \"$subject\" \"$message\" 0 $attachment > /dev/null 2>&1 &";
        exec($command);
    }

    /*
     * $post_data array must contain the following keys:
     * -initial_payment_txn_id
     * -recurring_payment_id
     * -next_payment_date
     */

    public function process_initial_recurring_transaction(array $post_data) {
        //check if transaction already processed
        if (isset($post_data['initial_payment_txn_id']) && $this->transaction_processed($post_data['initial_payment_txn_id'])) {
            return;
        }
        $this->load->model('User_model');
        //get profile info
        $profile_info = $this->get_profile($post_data['recurring_payment_id']);
        if (count($profile_info) == 0) { //check if profile not found
            $this->cancel_profile($post_data['recurring_payment_id'], 'Not found in DB.');
            return;
        }
        //mark profile as active
        $this->update_profile($post_data['recurring_payment_id'], array('profile_status' => 'Active'));
        if (isset($post_data['initial_payment_txn_id'])) {
            //lookup transaction
            $t_lookup = $this->Get_transaction_details($post_data['initial_payment_txn_id']);
            if (!$t_lookup['success']) {
                //ERROR - Cannot lookup transaction on PayPal Server
                log_message('error', 'PayPal ERROR! Cannot look up transaction with id #' . $post_data['initial_payment_txn_id']);
                return;
            } else {
                $transaction_data = $t_lookup['PayPalResult'];
                //Successful call
                //store transaction in database
                $this->store_transaction(array(
                    'transaction_id' => $post_data['initial_payment_txn_id'],
                    'profile_id' => $post_data['recurring_payment_id'],
                    'user_id' => $profile_info['user_id'],
                    'amount' => $transaction_data['AMT'],
                    'order_time' => $transaction_data['ORDERTIME']
                ));
                //insert new subscription
                $this->insert_subscription(array(
                    'user_id' => $profile_info['user_id'],
                    'profile_id' => $post_data['recurring_payment_id'],
                    'billing_transaction_id' => $post_data['initial_payment_txn_id'],
                    'amount_paid' => $transaction_data['AMT'],
                    'date_of_redemption' => date('Y-m-d'),
                    'expiry_date' => date('Y-m-d', strtotime($post_data['next_payment_date'])),
                    'renewal_date' => date('Y-m-d', strtotime($post_data['next_payment_date']))
                ));
                //notify user of success
                $message_body = "This is to notify you that your new subscription to RiskMP has been"
                        . " processed successfully and your credit card has been charged. Thank you for your business.";
                $subject = "RiskMP Subscription Created";
                $this->User_model->send_email($subject, $message_body, $profile_info['user_id']);
                //email receipt
                $this->email_transaction_receipt($post_data['initial_payment_txn_id']);
            }
        } else { //if no initial transaction
            //notify user of success
            $message_body = "This is to notify you that your new subscription to RiskMP has been"
                    . " processed successfully. Thank you for your business.";
            $subject = "RiskMP Subscription Created";
            $this->User_model->send_email($subject, $message_body, $profile_info['user_id']);
        }
    }

    /*
     * Processes a recurring payment PayPal IPN notification.
     * $post_data array must contain the following keys:
     * -txn_id
     * -recurring_payment_id
     * -next_payment_date
     */

    public function process_recurring_payment(array $post_data) {
        //check if transaction already processed
        if ($this->transaction_processed($post_data['txn_id'])) {
            return;
        }
        //lookup transaction
        $t_lookup = $this->Get_transaction_details($post_data['txn_id']);
        if (!$t_lookup['success']) {
            //ERROR - Cannot lookup transaction on PayPal Server
            log_message('ERROR', 'PayPal ERROR! Cannot look up transaction with id #' . $post_data['txn_id']);
            return;
        } else {
            $transaction_data = $t_lookup['PayPalResult'];
            //Successful call
            //get profile info
            $profile_info = $this->get_profile($post_data['recurring_payment_id']);
            if (count($profile_info) == 0) { //check if profile not found
                log_message('ERROR', 'IPN RECEIVED FOR PROFILE THAT DOES NOT EXIST IN DATABASE. PAYMENT WILL BE REFUNDED.');
                $this->Refund_transaction(
                        array('transaction_id' => $post_data['txn_id'],
                            'note' => 'Profile was previously cancelled')
                );
                $this->cancel_profile($post_data['recurring_payment_id'], 'Not found in DB.');
                log_message('ERROR', 'PAYMENT HAS BEEN REFUNDED');
                return;
            }
            //check if profile cancelled in database
            if ($profile_info['profile_status'] != 'Active') {
                //refund transaction
                log_message('ERROR', 'IPN RECEIVED FOR PROFILE THAT WAS ALREADY CANCELLED. PAYMENT WILL BE REFUNDED.');
                $this->Refund_transaction(
                        array('transaction_id' => $post_data['txn_id'],
                            'note' => 'Profile was previously cancelled')
                );
                $this->cancel_profile($post_data['recurring_payment_id']);
                log_message('ERROR', 'IPN RECEIVED FOR PROFILE THAT WAS ALREADY CANCELLED. PAYMENT HAS BEEN REFUNDED.');
                return;
            }
            $this->store_transaction(array(
                'transaction_id' => $post_data['txn_id'],
                'profile_id' => $post_data['recurring_payment_id'],
                'user_id' => $profile_info['user_id'],
                'amount' => $transaction_data['AMT'],
                'order_time' => $transaction_data['ORDERTIME']
            ));
            //insert new subscription
            $this->insert_subscription(array(
                'user_id' => $profile_info['user_id'],
                'profile_id' => $post_data['recurring_payment_id'],
                'billing_transaction_id' => $post_data['txn_id'],
                'amount_paid' => $transaction_data['AMT'],
                'date_of_redemption' => date('Y-m-d'),
                'expiry_date' => date('Y-m-d', strtotime($post_data['next_payment_date'])),
                'renewal_date' => date('Y-m-d', strtotime($post_data['next_payment_date']))
            ));
            $this->email_transaction_receipt($post_data['txn_id']);
        }
    }

    /*
     * Processes a failed payment IPN notification
     * $post_data array must contain the following keys:
     * -recurring_payment_id
     */

    public function process_failed_payment($post_data) {
        $this->store_cancelled_profile($post_data['recurring_payment_id']);
        //send email notification to user account owner
        $this->load->model('User_model');
        $profile_data = $this->get_profile($post_data['recurring_payment_id'], 'user_id');
        $message_body = "This is to notify you that a payment of $" . $post_data['amount']
                . " has failed. As a result, your subscription will be cancelled within 24 hours. Please log into your account" .
                " and update your billing information to prevent an interruption of service.";
        $subject = "RiskMP Subscription Cancelled";
        $this->User_model->send_email($subject, $message_body, $profile_data['user_id']);
    }

    /*
     * Processes a cancelled billing profile IPN notification
     */

    public function process_cancelled_profile($post_data) {
        $this->store_cancelled_profile($post_data['recurring_payment_id']);
    }

    /*
     * Checks whether a transaction has already been processed.
     */

    public function transaction_processed($transaction_id) {
        return ($this->db->from('billing_transactions')
                        ->where('transaction_id', $transaction_id)
                        ->count_all_results() > 0);
    }

    /*
     * Stores information about a transaction in the database
     */

    public function store_transaction(array $data) {
        return $this->db->insert('billing_transactions', $data);
    }

    /*
     * Stores information about a billing profile in the database. Accepts the PayPalResult array directly.
     */

    function store_billing_profile($user_id, array $PayPalResult) {
        $data = array(
            'profile_id' => $PayPalResult['PROFILEID'],
            'user_id' => $user_id,
            'amount' => $PayPalResult['REGULARAMT'],
            'taxamt' => $PayPalResult['TAXAMT'],
            'billingperiod' => $PayPalResult['BILLINGPERIOD'],
            'profile_status' => $PayPalResult['STATUS']
        );
        return $this->db->insert('billing_profiles', $data);
    }

    public function get_user_active_profiles($user_id) {
        $query = $this->db->select('*')
                ->from('billing_profiles')
                ->where('user_id', $user_id)
                ->where('profile_status', 'Active')
                ->get();
        return $query->result_array();
    }

    /*
     * Cancels all billing profiles for a user.
     */

    public function cancel_user_profiles($user_id) {
        $query = $this->db->select('*')
                ->from('billing_profiles')
                ->where('user_id', $user_id)
                ->where('profile_status', 'Active')
                ->get();
        $profiles = $query->result_array();
        $success = true;
        foreach ($profiles as $p) {
            $cancel = $this->cancel_profile($p['profile_id']);
            if (!$cancel['success']) {
                $success = false;
            }
        }
        if ($success) {
            $this->load->model('User_model');
            $subject = "Your subscription has been cancelled.";
            $message = "This is to notify you that you have cancelled your subscription to RiskMP. After your "
                    . "subscription expires, it will not be automatically renewed. Thank you for your business.";
            $this->User_model->send_email($subject, $message, $user_id);
            return array('success' => true);
        } else {
            return array('success' => false, 'message' => 'An unknown error has occurred.');
        }
    }

    /*
     * Cancels a profile with PayPal and updates database.
     */

    public function cancel_profile($profile_id, $reason = 'Requested by user.') {
        $cancel = $this->Manage_recurring_payments_profile_status($profile_id, 'Cancel', $reason);
        if ($cancel) {
            return $this->store_cancelled_profile($profile_id);
        } else {
            log_message('error', 'Could not cancel profile with profileid ' . $p['profile_id']);
            return array('success' => false, 'message' => 'Could not cancel the specified profile.');
        }
    }

    /*
     * Updates database to reflect cancelled profile (nullifies renewal date of
     * associated subscription and updates entry in billing_profiles table)
     */

    public function store_cancelled_profile($profile_id) {
        $this->update_profile($profile_id, array('profile_status' => 'Cancelled'));
        //nullify renewal date of associated subscription
        $this->db->where('profile_id', $profile_id)
                ->where('expiry_date >= CURDATE()')
                ->update('subscriptions', array('renewal_date' => null));
        return array('success' => true, 'message' => 'Profiles successsfully cancelled.');
    }

    /*
     * Creates a new subscription
     */

    function insert_subscription($data) {
        if (!isset($data['user_id'])) {
            return array('success' => false, 'message' => 'User id not specified.');
        }
        if (!isset($data['expiry_date'])) {
            return array('success' => false, 'message' => 'Expiry date not specified.');
        }
        if (!isset($data['amount_paid'])) {
            $data['amount_paid'] = 0.0;
        }
        if (!isset($data['date_of_redemption'])) {
            $data['date_of_redemption'] = date("Y-m-d");
        }
        $this->db->insert('subscriptions', $data);
    }

    /*
     * Performs PayPal API Call
     */

    function Get_recurring_payments_profile_details($profile_id) {
        $GRPPDFields = array(
            'profileid' => $profile_id   // Profile ID of the profile you want to get details for.
        );

        $PayPalRequestData = array('GRPPDFields' => $GRPPDFields);

        $PayPalResult = $this->paypal_pro->GetRecurringPaymentsProfileDetails($PayPalRequestData);
        if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
            return array('success' => false, 'message' => 'Error loading profile information.');
        } else {
            return array('success' => true, 'PayPalResult' => $PayPalResult);
        }
    }

    /*
     * Performs PayPal API Call
     */

    function Get_transaction_details($transactionid) {
        $GTDFields = array(
            'transactionid' => $transactionid  // PayPal transaction ID of the order you want to get details for.
        );

        $PayPalRequestData = array('GTDFields' => $GTDFields);

        $PayPalResult = $this->paypal_pro->GetTransactionDetails($PayPalRequestData);
        if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
            return array('success' => false, 'message' => 'Error loading transaction information.');
        } else {
            return array('success' => true, 'PayPalResult' => $PayPalResult);
        }
    }

    /*
     * Performs PayPal API Call
     */

    function Manage_recurring_payments_profile_status($profileid, $action, $note) {
        $MRPPSFields = array(
            'profileid' => $profileid, // Required. Recurring payments profile ID returned from CreateRecurring...
            'action' => $action, // Required. The action to be performed.  Mest be: Cancel, Suspend, Reactivate
            'note' => $note     // The reason for the change in status.  For express checkout the message will be included in email to buyers.  Can also be seen in both accounts in the status history.
        );

        $PayPalRequestData = array('MRPPSFields' => $MRPPSFields);

        $PayPalResult = $this->paypal_pro->ManageRecurringPaymentsProfileStatus($PayPalRequestData);

        if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
            return true;
        } else {
            return true;
        }
    }

    /*
     * $data array must contain the following keys:
     * -profilestartdate
     * -billingperiod
     * -amt
     * -initamt
     * -CCDetails - An associative array of credit card information
     * -firstname
     * -lastname
     */

    function CreateRecurringPaymentsProfile(array $data) {
        $ProfileDetails = array(
            'profilestartdate' => $data['profilestartdate'], // Required.  The date when the billing for this profiile begins.  Must be a valid date in UTC/GMT format.
            'profilereference' => '' // The merchant's own unique invoice number or reference ID.  127 char max.
        );
        $ScheduleDetails = array(
            'desc' => "RiskMP Recurring", // Required.  Description of the recurring payment.  This field must match the corresponding billing agreement description included in SetExpressCheckout.
            'maxfailedpayments' => 0, // The number of scheduled payment periods that can fail before the profile is automatically suspended.  
            'autobilloutamt' => 'AddToNextBilling'       // This field indiciates whether you would like PayPal to automatically bill the outstanding balance amount in the next billing cycle.  Values can be: NoAutoBill or AddToNextBilling
        );
        $BillingPeriod = array(
            'trialbillingperiod' => '',
            'trialbillingfrequency' => '',
            'trialtotalbillingcycles' => '',
            'trialamt' => '',
            'billingperiod' => $data['billingperiod'], // Required.  Unit for billing during this subscription period.  One of the following: Day, Week, SemiMonth, Month, Year
            'billingfrequency' => 1, // Required.  Number of billing periods that make up one billing cycle.  The combination of billing freq. and billing period must be less than or equal to one year. 
            'totalbillingcycles' => 0, // the number of billing cycles for the payment period (regular or trial).  For trial period it must be greater than 0.  For regular payments 0 means indefinite...until canceled.  
            'amt' => $data['amt'], // Required.  Billing amount for each billing cycle during the payment period.  This does not include shipping and tax. 
            'currencycode' => 'CAD', // Required.  Three-letter currency code.
            'taxamt' => $data['taxamt']         // Tax amount for each billing cycle during the payment period.
        );
        $ActivationDetails = array(
            'initamt' => $data['initamt'], // Initial non-recurring payment amount due immediatly upon profile creation.  Use an initial amount for enrolment or set-up fees.
            'failedinitamtaction' => 'CancelOnFailure', // By default, PayPal will suspend the pending profile in the event that the initial payment fails.  You can override this.  Values are: ContinueOnFailure or CancelOnFailure
        );
        $CCDetails = $data['CCDetails'];
        $PayerName = array(
            'firstname' => $data['firstname'], // Payer's first name.  25 char max.
            'lastname' => $data['lastname'], // Payer's last name.  25 char max.
        );
        $PayPalRequestData = array(
            'ProfileDetails' => $ProfileDetails,
            'ScheduleDetails' => $ScheduleDetails,
            'BillingPeriod' => $BillingPeriod,
            'ActivationDetails' => $ActivationDetails,
            'CCDetails' => $CCDetails,
            'PayerName' => $PayerName,
        );
        $this->config->load('paypal');
        $PayPalResult = $this->paypal_pro->CreateRecurringPaymentsProfile($PayPalRequestData);

        if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
            return array('success' => false, 'errors' => $PayPalResult['ERRORS']);
        } else {
            return array('success' => true, 'data' => $PayPalResult);
        }
    }

    function Refund_transaction(array $data) {
        $RTFields = array(
            'transactionid' => $data['transaction_id'], // Required.  PayPal transaction ID for the order you're refunding.
            'payerid' => '', // Encrypted PayPal customer account ID number.  Note:  Either transaction ID or payer ID must be specified.  127 char max
            'invoiceid' => '', // Your own invoice tracking number.
            'refundtype' => 'Full', // Required.  Type of refund.  Must be Full, Partial, or Other.
            'amt' => '', // Refund Amt.  Required if refund type is Partial.  
            'currencycode' => '', // Three-letter currency code.  Required for Partial Refunds.  Do not use for full refunds.
            'note' => $data['note'], // Custom memo about the refund.  255 char max.
            'retryuntil' => '', // Maximum time until you must retry the refund.  Note:  this field does not apply to point-of-sale transactions.
            'refundsource' => 'any', // Type of PayPal funding source (balance or eCheck) that can be used for auto refund.  Values are:  any, default, instant, eCheck
            'merchantstoredetail' => '', // Information about the merchant store.
            'refundadvice' => 0, // Flag to indicate that the buyer was already given store credit for a given transaction.  Values are:  1/0
            'refunditemdetails' => '', // Details about the individual items to be returned.
            'msgsubid' => '', // A message ID used for idempotence to uniquely identify a message.
            'storeid' => '', // ID of a merchant store.  This field is required for point-of-sale transactions.  50 char max.
            'terminalid' => ''        // ID of the terminal.  50 char max.
        );

        $PayPalRequestData = array('RTFields' => $RTFields);

        $PayPalResult = $this->paypal_pro->RefundTransaction($PayPalRequestData);

        if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
            $errors = array('Errors' => $PayPalResult['ERRORS']);
            $this->load->view('paypal_error', $errors);
        } else {
            // Successful call.  Load view or whatever you need to do here.	
        }
    }

    /*
     * Creates a pdf invoice detailling the transaction
     */

    public function get_receipt($transaction_id) {
        $this->load->library('fpdf');
        $transaction_details = $this->get_transaction($transaction_id);
        $this->load->model('User_model');
        $this->User_model->initialize($transaction_details['user_id']);
        $user_details = $this->User_model->get('*');
        //lookup profile
        $profile_details = $this->get_profile($transaction_details['profile_id'], 'billingperiod, amount, taxamt');
        //lookup subscription
        $subscription_details = $this->get_subscription_by_transaction($transaction_id);
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetAuthor('ESCTT Inc.');
        $pdf->SetTitle('Invoice');
        $pdf->Image('http://v1.riskmp.com/assets/images/logo.png', 10, 10, 35, 19, '', 'http://v1.riskmp.com/');
        $pdf->SetXY(50, 10);
        $pdf->SetFont('Arial', 'B', 40);
        $pdf->Cell(100, 20, 'Receipt');
        $address_x = $pdf->GetX();
        $pdf->set_field_title_font($pdf);
        $pdf->Write(14, 'ESCTT Inc.');
        $pdf->Ln(5);
        $pdf->SetX($address_x);
        $pdf->Write(14, '131 Bloor Street West');
        $pdf->Ln(5);
        $pdf->SetX($address_x);
        $pdf->Write(14, 'Suite 200/318');
        $pdf->Ln(5);
        $pdf->SetX($address_x);
        $pdf->Write(14, 'Toronto, ON M5S 1R8');
        $pdf->Ln(5);
        $pdf->SetX($address_x);
        $pdf->Write(14, 'Business # '.BUSINESS_NUMBER);
        $pdf->SetXY(10, 40);
        $pdf->set_field_title_font($pdf);
        $pdf->Write(10, 'Client: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(10, $user_details['first_name'] . " " . $user_details['last_name']);
        $pdf->set_field_title_font($pdf);
        $pdf->SetX(140);
        $pdf->Write(10, 'Generated on: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(10, date("Y-m-d"));
        $pdf->Ln(16);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Write(6, 'Transaction Details');
        $pdf->Ln(6);
        $pdf->set_field_title_font($pdf);
        $pdf->Write(14, 'Transaction ID: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(14, $transaction_id);
        $pdf->Ln(7);
        $pdf->set_field_title_font($pdf);
        $pdf->Write(14, 'Order Time: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(14, $transaction_details['order_time']);
        $pdf->Ln(7);
        $pdf->set_field_title_font($pdf);
        $pdf->Write(14, 'Payment Method: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(14, 'Credit Card');
        $pdf->Ln(16);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Write(6, 'Purchase Details');
        $pdf->Ln(4);
        //set table header and body fonts
        $thfont = array('family' => 'Arial', 'style' => 'B', 'size' => 11);
        $tbfont = array('family' => 'Arial', 'style' => '', 'size' => 11);
        $pdf->Ln(4);
        $twidth = array(150, 50); //column widths
        $theader = array('Item', 'Amount'); //column titles
        $tdata = array(
            array('RiskMP Membership @ 1 ' . $profile_details['billingperiod'], '$' . $profile_details['amount']),
            array('Tax', '$' . $profile_details['taxamt']),
            array('Grand Total', '$' . number_format(floatval($profile_details['amount']) + floatval($profile_details['taxamt']), 2, '.', ''))
        );
        $pdf->create_table($theader, $tdata, $twidth, 'L', 'L', $thfont, $tbfont); //add table to pdf document
        $pdf->set_field_title_font($pdf);
        $pdf->Write(14, 'Subscription Start Date: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(14, $subscription_details['date_of_redemption']);
        $pdf->Ln(7);
        $pdf->set_field_title_font($pdf);
        $pdf->Write(14, 'Subscription Expiry Date: ');
        $pdf->set_field_value_font($pdf);
        $pdf->Write(14, $subscription_details['expiry_date']);
        return $pdf;
    }

}

?>
