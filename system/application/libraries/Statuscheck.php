<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statuscheck {
	var $error_messages = array();

	function Statuscheck()
	{
		$this->error_messages = array();
	}

	function check_permissions()
	{
		$CI =& get_instance();

		/* Writable check */
		$check_path = $CI->config->item('config_path') . "settings/";
		if (! is_writable($check_path))
		{
			$this->error_messages[] = 'Application settings directory "' . $check_path . '" is not writable. You will not able to edit any application related settings.';
		}

		$check_path = $CI->config->item('config_path') . "accounts/";
		if (! is_writable($check_path))
		{
			$this->error_messages[] = 'Account settings directory "' . $check_path . '" is not writable. You will not able to add or edit any account related settings.';
		}

		$check_path = $CI->config->item('config_path') . "users/";
		if (! is_writable($check_path))
		{
			$this->error_messages[] = 'User directory "' . $check_path . '" is not writable. You will not able to add or edit any users.';
		}

		$check_path = $CI->config->item('backup_path');
		if (! is_writable($check_path))
		{
			$this->error_messages[] = 'Backup directory "' . $check_path . '" is not writable. You will not able to save or download any backups.';
		}

		/* Security checks */
		$check_path = $CI->config->item('config_path');
		if (substr(symbolic_permissions(fileperms($check_path)), -3, 1) == "r")
		{
			$this->error_messages[] = 'Security Risk ! The application config directory "' . $check_path . '" is world readable.';
		}
		if (substr(symbolic_permissions(fileperms($check_path)), -2, 1) == "W")
		{
			$this->error_messages[] = 'Security Risk ! The application config directory "' . $check_path . '" is world writeable.';
		}

		$check_path = $CI->config->item('config_path') . "accounts/";
		if (substr(symbolic_permissions(fileperms($check_path)), -3, 1) == "r")
		{
			$this->error_messages[] = 'Security Risk ! The application accounts directory "' . $check_path . '" is world readable.';
		}
		if (substr(symbolic_permissions(fileperms($check_path)), -2, 1) == "W")
		{
			$this->error_messages[] = 'Security Risk ! The application accounts directory "' . $check_path . '" is world writeable.';
		}

		$check_path = $CI->config->item('config_path') . "users/";
		if (substr(symbolic_permissions(fileperms($check_path)), -3, 1) == "r")
		{
			$this->error_messages[] = 'Security Risk ! The users directory "' . $check_path . '" is world readable.';
		}
		if (substr(symbolic_permissions(fileperms($check_path)), -2, 1) == "W")
		{
			$this->error_messages[] = 'Security Risk ! The users directory "' . $check_path . '" is world writeable.';
		}

		$check_path = $CI->config->item('config_path') . "settings/";
		if (substr(symbolic_permissions(fileperms($check_path)), -3, 1) == "r")
		{
			$this->error_messages[] = 'Security Risk ! The application settings directory "' . $check_path . '" is world readable.';
		}
		if (substr(symbolic_permissions(fileperms($check_path)), -2, 1) == "W")
		{
			$this->error_messages[] = 'Security Risk ! The application settings directory "' . $check_path . '" is world writeable.';
		}

		$check_path = $CI->config->item('backup_path');
		if (substr(symbolic_permissions(fileperms($check_path)), -3, 1) == "r")
		{
			$this->error_messages[] = 'Security Risk ! The application backup directory "' . $check_path . '" is world readable.';
		}
		if (substr(symbolic_permissions(fileperms($check_path)), -2, 1) == "W")
		{
			$this->error_messages[] = 'Security Risk ! The application backup directory "' . $check_path . '" is world writeable.';
		}
	}
}

/* End of file Statuscheck.php */
/* Location: ./system/application/libraries/Statuscheck.php */
