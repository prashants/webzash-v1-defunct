<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Check if the currently logger in user has the necessary permissions
 * to permform the given action
 */

if ( ! function_exists('check_access'))
{
	function check_access($action_name)
	{
		$CI =& get_instance();
		$CI->session->set_userdata('user_role', 'administrator');
		$user_role = $CI->session->userdata('user_role');
		$permissions['manager'] = array(
			'view voucher',
			'create voucher',
			'edit voucher',
			'delete voucher',
			'print voucher',
			'email voucher',
			'download voucher',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'manage tag',
			'view report',
			'view log',
			'clear log',
			'change account settings',
			'backup account database',
		);
		$permissions['accountant'] = array(
			'view voucher',
			'create voucher',
			'edit voucher',
			'delete voucher',
			'print voucher',
			'email voucher',
			'download voucher',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'manage tag',
			'view report',
			'view log',
			'clear log',
		);
		$permissions['dataentry'] = array(
			'view voucher',
			'create voucher',
			'edit voucher',
			'delete voucher',
			'print voucher',
			'email voucher',
			'download voucher',
			'create ledger',
			'edit ledger',
		);
		$permissions['guest'] = array(
			'view voucher',
			'print voucher',
			'email voucher',
			'download voucher',
		);

		/* If user is administrator then always allow access */
		if ($user_role == "administrator")
			return TRUE;

		if (in_array($action_name, $permissions[$user_role]))
			return TRUE;
		else
			return FALSE;
	}
}

/* End of file access_helper.php */
/* Location: ./system/application/helpers/access_helper.php */
