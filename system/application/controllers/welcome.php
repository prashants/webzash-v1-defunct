<?php

/**
 * DATABASE CONSTANTS USED IN THE APPLICATION
 *
 * table: ledgers
 * column : type
 *    0 = General
 *    1 = Bank or Cash account
 *    2 = Purchase
 *    3 = Sale
 *    4 = Creditor
 *    5 = Debtor
 *
 * table: voucher_types
 * column : base_type
 *    1 = Normal Voucher
 *    2 = Stock Voucher
 * column : inventory_entry_type
 *    1 = Purchase
 *    2 = Sale
 *    3 = Stock Transfer
 *
 * table: voucher_items
 * column : stock_type
 *    0 = Not Applicable
 *    1 = Account Ledger
 *    2 = Entity Ledger
 *    3 = Others Ledger
 *
 * table: inventory_items
 * column : costing_method
 *
 * table: inventory_entry_items
 * column : type
 *    1 = Incoming
 *    2 = Outgoing
 *
 */
class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->load->model('Ledger_model');
		$this->load->library('accountlist');
		$this->template->set('page_title', 'Welcome to Webzash');
		$this->template->set('add_css', array("css/tufte-graph.css"));
		$this->template->set('add_javascript', array("js/raphael.js", "js/jquery.enumerable.js", "js/jquery.tufte-graph.js"));

		/* Bank and Cash Ledger accounts */
		$this->db->from('ledgers')->where('type', 1);
		$bank_q = $this->db->get();
		if ($bank_q->num_rows() > 0)
		{
			foreach ($bank_q->result() as $row)
			{
				$data['bank_cash_account'][] = array(
					'id' => $row->id,
					'name' => $row->name,
					'balance' => $this->Ledger_model->get_ledger_balance($row->id),
				);
			}
		} else {
			$data['bank_cash_account'] = array();
		}

		/* Calculating total of Assets, Liabilities, Incomes, Expenses */
		$asset = new Accountlist();
		$asset->init(1);
		$data['asset_total'] = $asset->total;

		$liability = new Accountlist();
		$liability->init(2);
		$data['liability_total'] = $liability->total;

		$data['show_asset_liability'] = TRUE;
		if ($data['asset_total'] == 0 && $data['liability_total'] == 0)
			$data['show_asset_liability'] = FALSE;

		$income = new Accountlist();
		$income->init(3);
		$data['income_total'] = $income->total;

		$expense = new Accountlist();
		$expense->init(4);
		$data['expense_total'] = $expense->total;

		$data['show_income_expense'] = TRUE;
		if ($data['income_total'] == 0 && $data['expense_total'] == 0)
			$data['show_income_expense'] = FALSE;

		/* Getting Log Messages */
		$data['logs'] = $this->logger->read_recent_messages();
		$this->template->load('template', 'welcome', $data);
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
