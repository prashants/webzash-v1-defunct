<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$page_data['page_title'] = "Welcome to Webzash";
		$this->load->view('template/header', $page_data);
		$this->load->view('welcome_message');
		$this->load->view('template/footer');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
