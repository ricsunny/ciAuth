<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('api_model');
        $this->load->helper('date');
        $this->load->helper('form');
        //Loading encryption class to encrypt and decrypt password
        $this->load->library('encrypt');
    }

    public function change_password($hash_email = '') {
        $data['hash_email'] = $hash_email;
        $this->load->view('change_password', $data);
    }

    public function update_password() {
        $hash_email = $this->input->post('hash_email');

        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        if ($new_password != $confirm_password) {
            $this->session->set_flashdata('message', 'You New Password & Confirm Password does not match.');
            redirect('welcome/change_password/' . $hash_email);
        } else {
            //Encrypt new password
            $encrypted_password = hash('sha512', $new_password);

//Check if the url is expired or not (password_requested_time is less than 1 hour )
            //Get password requested time saved in db
            $password_requested_time = $this->api_model->get_password_requested_time($hash_email);
            if ($password_requested_time != FALSE) {
                $request_time = $password_requested_time[0]->password_requested_time;

                //Get current date time
                $current_time = date('Y-m-d H:i:s');

                $diff = strtotime($current_time) - strtotime($request_time);
                $diff_in_hrs = $diff / 3600;

                if ($diff_in_hrs < 1) {
                    $save_user_password = $this->api_model->save_new_password($hash_email, $encrypted_password);
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

    public function verify_email($hash_email = '') {

        $activate_account = $this->api_model->activate_account($hash_email);
        if ($activate_account != FALSE) {
            $data['title'] = "Email Verification";
            $data['message'] = "Your Email has been verified. You would be able to login into the Mobile App now. ";
            $this->load->view('message', $data);
        } else {
            $data['title'] = "Email Verification";
            $data['message'] = "Sorry! We are unable to verify your email at this time.";
            $this->load->view('message', $data);
        }
    }

}
