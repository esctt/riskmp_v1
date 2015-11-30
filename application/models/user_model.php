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

class User_model extends CI_Model {

    private $user_id;
    private $initialized = false;

    public function __construct() {
        parent::__construct();
    }

    public function initialize($user_id) {
        $this->user_id = $user_id;
        $this->initialized = true;
        return true;
    }

    /**
     * Gets all the projects for which the user has permissions.
     * The result is returned as a 2-dimensional array (numeric, associative).
     * @return array A two-dimensional array (numeric->row, associative->fields) or an empty array if there are no projects.
     */
    public function get_projects($limit = null, $offset = null, $orderby = false, $direction = "asc") {
        if ($this->initialized !== true) {
            return false;
        }
        $this->db->select('SQL_CALC_FOUND_ROWS
            view_projects.project_id, view_projects.project_name, view_projects.date_modified, 
            view_projects.last_modifier, view_projects.num_tasks, view_projects.active_risks, view_projects.closed_risks,
            view_projects.total_expected_cost, view_projects.project_total_mitigation_cost', false)->
                from('view_projects')->join('project_users p_u', 'p_u.project_id = view_projects.project_id')
                ->where('p_u.user_id = ' . $this->user_id);
        if ($orderby !== false) {
            $this->db->order_by($orderby, $direction);
        }
        if ($limit !== null && $limit != 0) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        if ($query->num_rows() === 0) {
            return array();
        }
        $result = $query->result_array();
        $result['num_rows'] = $this->db->
                        query('SELECT FOUND_ROWS()', false)->row(0)->{'FOUND_ROWS()'};
        return $result;
    }

    /*
     * Gets fields associated with the user account.
     * @param string $select Specify the fields to be retrieved. Default: All.
     * @return type an associative array containing the fetched data.
     */

    public function get($select = "*") {
        if ($this->initialized !== true) {
            return false;
        }
        $query = $this->db->select($select)->
                from('users')->
                where('user_id', $this->user_id)->
                limit(1)->
                get();
        return $query->row_array();
    }

    /*
     * Gets fields associated with a user account using its username
     * @param $username The username of the user
     * @param string $select Specify the fields to be retrieved. Default: All.
     * @return type an associative array containing the fetched data.
     */

    public function get_by_username($username, $select = "*") {
        $query = $this->db->select($select)->
                from('users')->
                where('username', $username)->
                limit(1)->
                get();
        if (!$query->num_rows()) {
            return array('success' => false, 'message' => 'The user does not exist.');
        } else {
            return array('success' => true, 'data' => $query->row_array());
        }
    }

    /**
     * Attempts to log a user in.
     * @param string $username username
     * @param type $pwd the user's password
     * @return boolean true if successful login, false if unsuccessful.
     */
    public function login($username, $password) {
        $result = $this->verify_credentials($password, $username);
        if ($result['success'] === false) {
            return $result;
        } else {
            if ($this->create_session($result['data']['user_id'], $result['data']['username'], $result['data']['first_name'], $result['data']['last_name'], $result['data']['email'])) { //create session
                return $result;
            } else {
                return array('success' => false);
            }
        }
    }

    /*
     * Verifies user login information. Note that password cannot be null, and at least one of
     * username or user_id must not be null. Returns false if no match found, or user data
     * if match is found.
     */

    public function verify_credentials($password, $username = null, $user_id = null) {
        if ($username == null && $user_id == null) {
            return array('success' => false, 'message' => 'User not specified');
        }
        $this->db->select('user_id, username, email,
            first_name, last_name, salt, password')->from('users')->limit(1);
        if ($username != null) {
            $this->db->where('username', $username)
                ->or_where('email', $username);
        }
        if ($user_id != null) {
            $this->db->where('user_id', $user_id);
        }
        $query = $this->db->get();
        if ($query->num_rows() < 1) { //check if no match found
            return array('success' => false, 'message' => 'The username is incorrect.');
        }
        $row = $query->row_array(); //store matched user data
        $digest = self::get_digest($password, $row['salt']); //get user salt and calculate digest
        if ($digest !== $row['password']) { //check if digest matches stored user digest
            return array('success' => false, 'message' => 'The password is incorrect.');
        } else {
            unset($row['password']);
            unset($row['salt']);
            return array('success' => true, 'data' => $row);
        }
    }

    /*
     * Creates a new user. Handles all checks to ensure that the username and email are unique, and that the
     * password matches the required criteria.
     */

    public function create($data) {
        if (
                !isset($data['username']) ||
                !isset($data['password']) ||
                !isset($data['first_name']) ||
                !isset($data['last_name']) ||
                !isset($data['email']) ||
                !isset($data['phone']) ||
                !isset($data['agreement_acceptance'])
        ) {
            return array('success' => false, 'message' => 'Not enough information to create the user.');
        }
        //check for valid username
        $username_check = $this->check_username($data['username']);
        if (!$username_check['success']) {
            return $username_check;
        }
        //check for unique username
        if (!$this->username_unique($data['username'])) {
            return array('success' => false, 'message' => 'That username is already in use. Please try a different username.');
        }
        //check for unique email
        if (!$this->email_unique($data['email'])) {
            return array('success' => false, 'message' => 'That email address is already in use. Please try a different email address.');
        }
        $data['salt'] = uniqid();
        $data['password'] = self::get_digest($data['password'], $data['salt']);
        if ($this->db->insert('users', $data)) {
            return array('success' => true, 'data' => array('user_id' => $this->db->insert_id()));
        } else {
            return array('success' => false, 'message' => 'An unknown error has occurred. Please try again later.');
        }
    }

    /*
     * Checks if a username is unique. The optional $user_id parameter can be used to skip
     * a particular user when doing this check (ie. the user being modified).
     */

    public function username_unique($username, $user_id = null) {
        $this->db->where('username', $username);
        if ($user_id != null) {
            $this->db->where('user_id <> ' . $user_id);
        }
        $query = $this->db->get('users');
        return $query->num_rows() == 0;
    }

    /*
     * Checks if an email is unique. The optional $user_id parameter can be used to skip
     * a particular user when doing this check (ie. the user being modified).
     */

    public function email_unique($email, $user_id = null) {
        $this->db->where('email', $email);
        if ($user_id != null) {
            $this->db->where('user_id <> ' . $user_id);
        }
        $query = $this->db->get('users');
        return $query->num_rows() == 0;
    }

    /*
     * Deletes a user account
     * @param $user_id The id of the user account to be deleted
     */

    public function delete($user_id) {
        return $this->db->from('users')->where('user_id', $user_id)->limit(1)->delete();
    }

    /*
     * Updates user information
     */

    public function edit($user_id, $data) {
        $keys = array('username', 'password', 'first_name', 'last_name',
            'email', 'phone', 'company_name');
        $this->initialize($user_id);
        if (isset($data['username'])) {
            if (!$this->username_unique($data['username'], $user_id)) {
                return array('success' => false, 'message' => 'That username is already in use.');
            }
            $username_check = $this->check_username($data['username']);
            if (!$username_check['success']) {
                return $username_check;
            }
        }
        if (isset($data['email'])) {
            if (!$this->email_unique($data['email'], $user_id)) {
                return array('success' => false, 'message' => 'That email address is already in use.');
            }
        }
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        if (isset($data['password'])) {
            //check password strength
            if ($this->password_strength($data['password']) < 3) {
                return array('success' => false, 'message' => "Please ensure that your password is at least 6 characters long and contains at least one lowercase letter, one uppercase letter and one number/symbol.");
            }
            //generate new salt and digest
            $data['salt'] = uniqid();
            $data['password'] = self::get_digest($data['password'], $data['salt']);
        }
        if ($this->db->where('user_id', $user_id)->limit(1)->update('users', $data)) {
            return array('success' => true);
        } else {
            return array('success' => false, 'message' => 'An unknown error occurred. Please try again later.');
        }
    }

    // Submitting agreement acceptance to DB
    public function user_agreement($user_id, $data) {
        $keys = array('agreement_acceptance');
        $this->initialize($user_id);
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys)) {
                unset($data[$key]);
            }
        }
        $data['agreement_acceptance'] = 'accepted';
        if ($this->db->where('user_id', $user_id)->update('users', $data)) {
            return array('success' => true);
        } else {
            return array('success' => false, 'message' => 'An unknown error occurred. Please try again later.');
        }
    }

    /**
     * Logs the user out.
     */
    public function logout() {
        if (session_id() == '') {
            session_start();
        }
        self::destroy_session();
    }

    /**
     * Checks whether a user is currently logged in with a valid session.
     * @return boolean true if the user is logged in with a valid session, or false if otherwise.
     */
    public function confirm_member($verify_subscribed = true, $redirect = true) {
        if (session_id() == '') {
            session_start();
        }
        $sid = session_id();

        if ($sid === "") {
            return array('success' => false, 'message' => null);
        }
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['time_created']) || !isset($_SESSION['last_activity'])
        ) { //check session variables
            return array('success' => false, 'message' => 'You are not logged in.');
        }
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $this->load->database();
        $query = $this->db->get_where('users', array(
            'user_id' => $user_id,
            'username' => $username,
            'session_id' => $sid,
            'last_ip' => $_SERVER['REMOTE_ADDR']
                ), 1); //check current session data against database
        if ($query->num_rows() < 1) { //invalid session
            self::destroy_session();
            return array('success' => false, 'message' => 'Your session has expired. This is likely because you logged in from a different device.');
        }
        //check how long since last activity or if session was created more than 6 hours ago
        if (time() - $_SESSION['last_activity'] > 1200 || time() - $_SESSION['time_created'] > 21600) {
            unset($_SESSION['username']); //unset username so that header recognizes not logged in
            //last activity was more than 20 minutes ago.
            return array('success' => false, 'message' => 'Session expired due to inactivity.');
        }
        $_SESSION['last_activity'] = time(); //update last activity time.
        //check user subscription
        if ($verify_subscribed) {
            if (!$this->verify_subscribed($user_id)) {
                //redirect user to subscription details page
                if ($redirect) {
                    redirect('user/subscription_details');
                } else {
                    return array('success' => false, 'message' => 'You are not currently subscribed.');
                }
            }
        }
        return array('success' => true);
    }

    private static function destroy_session() {
        session_unset();
        session_destroy();
    }

    /*
     * Store session information in the database
     */

    private function store_session($user_id) {
        return $this->db->where(array('user_id' => $user_id))
                        ->limit(1)
                        ->update('users', array('session_id' => session_id(),
                            'last_ip' => $_SERVER['REMOTE_ADDR'],
                            'last_connection' => date('Y-m-d H:i:s')));
    }

    /*
     * Creates a new session
     */

    private function create_session($user_id, $username, $first_name, $last_name, $email) {
        if (session_id() == '') {
            session_start();
        }
        //start session
        $_SESSION['user_id'] = $user_id; //store user id in session
        $_SESSION['username'] = $username; //store user username in session
        $_SESSION['first_name'] = $first_name; //store user first name in session
        $_SESSION['last_name'] = $last_name; //store user first name in session
        $_SESSION['email'] = $email;
        $_SESSION['time_created'] = time();
        $_SESSION['last_activity'] = time(); //update last activity time.
        return $this->store_session($user_id);
    }

    /*
     * Ensures that a username meets the criteria to be valid
     */

    public function check_username($username) {
        $len = strlen($username);
        if ($len < 2 || $len > 20) {
            return array('success' => false, 'message' => 'Please select a username that is between 2 and 20 characters in length and that only contains numbers, uppercase/lowercase letters, and underscores/periods.');
        }
        if (preg_match("'/^[a-zA-Z0-9_.]+$/'", $username)) {
            return array('success' => false, 'message' => 'Please select a username that is between 2 and 20 characters in length and that only contains numbers, uppercase/lowercase letters, and underscores/periods.');
        } else {
            return array('success' => true);
        }
    }

    /*
     * Sends an email to the associated user account with a link to reset their password (only if
     * user is a super user, regular users cannot reset their password; this must be done by a super
     * user)
     */

    function forgot_password($username) {
        $query = $this->db->select('user_id, salt')
                ->from('users')
                ->where('username', $username)
                ->or_where('email', $username)
                ->limit(1)
                ->get();
        if ($query->num_rows() < 1) {
            return array('succes' => false, 'message' => "There is no account matching that username. Please try again.");
        }
        $user_data = $query->row_array();
        $reset_code = hash('sha256', $user_data['user_id'] . $user_data['salt'] . uniqid("", true) . time());
        $reset_hash = hash('sha256', $reset_code);
        if (!$this->db->set('user_id', $user_data['user_id'])
                ->set('reset_hash', $reset_hash)
                ->set('time_requested', 'NOW()', false)
                ->set('used', 0)->insert('password_reset_codes')) {
            return array('succes' => false, 'message' => "An unknown error has occured. Please try again.");
        }
        //send email to user
        $message_body = "You are receiving this email because you requested to have your password reset. "
                . "To reset your password, please copy and paste the following link into your browser: \n \n "
                . base_url('user/reset_password/' . $reset_code)
                . "\n \n Please note that this code will expire within 24 hours. If you did not request to have your "
                . "password reset, please disregard this message.";
        $subject = "Reset your RiskMP password";
        
//        $this->initialize($user_data['user_id']);
//        $user_info = $this->get('first_name, last_name, email');
//        $to = $user_info['email'];
//        $message_top = "Dear " . $user_info['first_name'] . " " . $user_info['last_name'] . ",\n\n";
//        $message_bottom = "\n\nRegards,\nThe RiskMP Team\n\nThis email was sent from an unmonitored mailbox." .
//                " Please do not reply to this message.";
//        $message = $message_top . $message_body . $message_bottom;
//        
//        $headers = "MIME-Version: 1.0" . "\r\n";
//        $headers .= "To: " . $user_info['first_name'] . " " . $user_info['last_name'] . " <". $to .">" . "\r\n";
//        $headers .= "From: The RiskMP Team <admin@riskmp.com>" . "\r\n";
//        $headers .= "BCC: andrew@esctt.com" . "\r\n";
//        $headers .= "Reply-To: no-reply@riskmp.com" . "\r\n";
//        $headers .= "Return-Path: no-reply@riskmp.com" . "\r\n";
//        $headers .= "X-Mailer: PHP 5.x";

//        if ( mail($to, $subject, $message, $headers) ) {
//            return array('success' => true, 'message' => "An email has been sent to the email address we have on file with a link to reset your password.\n\n P.S. Check your spam mail.");
//        }
//        else {
//            return array('success' => false, 'message' => "An error occurred.");
//        }
        $this->send_email($subject, $message_body, $user_data['user_id']);
        return array('success' => true, 'message' => "An email has been sent to the email address we have on file with a link to reset your password.\n\n P.S. Check your spam mail.");
    }

    /*
     * Resets a password with a valid reset code generated by the forgot_password function.
     */

    function reset_password($reset_code, $new_password) {
        $query = $this->db->select('user_id', false)
                ->from('password_reset_codes')
                ->where('reset_hash', hash('sha256', $reset_code))
                ->where("NOW() < ADDTIME(time_requested, '1 00:00:00')") //add one day to time requested, check if still valid
                ->where('used', 0)
                ->limit(1)
                ->get();
        if ($query->num_rows() < 1) {
            return array('success' => false, 'message' => 'Invalid reset code.');
        }
        $reset_data = $query->row_array();
        $reset = $this->edit($reset_data['user_id'], array('password' => $new_password));
        if ($reset['success']) {
            //invalidate reset code
            $this->db->where('user_id', $reset_data['user_id'])
                    ->set('used', 1)
                    ->update('password_reset_codes');
        }
        return $reset;
    }

    /*
     * Checks the strength of a password
     */

    public function password_strength($pwd) {
        if (strlen($pwd) < 6)
            return 0;
        $regexs = array(
            '/[0-9]+/', // Numbers
            '/[a-z]+/', // Lower Case Letters
            '/[A-Z]+/', // Upper Case Letters
            '/[+-=]+/', // Your list of allowable symbols.
        );
        $count = 0;
        foreach ($regexs as $i) {
            if (preg_match($i, $pwd)) {
                $count++;
            }
        }
        return $count;
    }

    /*
     * Gets the password hash for a user password and salt
     */

    public static function get_digest($pwd, $salt) {
        $digest = $pwd . $salt;
        for ($i = 0; $i < 5; $i++) { //hash 5 times
            $digest = hash('sha256', $digest);
        }
        return $digest;
    }

    public function verify_subscribed($user_id) {
        return $this->db->select('*')
                        ->from('subscriptions')
                        ->where('user_id', $user_id)
                        ->where('expiry_date >= CURDATE()')
                        ->where('date_of_redemption <= CURDATE()')
                        ->count_all_results() > 0;
    }

    /*
     * Executes a script to send an email to the user without waiting for the script to run.
     */

    function send_email($subject, $message_body, $user_id) {
        $this->initialize($user_id);
        $user_info = $this->get('first_name, last_name, email');
        $to = $user_info['email'];
        $message_top = "Dear " . $user_info['first_name'] . " " . $user_info['last_name'] . ",\n\n";
        $message_bottom = "\n\nRegards,\nThe RiskMP Team\n\nThis email was sent from an unmonitored mailbox." .
                " Please do not reply to this message.";
        $message = $message_top . $message_body . $message_bottom;
        $command = "php /var/www.v1.riskmp.com/RichEmailSender.php \"$to\" \"admin@riskmp.com\" \"$subject\" \"$message\" 0 > /dev/null 2>&1 &";
        exec($command);
    }
}

?>
