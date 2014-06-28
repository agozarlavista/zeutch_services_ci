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

        //$this->load->model('mz_zeutchers_model');
        //$this->load->library('email');
        /*$this->load->helper('sort');
        $this->load->helper('token');
        $this->load->helper('thumbnails');
        $this->load->helper('profilecheck');
        $this->load->helper('translate_missions');*/
        header('X-Frame-Options: DENY');
    }
    public function index(){
        echo "login";
        //redirect('/', 'refresh');
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
    /* ----------------------- WEBSERVICE REQUEST ACCOUNT PRO ----------------------- */
    public function get_customer_infos_if_exist(){
        /* on va checker si l'email correspond :
               1 -> a un walker
               2 -> a un admin
               3 -> a un customer
               puis on check les infos walker et les infos customer et on met a jour la table customer avec les datas walker si elles existent
               nom du webservice : get_customer_infos_if_exist

               on retourne le code 200 si le compte admin existe déjà pour cet email
               on retourne 201 lorsque le compte vient d'être créé.
        */
        $walker_exist = false;
        $admin_exist = false;
        $customer_exist = false;

        $array = $this->input->post();

        $this->load->helper('profilecheck');
        $this->load->model('walker_model');
        $this->load->model('customer_model');
        $this->load->model('admin_model');

        $walker = $this->walker_model->get(array('email' => $array['email']));
        $customer = $this->customer_model->get(array('email' => $array['email']));
        $admin = $this->admin_model->get(array('email' => $array['email']));

        $this->load->library('encrypt');
        $pwd = strtolower(random_string('alnum', 6));
        $pwd_enc = sha1($pwd);

        $first_name = '';
        $last_name = '';
        $phone_number = '';
        $company = '';
        $status = '';
        $address = '';
        $fb_id = '';
        $company_siret = '';
        $company_ape = '';
        $terms = '';
        $avatar = '';
        $locale = '';
        $cp = '';
        $walker_id = '';

        if(count($walker) >= 1){
            $walker_exist = true;

            $walker_id = $walker->id;
            $first_name = $walker->first_name;
            $last_name = $walker->last_name;
            $phone_number = $walker->mobile_tel;
            $fb_id = $walker->fb_id;
            $address = $walker->address;
            $avatar = $walker->avatar;
            $locale = $walker->locale;
            $cp = $walker->cp;

            //$pwd_enc = $walker[0]->password;
            // Vous avez déjà un compte walker pour cet email
        }else{
            // create walker entry
            // $walker_id = $this->walker_model->add(array('email' => $array['email'], 'password' => $pwd_enc));
        }

        if(count($admin) >= 1){
            $admin_exist = true;
            $admin_id = $admin->id;
            // return vous avez déjà un compte admin pour cet email
        }else{
            // create admin entry
            $admin_id = $this->admin_model->add(array('email' => $array['email'], 'password' => $pwd_enc, 'rights'  => 36028797018963967, 'level' => 1));
        }

        if(count($customer) >= 1){
            $customer_exist = true;

            $customer_update = array();
            $customer_id = $customer[0]->id;
            $customer_update['id'] = $customer_id;
            if(isset($customer[0]->first_name)){
                if($customer[0]->first_name != ''){
                    $first_name = $customer[0]->first_name;
                }else{
                    $customer_update['first_name'] = $first_name;
                }
            }
            if(isset($customer[0]->last_name)){
                if($customer[0]->last_name != ''){
                    $last_name = $customer[0]->last_name;
                }else{
                    $customer_update['last_name'] = $last_name;
                }
            }
            if(isset($customer[0]->phone_number)){
                if($customer[0]->phone_number != ''){
                    $phone_number = $customer[0]->phone_number;
                }else{
                    $customer_update['phone_number'] = $phone_number;
                }
            }
            if(isset($customer[0]->fb_id)){
                if($customer[0]->fb_id != ''){
                    $fb_id = $customer[0]->fb_id;
                }else{
                    $customer_update['fb_id'] = $phone_number;
                }
            }
            if(isset($customer[0]->address)){
                if($customer[0]->address != ''){
                    $address = $customer[0]->address;
                }else{
                    $customer_update['address'] = $address;
                }
            }
            if(isset($customer[0]->cp)){
                if($customer[0]->cp != ''){
                    $cp = $customer[0]->cp;
                }else{
                    $customer_update['cp'] = $cp;
                }
            }
            if(isset($customer[0]->admin_id) && $customer[0]->admin_id != $admin_id)
                $customer_update['admin_id'] = $admin_id;

            if(isset($customer[0]->walker_id) && $customer[0]->walker_id != $walker_id)
                $customer_update['walker_id'] = $walker_id;

            $customer_id = $customer[0]->id;

            $this->customer_model->update($customer_update);

            $customer = $this->customer_model->get(array('email' => $array['email']));
            $company_siret = $customer[0]->company_siret;
            $company_ape = $customer[0]->company_ape;

            // return vous avez déjà un compte client pour cet email
        }else{
            // create customer entry
            $customer_id = $this->customer_model->add(array('email' => $array['email'], 'password' => $pwd_enc));

            $customer_update = array(
                "id" => $customer_id,
                "walker_id" => $walker_id,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "phone_number" => $phone_number,
                "fb_id" => $fb_id,
                "address" => $address,
                "avatar" => $avatar,
                "locale" => $locale,
                "cp" => $cp,
                "admin_id" => $admin_id
            );
            $this->customer_model->update($customer_update);
            //$this->customer_model->update($customer_update);
            $customer = $this->customer_model->get(array('email' => $array['email']));
        }
        if($customer_exist){
            echo '{"code":200}';
            exit();
        }
        //var_dump($customer);
        $data_mail = array(
            'email' => $customer[0]->email,
            'user_id' => $customer[0]->id,
            'password' => $pwd,
            'token_validator' => sha1($customer[0]->id . $customer[0]->email . $customer[0]->password),
            'type'  => 'community',
            'user_type' => 'pro'
        );
        $this->load->model('email_model');
        $this->email_model->send($data_mail);
        //$this->send_an_email($data_mail);

        echo json_encode($customer);
    }
    public function update_register_form(){
        $array = $this->input->post();
        $this->load->model('customer_model');
        $this->customer_model->update($array);
        $customer = $this->customer_model->get(array('email' => $array['email']));
        echo json_encode($customer);
    }
    public function hello(){
        echo 'say hello';
    }
    public function end_register_form(){
        // sended_vars = customer_id || email || message || be_contacted || CGU || select_where || complement_where || first_name || last_name || phone_number || company || status
        $array = $this->input->post();
        // STEP #1 envoi du mail de validation au client

        /*$this->load->library('email');
        $this->lang->load('email');

        $this->email->from('no-reply@clicandwalk.com', 'ClicAndWalk');
        $this->email->to($array['email']);

        $raw_message = base_url() . 'login_api/' . $array['customer_id'] . sha1($array['CGU']);
        $html_message = sprintf($this->load->view('email/password', '', true), $raw_message);

        $this->email->subject($this->lang->line('email_lostpasswd_title'));
        $this->email->message($html_message);

        $this->email->send();*/
        $message = "Je souhaite être contacté par un commercial ? " . $array['be_contacted'] . " | J'ai connu Clic and Walk par " . $array['where'] . " | complement : " . $array['who'] . " | message : " . $array['message'];
        // STEP #2 envoi du mail chez Clic and Walk ajout du message dans la pile _contact
        $this->load->model('contacts_model');
        if(isset($array['first_name']) && isset($array['last_name']) && isset($array['email']) && isset($array['phone_number']) && isset($array['message'])){
            $this->contacts_model->add(array(
                "type" => "customer",
                "date" => date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000)),
                "name" => $array['first_name'] . $array['last_name'],
                "ip" => $this->input->ip_address(),
                "email" => $array['email'],
                "tel" => $array["phone_number"],
                "message" => $message,
                "viewed" => "no"
            ));
            echo '{"code":200}';
            exit;
        }
        return '{"code":400}';
    }
}
    /* ----------------------- END WEBSERVICE REQUEST ACCOUNT PRO ----------------------- */


    /*public function send_an_email($data_mail){
        if(!isset($data_mail['email'])){
            exit;
        }
        if(!isset($data_mail['user_id'])){
            exit;
        }
        $this->load->library('email');
        $this->lang->load('email');

        $this->email->from('no-reply@clicandwalk.com', 'Clic and Walk');
        $this->email->to($data_mail['email']);

        //$token_validator = sha1($user_id . $email . $pwd_enc);

        $title = $this->lang->line('email_default_title');

        // pour changer les couleurs de background du mail selon les rubriques envoyer un type : pro(bleu foncé)/community(vert)/walker(bleu ciel)/bank(violet)/error(orange) valeur par default (bleu ciel)
        if(isset($data_mail['type'])){
            $data['type'] = $data_mail['type'];
        }
        if(isset($data_mail['new_password'])){
            $title = $this->lang->line('email_new_password_title');
            $data['title'] = $this->lang->line('email_password_pro_account');
            $data['content_html'] = $this->lang->line('email_password_pro_account_message');
            $data['new_password'] = $data_mail['new_password'];
        }
        if(isset($data_mail['password'])){
            $title = $this->lang->line('email_password_pro');
            $data['title'] = $this->lang->line('email_active_pro_account');
            $data['content_html'] = $this->lang->line('email_active_pro_account_message');
            $data['new_password'] = $data_mail['password'];
        }
        if(isset($data_mail['token_validator'])){
            $title = $this->lang->line('email_create_pro_account_title');
            $data['title'] = $this->lang->line('email_create_pro_account');
            $data['content_html'] = $this->lang->line('email_create_pro_account_message');
            $data['validation_link'] = base_url() . 'login_api/validation/' . $data_mail['user_id'] . '/' . $data_mail['token_validator'];
        }
        if(isset($data_mail['validation'])){
            $title = $this->lang->line('email_validation_pro_account_title');
            $data['title'] = $this->lang->line('email_validation_pro_account');
            $data['content_html'] = $this->lang->line('email_validation_pro_account_message');
        }

        $html_message = $this->load->view('email/template', $data, true);

        $this->email->subject($title);
        $this->email->message($html_message);

        $this->email->send();
    }
}