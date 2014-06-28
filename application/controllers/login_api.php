<?php
/* API LOGIN ZEUTCH */
/* ____ ____ _  _ ____ ____ _  _ */
/*    | |    |  |   |  |    |  | */
/*   /  |    |  |   |  |    |  | */
/*  /   |--  |  |   |  |    |--| */
/* |    |    |  |   |  |    |  | */
/* ---- ---- ----   -  ---- -  - */
/* development : simon delamarre */
/* contact : landscapeviewer@gmail.com */
/* l'api login est une api ouverte au service de l'application mobile zeutch basée sur codeigniter, les apis facebook twitter et les webservices landscapeviewer ressources privée */


class Login_api extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header('Access-Control-Allow-Origin: *');

        $this->load->model('zeutcher_model');
        //$this->load->library('email');
        /*$this->load->helper('sort');
        $this->load->helper('token');
        $this->load->helper('thumbnails');
        $this->load->helper('profilecheck');
        $this->load->helper('translate_missions');*/
        header('X-Frame-Options: DENY');
    }
    public function index(){
        redirect('/', 'refresh');
        return true;
    }
    public function login(){
        $array = $this->input->post();
        if(isset($array['fb_id']))
            $zeutcher = $this->zeutcher_model->get(array('fb_id' => $array['fb_id']));
        else if(isset($array['tw_id']))
            $zeutcher = $this->zeutcher_model->get(array('tw_id' => $array['tw_id']));
        else
            $zeutcher = $this->zeutcher_model->get(array('email' => $array['email'], 'pwd' => $array['pwd']));

        echo json_encode($zeutcher);
    }
    public function subscribe(){
        $array = $this->input->post();
        $zeutcher = $this->zeutcher_model->add($array);
        echo json_encode($zeutcher);
    }
}