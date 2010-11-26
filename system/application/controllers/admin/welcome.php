<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->template->set('page_title', 'Administer Webzash');

		$data['current_account'] = "";
		/* Loading account data */
		$valid_db_q = mysql_query('DESC settings');
		if ($valid_db_q)
		{
			$account_q = $this->db->query('SELECT * FROM settings WHERE id = 1');
			if ($account_d = $account_q->row())
			{
				$data['current_account'] .= "Currently active account is ";
				$data['current_account'] .= "<b>" . $account_d->name . "</b>";
				$data['current_account'] .= " from " . "<b>" . date_mysql_to_php($account_d->ay_start) . "</b>";
				$data['current_account'] .= " to " . "<b>" . date_mysql_to_php($account_d->ay_end) . "</b>";
				$data['current_account'] .= " (change active account)";
			}
		}

		if ($data['current_account'] == "")
			$data['current_account'] = "No account is currently active. You can " .anchor('admin/create', 'create', array('title' => 'Create a new account', 'style' => 'color:#000000')) . " a new account or activate an existing account";

		$this->template->load('admin_template', 'admin/welcome', $data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */
