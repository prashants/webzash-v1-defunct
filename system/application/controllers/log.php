<?php

class Log extends Controller {
	function index()
	{
		$this->load->helper('text');
		$this->template->set('page_title', 'Logs');
		$this->template->set('nav_links', array('log/clear' => 'Clear Log'));
		$this->template->load('template', 'log/index');
		return;
	}

	function clear()
	{
		if ($this->db->query('DELETE FROM logs'))
		{
			$this->messages->add('Log cleared.', 'success');
			redirect("log");
		} else {
			$this->messages->add('Error clearing Log.', 'error');
			redirect("log");
		}
		return;
	}

	function feed()
	{
		$this->load->helper('xml');
		$this->load->helper('text');
		$data['feed_name'] = $this->config->item('account_name');
		$data['feed_url'] = base_url();
		$data['page_description'] = 'Accounting feed for ' . $data['feed_name'];
		$data['page_language'] = 'en-en';
		$data['creator_email'] = $this->config->item('account_email');

		$this->db->order_by("id", "desc");
		$data['feed_data'] = $this->db->get('logs');

		header("Content-Type: application/rss+xml");
		$this->load->view('rss', $data);
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */
