<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Rest Controller
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

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Api extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('api_model');

        //Loading encryption class to encrypt and decrypt password
        $this->load->library('encrypt');
        //Loading Email library
        $this->load->library('email');
    }

    public function register_post() {
        $email = $this->post('email');
        $password = $this->post('password');
        $name = $this->post('name');

        $encrypted_password = hash('sha512', $password);
        $api_token = hash('sha512', $email);

        //For change password web interface as unique key
        $remember_token = md5($email);

        $register_user = $this->api_model->create_new_user($email, $encrypted_password, $name, $api_token, $remember_token);

        if ($register_user == 1) {
            //Encode user email to attach to the url 
            $url = base_url() . "welcome/verify_email/" . $remember_token;

            //Send email with credentials to user
            $this->email->from('info@domain.com', 'App Name');
            $this->email->to($email);
            $this->email->subject('Account Verification');
            $this->email->message('Dear Customer,
							 <br/><br/>
							 Please click on the following LINK to verify your account:<br/>
							 <a href="' . $url . '">Verify</a><br/>
                                                         <br/><br/>
							 Best Regards,<br/>');
            $this->email->send();
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "User Registered Successfully."),
                'body' => array("apiToken" => $api_token)
            );
        } else if ($register_user == 3) {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "Email already exists"),
                'body' => array()
            );
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to register a user account"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function login_post() {

        $email = $this->post('email');
        $password = $this->post('password');

        $encrypted_password = hash('sha512', $password);
        $login_user = $this->api_model->login($email, $encrypted_password);
        if ($login_user != FALSE) {
            $is_active = $login_user[0]->is_active;
            if ($is_active != 0) {
                $api_token = $login_user[0]->api_token;
                $message = array(
                    'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "User logged in Successfully"),
                    'body' => array("apiToken" => $api_token)
                );
            } else {
                $message = array(
                    'header' => array("result" => "false", "resultCode" => "0005", "resultMessage" => "You need to verify your email in order to login."),
                    'body' => array()
                );
            }
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "You've entered incorrect email or password."),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function forgot_password_post() {

        $email = $this->post('email');
        $email_exists = $this->api_model->check_email_exists($email);

        if ($email_exists != FALSE) {
            //Update password requested time
            $this->api_model->update_password_requested_time($email);
            //Encode user email to attach to the url 
            $hash_email = md5($email);
            $url = base_url() . "welcome/change_password/" . $hash_email;
            //Send email with credentials to user
            $this->email->from('info@domain.com', 'App Name');
            $this->email->to($email);
            $this->email->subject('Password Change Request');
            $this->email->message('Dear Customer,
							 <br/><br/>
							 Please click on the following LINK to create your new password:<br/>
							 <a href="' . $url . '">Change Password</a><br/>
                                                         <br/><br/>
							 Best Regards,<br/>');
            $this->email->send();
//                        echo $this->email->print_debugger(); die;
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Email with url to get password sent Successfully."),
                'body' => array("status" => "true")
            );
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "Email Record does not exist"),
                'body' => array("status" => "false")
            );
        }
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function change_password_post() {
        $apiToken = $this->post('apiToken');
        $oldPassword = $this->post('oldPassword');
        $encryptedOldPassword = hash('sha512', $oldPassword);

        $newPassword = $this->post('newPassword');
        $encryptedNewPassword = hash('sha512', $newPassword);
        //Match the entered oldPassword password with stored password
        $password_check = $this->api_model->match_old_password($apiToken, $encryptedOldPassword);
        if ($password_check == TRUE) {
            $modify_password = $this->api_model->modify_forgot_password($apiToken, $encryptedNewPassword);

            if ($modify_password == 1) {
                $message = array(
                    'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Password Updated Successfully"),
                    'body' => array()
                );
            } else {
                $message = array(
                    'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to update your new password"),
                    'body' => array()
                );
            }
        } else {

            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "Your entered password did not match your old password"),
                'body' => array()
            );
        }




        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }
    
      public function update_password_post() {

        $remember_token = $this->input->post('hash_email');
        $new_password = $this->input->post('new_password');

        $confirm_password = $this->input->post('confirm_password');



        if ($new_password != $confirm_password) {

            $this->session->set_flashdata('message', 'You New Password & Confirm Password does not match.');

            redirect('welcome/change_password/' . $remember_token);
        } else {

            //Encrypt new password

            $encrypted_password = hash('sha512', $new_password);



//Check if the url is expired or not (password_requested_time is less than 1 hour )
            //Get password requested time saved in db

            $password_requested_time = $this->api_model->get_password_requested_time($remember_token);

            if ($password_requested_time != FALSE) {

                $request_time = $password_requested_time[0]->password_requested_time;



                //Get current date time

                $current_time = date('Y-m-d H:i:s');



                $diff = strtotime($current_time) - strtotime($request_time);

                $diff_in_hrs = $diff / 3600;

                if ($diff_in_hrs < 1) {

                    $save_user_password = $this->api_model->save_new_password($remember_token, $encrypted_password);

                    if ($save_user_password != FALSE) {



                        $data['message'] = "Your Password has been updated. Please login using your new password in the Mobile App. ";

                        $this->load->view('message', $data);
                    } else {

                        $data['message'] = "Sorry! We are unable to update your password this time.";

                        $this->load->view('message', $data);
                    }
                } else {

                    $data['message'] = "Your Password Change Link has been expired. Please start the password change process again.";

                    $this->load->view('message', $data);
                }
            } else {

                $data['message'] = "It seems you've already updated or have not requested a password change. Please start the password change process again.";

                $this->load->view('message', $data);
            }
        }
    }

    public function change_password_without_old_post() {

        $email = $this->post('email');
        $newPassword = $this->post('newPassword');
        $encryptedNewPassword = hash('sha512', $newPassword);
        //Match the entered oldPassword password with stored password
        $modify_password = $this->api_model->modify_forgot_password_by_email($email, $encryptedNewPassword);

        if ($modify_password == 1) {
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Password Updated Successfully"),
                'body' => array()
            );
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to update your new password"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function logged_me_in_post() {
        $authToken = $this->post('apiToken');
        $email = $this->api_model->getEmailByAuthToken($authToken);
        if (!empty($email)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "email is verified"), 'body' => array("vemail" => $email));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "email not in parameters"), 'body' => array("vemail" => $email));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

}
