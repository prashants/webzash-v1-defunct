<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		redirect('inventory/account');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/inventory/welcome.php */
