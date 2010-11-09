<?php
class Report extends Controller {
	var $acc_array;
	var $account_counter;
	function Report()
	{
		parent::Controller();
		$this->load->model('Ledger_model');
	}
	
	function index()
	{
		$this->template->set('page_title', 'Reports');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledger' => 'Ledger Statement',

		);
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/index');
	}

	function balancesheet($period = NULL)
	{
		$this->template->set('page_title', 'Balance Sheet');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledger' => 'Ledger Statement',

		);
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/balancesheet');
	}

	function profitandloss($period = NULL)
	{
		$this->template->set('page_title', 'Profit And Loss Statement');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledger' => 'Ledger Statement',

		);
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/profitandloss');
	}

	function trialbalance($period = NULL)
	{
		$this->template->set('page_title', 'Trial Balance');
		$menu = array(
			'report/balancesheet' => 'Balance Sheet',
			'report/profitandloss' => 'Profit & Loss',
			'report/trialbalance' => 'Trial Balance',
			'report/ledger' => 'Ledger Statement',

		);
		$this->load->library('accountlist');
		$a = new Accountlist(0);
		$data['a'] = $a;
		$this->template->set('nav_links', $menu);
		$this->template->load('template', 'report/trialbalance', $data);
	}
}
