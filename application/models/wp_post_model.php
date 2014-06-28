<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Wp_Zeutch_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _required($required, $data) {
        foreach ($required AS $field)
            if (!isset($data[$field]))
                return false;
        return true;
    }

    public function get($options = array()) {
        if (!empty($options['order_by']) && !empty($options['order']))
            $this->db->order_by($options['order_by'], $options['order']);
        else if (!empty($options['order_by']))
            $this->db->order_by($options['order_by']);

        if (!empty($options['limit']) && !empty($options['offset']))
            $this->db->limit($options['limit'], $options['offset']);
        else if (!empty($options['limit']))
            $this->db->limit($options['limit']);

        $query = $this->db->get('wp_posts');
        return $query->result();
    }

    public function add($options = array()) {
        if (!$this->_required(array('email', 'password'), $options))
            return false;

        $this->db->insert('mz_zeutchers', $options);
        $this->get($options);
    }

    public function update($options = array()) {
        if (!$this->_required(array('id'), $options)) {
            return false;
        }
        $this->db->where('id', $options['id']);

        if (isset($options['fb_id']))
            $this->db->set('fb_id', (int) $options['fb_id']);
        if (isset($options['tw_id']))
            $this->db->set('tw_id', (int) $options['tw_id']);
        if (isset($options['avatar']))
            $this->db->set('avatar', $options['avatar']);
        if (isset($options['pseudo']))
            $this->db->set('pseudo', $options['pseudo']);
        if (!empty($options['email']))
            $this->db->set('email', $options['email']);
        if (!empty($options['password']))
            $this->db->set('password', $options['password']);
        if (isset($options['first_name']))
            $this->db->set('first_name', $options['first_name']);
        if (isset($options['last_name']))
            $this->db->set('last_name', $options['last_name']);
        if (isset($options['status']))
            $this->db->set('status', $options['status']);
        if (isset($options['address']))
            $this->db->set('address', $options['address']);
        if (isset($options['mobile_number'])){
            $this->db->set('mobile_number', $options['mobile_number']);
        }
        if (isset($options['country']))
            $this->db->set('country', (int) $options['country']);
        if (isset($options['nationality']))
            $this->db->set('nationality', (int) $options['nationality']);
        if (isset($options['cp']))
            $this->db->set('cp', (int) $options['cp']);
        if (!empty($options['secret']))
            $this->db->set('secret', $options['secret']);
        if (isset($options['locale']))
            $this->db->set('locale', $options['locale']);

        $returnedDatas = $this->db->update('mz_zeutchers');

        return $returnedDatas;
    }
    public function delete($id) {
        if (is_numeric($id) && $this->count(array('id' => $id)) == 1)
            return $this->db->delete('mz_zeutchers', array('id' => intval($id)));
        return false;
    }
}
