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


class Wp_api extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header('Access-Control-Allow-Origin: *');

        $this->load->model('wp_post_model');
        header('X-Frame-Options: DENY');
    }
    public function index(){
        echo "wp_api";
        //redirect('/', 'refresh');
        return true;
    }
    public function get_posts(){
        echo "prout";
        //$this->db->connect();
        //$array = $this->input->post();
        //$posts = $this->wp_posts_model->get($array);
        //echo json_encode($posts);
    }
}