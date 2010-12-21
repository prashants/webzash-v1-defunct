<?php

class Status extends Controller {

	function Status()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->load->library('statuscheck');
		$this->template->set('page_title', 'Status report');
		$statuscheck = new Statuscheck(); 
		$statuscheck->check_permissions();
		$data['error_messages'] = $statuscheck->error_messages;
		$this->template->load('admin_template', 'admin/status', $data);
		return;
	}
}

/* End of file status.php */
/* Location: ./system/application/controllers/admin/status.php */
