<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->template->set('page_title', 'Administer Webzash');
		$this->template->load('admin_template', 'admin/welcome');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */
