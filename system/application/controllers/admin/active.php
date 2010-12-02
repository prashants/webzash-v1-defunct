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
			$active_db = array(
				'db_settings' => FALSE,
				'db_hostname' => "",
				'db_port' => "",
				'db_name' => "",
				'db_username' => "",
				'db_password' => ""
			);
			$this->session->set_userdata($active_db);
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

			/* Setting new account database details in session */
			$active_db = array(
				'db_settings' => TRUE,
				'db_hostname' => $active_accounts['db_hostname'],
				'db_port' => $active_accounts['db_port'],
				'db_name' => $active_accounts['db_name'],
				'db_username' => $active_accounts['db_username'],
				'db_password' => $active_accounts['db_password']
			);
			$this->session->set_userdata($active_db);
			$this->messages->add("Active account changed successfully", 'success');
			redirect('admin');
		}
		return;
	}
}

/* End of file active.php */
/* Location: ./system/application/controllers/admin/active.php */
