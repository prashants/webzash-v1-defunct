<?php
class Account extends Controller {
	function index()
	{
		$this->template->set('page_title', 'Chart of accounts');
		$this->template->set('nav_links', array('group/add' => 'New Group', 'ledger/add' => 'New Ledger'));
		$this->template->load('template', 'account/index');
	}
}
