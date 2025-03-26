<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Pages extends MX_Controller {
    
    var $data;

    public function __construct()
    {
        parent::__construct();
        $this->data         = array(
            'page_content'  => FALSE
        );
//        modules::run('layout/autoload/initiate');
        $this->data['form_result'] = (object) array('form_name' => FALSE, 'form_success' => FALSE);
		$this->load->model('user');
		$this->load->helper('url');
    }

    public function index()
    {
        $this->data['meta_keywords']    = '';
        $this->data['meta_description'] = '';
        $this->data['meta_title']       = '';
        $this->data['template']         = 'home';

        $this->data['site_name']        = config_item('site_name');       
        $this->data['css']              = config_item('css');
        $this->data['js']               = config_item('js');
        
        if ($this->input->post('ref'))
        {
            $this->data['form_result'] = $this->_form_submission($this->data);
        }

        $page = $this->uri->segment(1);

		if ( $page == 'logout' ){
			$this->session->sess_destroy();
			$this->session->set_flashdata('message','You have logged out.');
			//redirect to home page
			redirect('/');
		}
//		echo "page: ".$page."\n";
        if(!$page) $page = 'home';
        if ( ! file_exists('application/modules/pages/views/'.$page.'.php') )
        {
			log_message("ERROR", "calling 404");
            // Whoops, we don't have a page for that!
            return show_404();
        }

//		log_message("ERROR", "session: ".print_r($this->session->userdata(),TRUE));
		$this->data['login_message'] = NULL;
		if ( $page == 'members' ){
			if ( !$this->session->userdata('is_logged_in') ){
				$this->session->set_flashdata('message','You must be logged in.');
				//redirect to home page
				redirect('/login');
			}
			$return = $this->user->get_all_users();
			$this->data['users'] = $return['users'];
		}

        $this->data['menu'] = config_item('menu');
        $this->data['page_name'] = $page;

        $this->data['page_content'] = $this->load->view($page, $this->data, TRUE);
        $this->load->view('template', $this->data);
    }

    function _form_submission($data = FALSE)
    {
        $form = $this->input->post('ref'); 

        $method = '_'.$form.'_form_validation';

		log_message("ERROR", "_form_submission form: ".$form);

        if (is_callable(array($this, $method))) 
        {
			log_message("ERROR", "method callable: ".$method);
            call_user_func(array($this, $method));

            $this->form_validation->set_error_delimiters('', '<br />');
        
            if($this->form_validation->run()) 
            {
				log_message("ERROR", "done calling form validation");
				if ( strtolower($form) == 'registration' ){
					$return = $this->user->register_new($_POST);
					if ( !$return['success'] ){
						return (object) array('form_name' => $form, 'form_success' => FALSE, 'form_error_message' => $return['message']);
					} else {
						$this->session->set_flashdata('message','You have registered successfully. Please log in.');
						redirect('/login', 'refresh');
					}
				}
				if ( strtolower($form) == 'login' ){
					$return = $this->user->login($_POST);
					if ( !$return['success'] ){
						return (object) array('form_name' => $form, 'form_success' => FALSE, 'form_error_message' => $return['message']);
					} else {
						redirect('/members', 'refresh');
					}
				}
				if ( strtolower($form) == 'update_user' ){
					$return = $this->user->update($_POST);
					if ( !$return['success'] ){
						$this->session->set_flashdata('message',$return['message']);
						redirect('/members', 'refresh');
					} else {
						$this->session->set_flashdata('message','Member Details Updated');
						redirect('/members', 'refresh');
					}
				}
				if ( strtolower($form) == 'delete_user' ){
					$return = $this->user->delete($_POST);
					if ( !$return['success'] ){
						$this->session->set_flashdata('message',$return['message']);
						redirect('/members', 'refresh');
					} else {
						$this->session->set_flashdata('message','Member Deleted');
						redirect('/members', 'refresh');
					}
				}
            } else {
				log_message("ERROR", "could not call form validation");
			}
        } else {
			log_message("ERROR", "method not callable: ".$method);
		}
        unset($_POST['ref']);
        return (object) array('form_name' => $form, 'form_success' => FALSE);
    }

     private function _general_enquiry_form_validation()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
    }
	private function _registration_form_validation()
	{
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
		$this->form_validation->set_rules('phone', 'Phone', 'trim');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
	}
	private function _login_form_validation()
	{
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
	}

	private function _update_user_form_validation()
	{
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
		$this->form_validation->set_rules('phone', 'Phone', 'trim');
		$this->form_validation->set_rules('password', 'Password', 'trim');
	}

	private function _delete_user_form_validation()
	{
		$this->form_validation->set_rules('user_id', 'User ID', 'required');
	}


    public function sitemap()
    {
        $this->data['pages'] = config_item('menu');

        header("Content-Type: text/xml;charset=iso-8859-1");
        $this->load->view('sitemap', $this->data);
    }
    
}
