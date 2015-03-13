<?php

/* * *******
 * Author: 
 * Date  : 
 * Modified By: 
 * Modified Date:
 * 
 * Purpose:
 * Controller For WALL
 * 
 * 
 */
include(APPPATH . 'controllers/base_controller.php');

class Intercession_testimony extends Base_controller {

    private $testimony_pagination_per_page = 10;

    public function __construct() {

        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            /*$this->load->model('users_model');
            $this->load->model('holy_place_model');
            $this->load->model('bible_fruits_model');
            $this->load->model('prayer_wall_photos_model');
            $this->load->model('prayer_wall_model');
            $this->load->model('prayer_commit_model');*/
            $this->load->model('intercession_model');

            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index() {
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/tab.js',*/
                'js/production/prayer_wall.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $data['testimony_pagination_per_page'] = $this->testimony_pagination_per_page;


            #$this->session->set_userdata('search_fo_intercession','');
            #$this->session->set_userdata('search_non_eaxct_req_i','');
            $this->session->set_userdata('filter_intercession', '');

            ob_start();
            $this->intercession_testimony_ajax_pagination();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['prayer_req_ajax_content'] = $content_obj->html;
            $data['no_of_result'] = $content_obj->no_of_result;
            $data['view_more'] = $content_obj->view_more;
            ob_end_clean();

            # view file...

            $curr_url  = curPageURL();

            $this->session->set_userdata('current_url', $curr_url);

            //$data['current_url'] = $this->session->get_userdata('current_url');

            $VIEW = "logged/holy_place/testimony/intercession_testimony.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function intercession_testimony_ajax_pagination($page = 0) {


        $cur_page = $page + $this->testimony_pagination_per_page;

        $data = $this->data;


        $where = "";
        $non_exact_where = "";
        $s_non_exact_where = "";

        /* if($this->input->post('if_posted')=='y')
          {

          $location = get_formatted_string(trim($this->input->post('txt_location')));
          if($location != '')
          {
          $location_arr = explode(',',$location);
          }
          $total_locations = count($location_arr);
          if($total_locations)
          {


          for($i=0;$i<$total_locations;$i++)
          {
          if($i == 0){
          $where .= " (mst_c.s_country_name like '".trim($location_arr[$i])."%' OR  i.s_state like '".trim($location_arr[$i])."%' OR i.s_city like '".trim($location_arr[$i])."%')";


          $non_exact_where .= " (mst_c.s_country_name like '".trim($location_arr[$i])."%' OR i.s_state like '".trim($location_arr[$i])."%' OR i.s_city like '".trim($location_arr[$i])."%')";
          }
          else{

          $where .= " AND (mst_c.s_country_name like '".trim($location_arr[$i])."%' OR  i.s_state like '".trim($location_arr[$i])."%' OR i.s_city like '".trim($location_arr[$i])."%')";


          $non_exact_where .= " OR (mst_c.s_country_name like '".trim($location_arr[$i])."%' OR i.s_state like '".trim($location_arr[$i])."%' OR i.s_city like '".trim($location_arr[$i])."%')";
          }
          }


          }



          $time_from  = get_formatted_string(trim($this->input->post('txt_time_from')));
          $date_from  = $this->input->post('date_from');
          $time_to    = get_formatted_string(trim($this->input->post('txt_time_to')));
          $date_to    = $this->input->post('date_to');



          if($time_from!='')
          {
          if($where!='')
          {
          $where .= " AND DATE_FORMAT(i.dt_start_date , '%H:%i') ='{$time_from}'";
          }
          else
          {
          $where .= " DATE_FORMAT(i.dt_start_date , '%H:%i') ='{$time_from}'";
          }
          if($non_exact_where!='')
          {
          $non_exact_where .= " OR DATE_FORMAT(i.dt_start_date , '%H:%i') ='{$time_from}'";
          }
          else
          {
          $non_exact_where .= " DATE_FORMAT(i.dt_start_date , '%H:%i') ='{$time_from}'";
          }


          }
          if($date_from!='')
          {
          if($where!='')
          {
          $where .= " AND date(i.dt_start_date) ='".get_db_dateformat($date_from,'/')."'";
          }
          else
          {
          $where .= " date(i.dt_start_date) ='".get_db_dateformat($date_from,'/')."'";
          }
          if($non_exact_where!='')
          {
          $non_exact_where .= "OR date(i.dt_start_date) ='".get_db_dateformat($date_from,'/')."'";
          }
          else
          {
          $non_exact_where .= " date(i.dt_start_date) ='".get_db_dateformat($date_from,'/')."'";
          }


          }
          if($time_to!='')
          {
          if($where!='')
          {
          $where .= " AND DATE_FORMAT(i.dt_end_date , '%H:%i') ='{$time_to}'";
          }
          else
          {
          $where .= " DATE_FORMAT(i.dt_end_date , '%H:%i') ='{$time_to}'";
          }
          if($non_exact_where!='')
          {
          $non_exact_where .= " OR DATE_FORMAT(i.dt_end_date , '%H:%i') ='{$time_to}'";
          }
          else
          {
          $non_exact_where .= " DATE_FORMAT(i.dt_end_date , '%H:%i') ='{$time_to}'";
          }


          }
          if($date_to!='')
          {
          if($where!='')
          {
          $where .= " AND date(i.dt_end_date) = '".get_db_dateformat($date_to,'/')."'";
          }
          else
          {
          $where .= " date(i.dt_end_date) = '".get_db_dateformat($date_to,'/')."'";
          }
          if($non_exact_where!='')
          {
          $non_exact_where .= " OR date(i.dt_end_date) = '".get_db_dateformat($date_to,'/')."'";
          }
          else
          {
          $non_exact_where .= " date(i.dt_end_date) = '".get_db_dateformat($date_to,'/')."'";
          }


          }
          $this->session->set_userdata('search_fo_intercession','');
          $this->session->set_userdata('search_fo_intercession',$where);
          $this->session->set_userdata('search_non_eaxct_req_i',$non_exact_where);



          }
         */


        $WHERE = "";

        if ($this->input->post('if_posted') == 'y') {


            $key_word = $this->input->post('key_word');
            if ($key_word == 'view_all') {
                $WHERE = "";
            } else if ($key_word == 'my_commitment') {
                $WHERE = " AND c.i_user_id={$this->i_profile_id}";
            }

            //echo $WHERE;
            $this->session->set_userdata('filter_intercession', $WHERE);
        }
        //$this->session->set_userdata('search_fo_intercession','');
        if ($this->session->userdata('search_fo_intercession') != '')
            $where = "AND ( " . $this->session->userdata('search_fo_intercession') . " )";
        if ($this->session->userdata('search_non_eaxct_req_i') != '')
            $s_non_exact_where = "AND ( " . $this->session->userdata('search_non_eaxct_req_i') . " )"; //exit;



            
//echo "where : ".$where;
        //echo "s_non_exact_where : ".$s_non_exact_where." === ";
        //$result = $this->intercession_model->get_all_intercession_testimony($where,$s_non_exact_where,intval($page), $this->testimony_pagination_per_page);
        $where = $this->session->userdata('filter_intercession');
        $result = $this->intercession_model->get_intercession_testimony($where, intval($page), $this->testimony_pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->intercession_model->get_total_intercession_testimony($where);
        //pr($result,1);
        $data['arr_testimony'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->testimony_pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/holy_place/testimony/intercession_testimony_ajax.phtml";

        /* if( is_array($result) && count($result) ) {
          $content = $this->load->view( $VIEW_FILE , $data, true);
          }
          else {
          $content = '';
          } */
        $content = $this->load->view($VIEW_FILE, $data, true);
        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'no_of_result' => $data['no_of_result'], 'view_more' => $view_more, 'cur_page' => $data['current_page']));
    }

}

// end of controller