<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logger
{
	function Logger()
	{
		return;
	}

	/*
	 * Write message to database log
	 * Levels defined are :
	 * 0 - error
	 * 1 - info
	 * 2 - debug
	 */
	function write_message($level = "debug", $title = "", $desc = "")
	{
		$CI =& get_instance();
		$data['date'] = date("Y-m-d H:i:s");
		switch ($level)
		{
		case "error": $data['level'] = 0; break;
		case "info": $data['level'] = 1; break;
		case "debug": $data['level'] = 2; break;
		default: $data['level'] = 0; break;
		}
		$data['host_ip'] = $CI->input->ip_address();
		$data['url'] = current_url();
		$data['user_agent'] = $CI->input->user_agent();
		$data['message_title'] = $title;
		$data['message_desc'] = $desc;
		$CI->db->insert('log', $data);
	}
}

