<?php

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

		/* Draft voucher count */
		$draft_q = $this->db->get_where('vouchers', array('draft' => 1));
		$data['draft_count'] = $draft_q->num_rows();

		/* Bank and Cash Ledger accounts */
		$bank_q = $this->db->get_where('ledgers', array('type' => 'B'));
		if ($bank_q->num_rows() > 1)
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
		$data['liability_total'] = -$liability->total;

		$income = new Accountlist();
		$income->init(3);
		$data['income_total'] = -$income->total;

		$expense = new Accountlist();
		$expense->init(4);
		$data['expense_total'] = $expense->total; var_dump($data);
		
		$this->template->load('template', 'welcome_message', $data);
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
