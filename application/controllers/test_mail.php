<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*********
* Author: Dipankar Deb
* Date  : 
* Modified By: Suman Chalki
* Modified Date:
* 
* Purpose:
*  Controller For Artists Page 
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/users_model.php
* @link views/non_logue_index.phtml
*        views/logue/logue_index.phtml
*/

include(APPPATH.'controllers/base_controller.php');

class Test_mail extends Base_controller
{
    
    public function __construct()
    {
        try
        {
            $this->load->library('email');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
    public function index() {

        $this->email->from('your@example.com', 'Your Name');
        $this->email->to('hereissuman@gmail.com'); 
        $this->email->cc('another@another-example.com'); 
        $this->email->bcc('them@their-example.com'); 

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');    

        $this->email->send();

        echo $this->email->print_debugger();
        
    }
    
}