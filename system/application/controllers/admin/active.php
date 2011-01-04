<?php

class Active extends Controller {

	function Active()
	{
		parent::Controller();

		/* Check access */
		if ( ! check_access('administer'))
		{
			$this->messages->add('Permission denied', 'error');
			redirect('');
			return;
		}

		return;
	}
	
	function index($url_label_name = NULL)
	{
		$this->template->set('page_title', 'Change Active Account');

		/* If label specified in URL */
		if ($url_label_name)
		{
			$url_label_name = $this->input->xss_clean($url_label_name);
			$data['account'] = $url_label_name;
		} else {
			$data['account'] = "";
		}

		/* Getting list of files in the config - accounts directory */
		$accounts_list = get_filenames($this->config->item('config_path') . 'accounts');
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
		if ( ! $url_label_name)
		{
			$this->form_validation->set_rules('account', 'Account', 'trim|required');
		}

		/* Repopulating form */
		if ($_POST)
		{
			/* Unsetting all database configutaion */
			$this->session->unset_userdata('db_active_label');
			$data['account'] = $this->input->post('account', TRUE);
		}

		/* Validating form : only if label name is not set from URL */
		if ($this->form_validation->run() == FALSE &&  ( ! $url_label_name))
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('admin_template', 'admin/active', $data);
			return;
		} else {
			if ($url_label_name)
			{
				$db_label = $this->input->xss_clean($url_label_name);
			} else {
				$db_label = $this->input->post('account', TRUE);
			}
			$ini_file = $this->config->item('config_path') . "accounts/" . $db_label . ".ini";

			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$this->messages->add('Account settings file is missing.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}

			/* Parsing database ini file */
			$active_accounts = parse_ini_file($ini_file);
			if ( ! $active_accounts)
			{
				$this->messages->add('Invalid account settings file.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}

			/* Check if all needed variables are set in ini file */
			if ( ! isset($active_accounts['db_hostname']))
			{
				$this->messages->add('Hostname missing from account settings file.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_port']))
			{
				$this->messages->add('Port missing from account settings file. Default MySQL port is 3306.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_name']))
			{
				$this->messages->add('Database name missing from account settings file.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_username']))
			{
				$this->messages->add('Database username missing from account settings file.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}
			if ( ! isset($active_accounts['db_password']))
			{
				$this->messages->add('Database password missing from account settings file.', 'error');
				$this->template->load('admin_template', 'admin/active', $data);
				return;
			}

			/* Setting new account database details in session */
			$this->session->set_userdata('db_active_label', $db_label);
			$this->messages->add('Active account settings changed.', 'success');
			redirect('admin');
		}
		return;
	}
}

/* End of file active.php */
/* Location: ./system/application/controllers/admin/active.php */
