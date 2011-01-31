<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Converts D/C to Dr / Cr
 *
 * Covnerts the D/C received from database to corresponding
 * Dr/Cr value for display.
 *
 * @access	public
 * @param	string	'd' or 'c' from database table
 * @return	string
 */	
if ( ! function_exists('convert_dc'))
{
	function convert_dc($label)
	{
		if ($label == "D")
			return "Dr";
		else if ($label == "C")
			return "Cr";
		else
			return "Error";
	}
}

/**
 * Converts amount to Dr or Cr Value
 *
 * Covnerts the amount to 0 or Dr or Cr value for display
 *
 * @access	public
 * @param	float	amount for display
 * @return	string
 */	
if ( ! function_exists('convert_amount_dc'))
{
	function convert_amount_dc($amount)
	{
		if ($amount == "D")
			return "0";
		else if ($amount < 0)
			return "Cr " . convert_cur(-$amount);
		else
			return "Dr " . convert_cur($amount);
	}
}

/**
 * Converts Opening balance amount to Dr or Cr Value
 *
 * Covnerts the Opening balance amount to 0 or Dr or Cr value for display
 *
 * @access	public
 * @param	amount
 * @param	debit or credit
 * @return	string
 */
if ( ! function_exists('convert_opening'))
{
	function convert_opening($amount, $dc)
	{
		if ($amount == 0)
			return "0";
		else if ($dc == 'D')
			return "Dr " . convert_cur($amount);
		else
			return "Cr " . convert_cur($amount);
	}
}

if ( ! function_exists('convert_cur'))
{
	function convert_cur($amount)
	{
		return number_format($amount, 2, '.', '');
	}
}

/**
 * Return the value of variable is set
 *
 * Return the value of varaible is set else return empty string
 *
 * @access	public
 * @param	a varaible
 * @return	string value
 */	
if ( ! function_exists('print_value'))
{
	function print_value($value = NULL, $default = "")
	{
		if (isset($value))
			return $value;
		else
			return $default;
	}
}

/**
 * Return Voucher Type information
 *
 * @access	public
 * @param	int voucher type id
 * @return	array
 */
if ( ! function_exists('voucher_type_info'))
{
	function voucher_type_info($voucher_type_id)
	{
		$CI =& get_instance();
		$voucher_type_all = $CI->config->item('account_voucher_types');

		if ($voucher_type_all[$voucher_type_id])
		{
			return array(
				'id' => $voucher_type_all[$voucher_type_id],
				'label' => $voucher_type_all[$voucher_type_id]['label'],
				'name' => $voucher_type_all[$voucher_type_id]['name'],
				'numbering' => $voucher_type_all[$voucher_type_id]['numbering'],
				'prefix' => $voucher_type_all[$voucher_type_id]['prefix'],
				'suffix' => $voucher_type_all[$voucher_type_id]['suffix'],
				'zero_padding' => $voucher_type_all[$voucher_type_id]['zero_padding'],
				'bank_cash_ledger_restriction' => $voucher_type_all[$voucher_type_id]['bank_cash_ledger_restriction'],
			);
		} else {
			return array(
				'id' => $voucher_type_all[$voucher_type_id],
				'label' => '',
				'name' => '(Unkonwn)',
				'numbering' => 1,
				'prefix' => '',
				'suffix' => '',
				'zero_padding' => 0,
				'bank_cash_ledger_restriction' => 5,
			);
		}
	}
}

/**
 * Return Voucher Type Id from Voucher Type Name
 *
 * @access	public
 * @param	string voucher type name
 * @return	int voucher type id
 */
if ( ! function_exists('voucher_type_name_to_id'))
{
	function voucher_type_name_to_id($voucher_type_name)
	{
		$CI =& get_instance();
		$voucher_type_all = $CI->config->item('account_voucher_types');
		foreach ($voucher_type_all as $id => $row)
		{
			if ($row['label'] == $voucher_type_name)
			{
				return $id;
				break;
			}
		}
		return FALSE;
	}
}

/**
 * Converts Voucher number to proper voucher prefix formats
 *
 * @access	public
 * @param	int voucher type id
 * @return	string
 */
if ( ! function_exists('full_voucher_number'))
{
	function full_voucher_number($voucher_type_id, $voucher_number)
	{
		$CI =& get_instance();
		$voucher_type_all = $CI->config->item('account_voucher_types');

		if ( ! $voucher_type_all[$voucher_type_id])
		{
			return $voucher_number;
		} else {
			return $voucher_type_all[$voucher_type_id]['prefix'] . $voucher_number . $voucher_type_all[$voucher_type_id]['suffix'];
		}
	}
}

/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
