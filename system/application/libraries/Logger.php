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
	 * 1 - success
	 * 2 - info
	 * 3 - debug
	 */
	function write_message($level = "debug", $title = "", $desc = "")
	{
		$CI =& get_instance();

		/* Check if logging is enabled. Skip if it is not enabled */
		if ($CI->config->item('log') != "1")
			return;

		$data['date'] = date("Y-m-d H:i:s");
		$data['level'] = 3;
		switch ($level)
		{
			case "error": $data['level'] = 0; break;
			case "success": $data['level'] = 1; break;
			case "info": $data['level'] = 2; break;
			case "debug": $data['level'] = 3; break;
			default: $data['level'] = 0; break;
		}
		$data['host_ip'] = $CI->input->ip_address();
		$data['user'] = $CI->session->userdata('user_name');
		$data['url'] = uri_string();
		$data['user_agent'] = $CI->input->user_agent();
		$data['message_title'] = $title;
		$data['message_desc'] = $desc;
		$CI->db->insert('logs', $data);
		return;
	}

	function read_recent_messages()
	{
		$CI =& get_instance();
		$CI->db->from('logs')->order_by('id', 'desc')->limit(20);
		$logs_q = $CI->db->get();
		if ($logs_q->num_rows() > 0)
		{
			return $logs_q;
		} else {
			return FALSE;
		}
	}
}

