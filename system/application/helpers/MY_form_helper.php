<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('form_dropdown_dc'))
{
	function form_dropdown_dc($name, $selected = NULL, $extra = '')
	{
		$options = array("D" => "Dr", "C" => "Cr");

		// If no selected state was submitted we will attempt to set it automatically
		if ( ! ($selected == "D" || $selected == "C"))
		{
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name]))
			{
				$selected = $_POST[$name];
			}
		}

		if ($extra != '') $extra = ' '.$extra;

		$form = '<select name="'.$name.'"'.$extra.">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;
			$sel = ($key == $selected) ? ' selected="selected"' : '';
			$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
		}

		$form .= '</select>';

		return $form;
	}
}

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

/* End of file MY_form_helper.php */
/* Location: ./system/application/helpers/MY_form_helper.php */
