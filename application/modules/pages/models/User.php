<?php
class user extends CI_Model {

	var $data = '';

	public function __construct()
	{
		parent::__construct();
	}

	function register_new($p_data)
	{
//		log_message("ERROR","user::register_new called");
//		log_message("ERROR", print_r($p_data,TRUE));
		$query = $this->db->where(array('email' => $p_data['email']))->get('user', 1);
		if ( $query->num_rows() > 0 ){
			return array(
				'success' => false,
				'message' => 'Email already exists'
			);
		}
		$data = array(
			'email' => $p_data['email'],
			'name' => $p_data['name'],
			'phone' => $p_data['phone'],
			'password' => md5($p_data['password']),
			'created' => time()
		);
		$this->db->insert('user', $data);
		$new_id = $this->db->insert_id();
		return array(
			'success' => true,
			'user_id' => $new_id
		);
	}

	function login($p_data)
	{
//		log_message("ERROR","user::login called");
//		log_message("ERROR", print_r($p_data,TRUE));
		$query = $this->db->where(array('email' => $p_data['email']))->get('user', 1);
		if ( $query->num_rows() == 0 ){
			return array(
				'success' => false,
				'message' => 'Email not Found'
			);
		}
		$user = $query->row_array();
		if ( $user['password'] != md5($p_data['password']) ){
			return array(
				'success' => false,
				'message' => 'Password Incorrect'
			);
		}

		$data = array(
			'user_id' => $user['id'],
			'email' => $user['email'],
			'is_logged_in' => true,
		);
		$this->session->set_userdata($data);

		return array(
			'success' => true,
			'user' => $user
		);
	}

	function update($p_data)
	{
//		log_message("ERROR","user::update called");
//		log_message("ERROR", print_r($p_data,TRUE));

		$data = array(
			'email' => $p_data['email'],
			'name' => $p_data['name'],
			'phone' => $p_data['phone'],
		);

		if ( strlen($p_data['password']) > 0 ){
			$data['password'] = md5($p_data['password']);
		}

		$this->db->where('id', (integer) $p_data['user_id']);
		$this->db->update('user', $data);
//
		return array(
			'success' => true,
			'user_id' => (integer) $p_data['user_id']
		);
	}

	function delete($p_data)
	{
//		log_message("ERROR","user::delete called");
//		log_message("ERROR", print_r($p_data,TRUE));
//
//		log_message("ERROR", "SESSION: ".print_r($this->session->userdata(),TRUE));

		if ( (integer) $this->session->userdata('user_id') == (integer) $p_data['user_id'] ){
			return array(
				'success' => false,
				'message' => 'You cannot delete yourself.'
			);
		}

		$this->db->where('id', (integer) $p_data['user_id']);
		$this->db->delete('user');
		return array(
			'success' => true,
			'user_id' => (integer) $p_data['user_id']
		);
	}

	function get_all_users(){
		$query = $this->db->select('id,email,name,phone')->where(array('deleted' => NULL))->get('user');
		$users = $query->result();

		return array(
			'success' => true,
			'users' => $users
		);
	}

}
