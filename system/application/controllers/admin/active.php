<?php

class Active extends Controller {

	function Active()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Change Active Account');

		/* Getting list of files in the config/accounts directory */
		$accounts_list = get_filenames('system/application/config/accounts');
		$data['accounts'] = array();
		if ($accounts_list)
		{
			foreach ($accounts_list as $row)
			{
				/* Only include file ending with .ini */
				if (substr($row, -4) == ".ini")
				{
					$ini_label = substr($row, 0, -4);
					$data['accounts'][$ini_label] = $ini_label;
				}
			}
		}

		/* Form validations */
		$this->form_validation->set_rules('accounts', 'Accounts', 'trim|required');

		/* Repopulating form */
		if ($_POST)
		{
			/* Unsetting all database configutaion */
			$this->session->unset_userdata('db_active_label');
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('admin_template', 'admin/active', $data);
			return;
		}
		else
		{
			$db_label = $this->input->post('accounts', TRUE);
			$ini_file = "system/application/config/accounts/" . $db_label . ".ini";

			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$this->messages->add("Account setting file is missing", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}

			/* Parsing database ini file */
			$active_accounts = parse_ini_file($ini_file);
			if ( ! $active_accounts)
			{
				$this->messages->add("Invalid account setting file", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}

			/* Check if all needed variables are set in ini file */
			if ( ! isset($active_accounts['db_hostname']))
			{
				$this->messages->add("Hostname missing from account setting file", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_port']))
			{
				$this->messages->add("Port missing from account setting file. Default MySQL port is 3306", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_name']))
			{
				$this->messages->add("Database name missing from account setting file", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_username']))
			{
				$this->messages->add("Database username missing from account setting file", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_password']))
			{
				$this->messages->add("Database password missing from account setting file", 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}

			/* Setting new account database details in session */
			$this->session->set_userdata('db_active_label', $db_label);
			$this->messages->add("Active account settings changed", 'success');
			redirect('admin');
		}
		return;
	}
}

/* End of file active.php */
/* Location: ./system/application/controllers/admin/active.php */
