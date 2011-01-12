<?php

class User extends Controller {
	function index()
	{
		redirect('user/login');
		return;
	}

	function login()
	{
		$this->template->set('page_title', 'Login');

		/* If user already logged in then redirect to profile page */
		if ($this->session->userdata('user_name'))
			redirect('user/profile');

		/* Form fields */
		$data['user_name'] = array(
			'name' => 'user_name',
			'id' => 'user_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['user_password'] = array(
			'name' => 'user_password',
			'id' => 'user_password',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		/* Form validations */
		$this->form_validation->set_rules('user_name', 'User name', 'trim|required|min_length[1]|max_length[100]');
		$this->form_validation->set_rules('user_password', 'Password', 'trim|required|min_length[1]|max_length[100]');

		/* Re-populating form */
		if ($_POST)
		{
			$data['user_name']['value'] = $this->input->post('user_name', TRUE);
			$data['user_password']['value'] = $this->input->post('user_password', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('user_template', 'user/login', $data);
			return;
		}
		else
		{
			$data_user_name = $this->input->post('user_name', TRUE);
			$data_user_password = $this->input->post('user_password', TRUE);

			/* User validation */
			$ini_file = $this->config->item('config_path') . "users/" . $data_user_name . ".ini";

			/* Check if user ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$this->messages->add('User does not exists.', 'error');
				$this->template->load('user_template', 'user/login', $data);
				return;
			} else {
				/* Parsing user ini file */
				$active_users = parse_ini_file($ini_file);
				if ( ! $active_users)
				{
					$this->messages->add('Invalid user file.', 'error');
					$this->template->load('user_template', 'user/login', $data);
					return;
				} else {
					/* Status check */
					if (isset($active_users['status']))
					{
						if ($active_users['status'] != 1)
						{
							$this->messages->add('User disabled.', 'error');
							$this->template->load('user_template', 'user/login', $data);
							return;
						}
					} else {
						$this->messages->add('Invalid status.', 'error');
						$this->template->load('user_template', 'user/login', $data);
						return;
					}

					/* Password check */
					if (isset($active_users['password']))
					{
						$password = $active_users['password'];

						/* Role check */
						if (isset($active_users['role']))
						{
							$data_user_role = $active_users['role'];
						} else {
							$this->messages->add('Invalid role. Defaulting to "guest" role.', 'success');
							$data_user_role = 'guest';
						}

						/* Password verify */
						if ($password == $data_user_password)
						{
							$this->messages->add('Logged in as ' . $data_user_name . '.', 'success');
							$this->session->set_userdata('user_name', $data_user_name);
							$this->session->set_userdata('user_role', $data_user_role);
							redirect('');
							return;
						} else {
							$this->session->unset_userdata('user_name');
							$this->session->unset_userdata('user_role');
							$this->session->unset_userdata('active_account');
							$this->messages->add('Authentication failed.', 'error');
							$this->template->load('user_template', 'user/login', $data);
							return;
						}
					} else {
						$this->messages->add('Password missing from user file.', 'error');
						$this->template->load('user_template', 'user/login', $data);
						return;
					}
				}
			}
		}
		return;
	}

	function logout()
	{
		$this->session->unset_userdata('user_name');
		$this->session->unset_userdata('user_role');
		$this->session->unset_userdata('active_account');
		$this->session->sess_destroy();
		$this->messages->add('Logged out.', 'success');
		redirect('user/login');
	}

	function account()
	{
		$this->template->set('page_title', 'Change Account');

		/* Show manage accounts links if user has permission */
		if (check_access('administer'))
		{
			$this->template->set('nav_links', array('admin/create' => 'Create account', 'admin/manage' => 'Manage accounts'));
		}

		/* Check access */
		if ( ! ($this->session->userdata('user_name')))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		/* Currently active account */
		$data['active_account'] = $this->session->userdata('active_account');

		/* User validation */
		$ini_file = $this->config->item('config_path') . "users/" . $this->session->userdata('user_name') . ".ini";

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

		/* Check if user ini file exists */
		if ( ! get_file_info($ini_file))
		{
			$this->messages->add('User does not exists.', 'error');
			redirect('user/profile');
			return;
		} else {
			/* Parsing user ini file */
			$active_users = parse_ini_file($ini_file);
			if ( ! $active_users)
			{
				$this->messages->add('Invalid user file.', 'error');
				redirect('user/profile');
				return;
			} else {
				/* Account check */
				if (isset($active_users['accounts']))
				{
					if ($active_users['accounts'] != '*')
					{
						$valid_accounts = explode(",", $active_users['accounts']);
						$data['accounts'] = array_intersect($data['accounts'], $valid_accounts);
					}
				} else {
					$this->messages->add('Invalid accounts in user file.', 'error');
					redirect('user/profile');
					return;
				}
			}
		}

		/* Form validations */
		$this->form_validation->set_rules('account', 'Account', 'trim|required');

		/* Repopulating form */
		if ($_POST)
		{
			$data['active_account'] = $this->input->post('account', TRUE);
		}

		/* Validating form : only if label name is not set from URL */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('user_template', 'user/account', $data);
			return;
		} else {
			$data_active_account = $this->input->post('account', TRUE);

			/* Check for valid account */
			if ( ! array_key_exists($data_active_account, $data['accounts']))
			{
				$this->messages->add('Invalid account selected.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}

			$ini_file = $this->config->item('config_path') . "accounts/" . $data_active_account . ".ini";

			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$this->messages->add('Account settings file is missing.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}

			/* Parsing database ini file */
			$current_account = parse_ini_file($ini_file);
			if ( ! $current_account)
			{
				$this->messages->add('Invalid account settings file.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}

			/* Check if all needed variables are set in ini file */
			if ( ! isset($current_account['db_hostname']))
			{
				$this->messages->add('Hostname missing from account settings file.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}
			if ( ! isset($current_account['db_port']))
			{
				$this->messages->add('Port missing from account settings file.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}
			if ( ! isset($current_account['db_name']))
			{
				$this->messages->add('Database name missing from account settings file.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}
			if ( ! isset($current_account['db_username']))
			{
				$this->messages->add('Database username missing from account settings file.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}
			if ( ! isset($current_account['db_password']))
			{
				$this->messages->add('Database password missing from account settings file.', 'error');
				$this->template->load('user_template', 'user/account', $data);
				return;
			}

			/* Setting new account database details in session */
			$this->session->set_userdata('active_account', $data_active_account);
			$this->messages->add('Active account changed.', 'success');
			redirect('');
		}
		return;
	}

	function profile()
	{
		$this->template->set('page_title', 'User Profile');

		/* Check access */
		if ( ! ($this->session->userdata('user_name')))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		$this->template->load('user_template', 'user/profile');
		return;
	}
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */
