<?php
class Report extends Controller {
	var $acc_array;
	var $account_counter;
	function Report()
	{
		parent::Controller();
		$this->load->model('Ledger_model');
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Reports');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledgerst' => 'Ledger Statement',

		);
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/index');
		return;
	}

	function balancesheet($period = NULL)
	{
		$this->template->set('page_title', 'Balance Sheet');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledgerst' => 'Ledger Statement',

		);
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/balancesheet');
		return;
	}

	function profitandloss($period = NULL)
	{
		$this->template->set('page_title', 'Profit And Loss Statement');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledgerst' => 'Ledger Statement',

		);
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/profitandloss');
		return;
	}

	function trialbalance($period = NULL)
	{
		$this->template->set('page_title', 'Trial Balance');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledgerst' => 'Ledger Statement',

		);
		$this->load->library('accountlist');
		$a = new Accountlist(0);
		$data['a'] = $a;
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/trialbalance', $data);
		return;
	}

	function ledgerst($ledger_id = 0)
	{
		/* Pagination setup */
		$this->load->library('pagination');

		$this->template->set('page_title', 'Ledger Statement');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledgerst' => 'Ledger Statement',

		);
		if ($_POST)
		{
			$ledger_id = $this->input->post('ledger_id', TRUE);
			redirect('report/ledgerst/' . $ledger_id);
		}
		$data['ledger_id'] = $ledger_id;
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/ledgerst', $data);
		return;
	}
}
