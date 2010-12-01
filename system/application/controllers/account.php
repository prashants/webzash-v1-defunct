<?php
class Account extends Controller {
	function index()
	{
		$this->load->model('Ledger_model');
		$this->template->set('page_title', 'Chart of accounts');
		$this->template->set('nav_links', array('group/add' => 'New Group', 'ledger/add' => 'New Ledger'));

		/* Calculating difference in Opening Balance */
		$data['total_op'] = $this->Ledger_model->get_diff_op_balance();
		$this->template->load('template', 'account/index', $data);
		return;
	}
}
