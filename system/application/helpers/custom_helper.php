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
 * Return Entry Type information
 *
 * @access	public
 * @param	int entry type id
 * @return	array
 */
if ( ! function_exists('entry_type_info'))
{
	function entry_type_info($entry_type_id)
	{
		$CI =& get_instance();
		$entry_type_all = $CI->config->item('account_entry_types');

		if ($entry_type_all[$entry_type_id])
		{
			return array(
				'id' => $entry_type_all[$entry_type_id],
				'label' => $entry_type_all[$entry_type_id]['label'],
				'name' => $entry_type_all[$entry_type_id]['name'],
				'numbering' => $entry_type_all[$entry_type_id]['numbering'],
				'prefix' => $entry_type_all[$entry_type_id]['prefix'],
				'suffix' => $entry_type_all[$entry_type_id]['suffix'],
				'zero_padding' => $entry_type_all[$entry_type_id]['zero_padding'],
				'bank_cash_ledger_restriction' => $entry_type_all[$entry_type_id]['bank_cash_ledger_restriction'],
			);
		} else {
			return array(
				'id' => $entry_type_all[$entry_type_id],
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
 * Return Entry Type Id from Entry Type Name
 *
 * @access	public
 * @param	string entry type name
 * @return	int entry type id
 */
if ( ! function_exists('entry_type_name_to_id'))
{
	function entry_type_name_to_id($entry_type_name)
	{
		$CI =& get_instance();
		$entry_type_all = $CI->config->item('account_entry_types');
		foreach ($entry_type_all as $id => $row)
		{
			if ($row['label'] == $entry_type_name)
			{
				return $id;
				break;
			}
		}
		return FALSE;
	}
}

/**
 * Converts Entry number to proper entry prefix formats
 *
 * @access	public
 * @param	int entry type id
 * @return	string
 */
if ( ! function_exists('full_entry_number'))
{
	function full_entry_number($entry_type_id, $entry_number)
	{
		$CI =& get_instance();
		$entry_type_all = $CI->config->item('account_entry_types');
		$return_html = "";
		if ( ! $entry_type_all[$entry_type_id])
		{
			$return_html = $entry_number;
		} else {
			$return_html = $entry_type_all[$entry_type_id]['prefix'] . str_pad($entry_number, $entry_type_all[$entry_type_id]['zero_padding'], '0', STR_PAD_LEFT) . $entry_type_all[$entry_type_id]['suffix'];
		}
		if ($return_html)
			return $return_html;
		else
			return " ";
	}
}

/**
 * Floating Point Operations
 *
 * Multiply the float by 100, convert it to integer,  
 * Perform the integer operation and then divide the result
 * by 100 and return the result
 *
 * @access	public
 * @param	float	number 1
 * @param	float	number 2
 * @param	string	operation to be performed
 * @return	float	result of the operation
 */	
if ( ! function_exists('float_ops'))
{
	function float_ops($param1 = 0, $param2 = 0, $op = '')
	{
		$result = 0;
		$param1 = $param1 * 100;
		$param2 = $param2 * 100;
		$param1 = (int)round($param1, 0);
		$param2 = (int)round($param2, 0);
		switch ($op)
		{
		case '+':
			$result = $param1 + $param2;
			break;
		case '-':
			$result = $param1 - $param2;
			break;
		case '==':
			if ($param1 == $param2)
				return TRUE;
			else
				return FALSE;
			break;
		case '!=':
			if ($param1 != $param2)
				return TRUE;
			else
				return FALSE;
			break;
		case '<':
			if ($param1 < $param2)
				return TRUE;
			else
				return FALSE;
			break;
		case '>':
			if ($param1 > $param2)
				return TRUE;
			else
				return FALSE;
			break;

		}
		$result = $result/100;
		return $result;
	}
}

/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
