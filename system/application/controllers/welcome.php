<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Welcome to Webzash');
		$this->template->load('template', 'welcome_message');
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
