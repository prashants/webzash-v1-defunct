<?php

class Active extends Controller {

	function Active()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->load->helper('file');
		$this->template->set('page_title', 'Change Active Account');

		$active_accounts = read_file('system/application/controllers/admin/activeaccount.inc');
		$data['accounts'] = explode(';', $active_accounts);
		if (count($data['accounts']) > 1)
			array_pop($data['accounts']);
		$this->template->load('admin_template', 'admin/active', $data);
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */
