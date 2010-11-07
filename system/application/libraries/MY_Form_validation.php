<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	function My_Form_validation()
	{
		parent::CI_Form_validation();
		parent::set_error_delimiters('<li>', '</li>');
	}

	/**
	 * Unique
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	function unique($str, $field)
	{
		$CI =& get_instance();
		list($table, $column) = explode('.', $field, 2);

		$CI->form_validation->set_message('unique', 'The %s that you requested is already in use');

		$query = $CI->db->query("SELECT COUNT(*) AS dupe FROM $table WHERE $column = '$str'");
		$row = $query->row();
		return ($row->dupe > 0) ? FALSE : TRUE;
	}

	function uniquewithid($str, $field)
	{
		$CI =& get_instance();
		list($table, $column, $id) = explode('.', $field, 3);

		$CI->form_validation->set_message('uniquewithid', 'The %s that you requested is already in use');

		$query = $CI->db->query("SELECT COUNT(*) AS dupe FROM $table WHERE $column = '$str' AND id != ?", array($id));
		$row = $query->row();
		return ($row->dupe > 0) ? FALSE : TRUE;
	}

	function is_dc($str)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('is_dc', '%s can only be "Dr" or "Cr"');
		return ($str == "D" || $str == "C") ? TRUE : FALSE;
	}

	function currency($str)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('currency', '%s must be a valid amount. Maximum 2 decimal places is allowed');
		return preg_match('/^[\-+]?[0-9]*\.?[0-9]{0,2}$/', $str) ? TRUE : FALSE;
	}

	function is_date($str)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('is_date', 'The %s is a invalid date');

		list($d, $m, $y) = explode('/', $str);
		return checkdate($m , $d, $y) ? TRUE : FALSE;
	}
}
?>
