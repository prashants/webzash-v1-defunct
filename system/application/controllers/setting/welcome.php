<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();

		/* Check access */
		if ( ! check_access('change account settings'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}

	function index()
	{
		$this->template->set('page_title', 'Settings');
		$this->template->load('template', 'setting/index');
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/setting/welcome.php */
