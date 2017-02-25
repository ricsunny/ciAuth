<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter Model
 * A Complete Authentication using RESTful server implementation for CodeIgniter.
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Richard Sunny
 * @credits         Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/ricsunny/ciAuth
 * @version         3.1.3
 */
class Api_model extends CI_Model {

    var $Users = 'users';

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    public function create_new_user($email, $password, $name, $api_token, $remember_token) {

        //Query to check if the email already exists 
        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            $data = array(
                'email' => $email,
                'password' => $password,
                'name' => $name,
                'api_token' => $api_token,
                'remember_token' => $remember_token,
                'is_active' => 0,
                'created_at' => date(DATE_ATOM, time())
            );
            if ($this->db->insert($this->Users, $data)) {
                //on successfull insert operation
                return 1;
            } else {
                //if db insert operation fails
                return 0;
            }
        } else {
            //if user email already exists
            return 3;
        }
    }

    function login($email, $encrypted_password) {
        //Query to fetch user token and match email & password
        $this->db->select('api_token, is_active');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('password', $encrypted_password);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function check_email_exists($email) {
        //Query to match email & verify if record is not deleted
        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return false;
        }
    }

    /* For Change Password API */

    public function match_old_password($apiToken, $encryptedOldPassword) {
        //Query to fetch user password
        $this->db->select('password');
        $this->db->from($this->Users);
        $this->db->where('api_token', $apiToken);
        $this->db->where('password', $encryptedOldPassword);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {

            return true;
        } else {
            return false;
        }
    }

    public function modify_forgot_password($userToken, $encryptedNewPassword) {
        //Query to update the new password based on userToken
        $data = array(
            'password' => $encryptedNewPassword
        );
        $this->db->where('api_token', $userToken);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return 1;
        } else {
            //if db update operation fails
            return 0;
        }
    }

    /* For Change Password API ends */

    public function update_password_requested_time($email) {
        //Query to update password requested time
        $data = array(
            'password_requested_time' => date('Y-m-d H:i:s'),
            'is_password_requested' => 1
        );
        $this->db->where('email', $email);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return TRUE;
        } else {
            //if db update operation fails
            return FALSE;
        }
    }

    public function save_new_password($hash_email, $password_hash) {
        //Query to update the new password based on userToken
        $data = array(
            'password' => $password_hash,
            'is_password_requested' => 0
        );
        $this->db->where('remember_token', $hash_email);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return TRUE;
        } else {
            //if db update operation fails
            return FALSE;
        }
    }

    function get_password_requested_time($hash_email) {
        //Query to fetch upassword requested time based on user token
        $this->db->select('password_requested_time');
        $this->db->from($this->Users);
        $this->db->where('remember_token', $hash_email);
        $this->db->where('is_deleted !=', '1');
        $this->db->where('is_password_requested !=', '0');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return FALSE;
        }
    }


    public function activate_account($hash_email) {
        //Link expiration code	
        $this->load->helper('date');
        $date = date('Y-m-d H:i:s');
        $this->db->select('created_at');
        $this->db->from($this->Users);
        $this->db->where('remember_token', $hash_email);
        $query = $this->db->get();
        if (!empty($query)) {
            //echo $this->db->last_query();
            $row = $query->row();
            $date1Timestamp = strtotime(@$row->created_at);
            $date2Timestamp = strtotime($date);
            //Calculate the difference.
            $difference = $date2Timestamp - $date1Timestamp;
            if ($difference > 1800) {
                $this->db->delete($this->Users, array('remember_token' => $hash_email));
                return false;
            }
        }
        //link expiration code ends here
        //Query to update the new password based on userToken

        $data = array(
            'is_active' => "1"
        );

        $this->db->where('remember_token', $hash_email);

        if ($this->db->update($this->Users, $data)) {

            //on successfull update operation

            return TRUE;
        } else {

            //if db update operation fails

            return FALSE;
        }
    }

    public function modify_forgot_password_by_email($email, $encryptedNewPassword) {
        //Query to update the new password based on userToken
        $data = array(
            'password' => $encryptedNewPassword
        );
        $this->db->where('email', $email);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return 1;
        } else {
            //if db update operation fails
            return 0;
        }
    }

    public function getEmailByAuthToken($authToken) {

        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('api_token', $authToken);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->email;
        } else {
            return FALSE;
        }
    }

}