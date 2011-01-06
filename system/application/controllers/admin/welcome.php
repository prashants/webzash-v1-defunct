<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();

		/* Check access */
		if ( ! check_access('administer'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Administer Webzash');

		/* Check status report */
		$this->load->library('statuscheck');
		$statuscheck = new Statuscheck(); 
		$statuscheck->check_permissions();
		if (count($statuscheck->error_messages) > 0)
		{
			$this->messages->add('One or more problems were detected with your installation. Check the ' . anchor('admin/status', 'Status report', array('title' => 'Check Status report', 'class' => 'anchor-link-a')) . ' for more information.', 'error');
		}

		$this->template->load('admin_template', 'admin/welcome');
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */
