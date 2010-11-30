<?php
class Account extends Controller {
	function index()
	{
		$this->load->model('Ledger_model');
		$this->template->set('page_title', 'Chart of accounts');
		$this->template->set('nav_links', array('group/add' => 'New Group', 'ledger/add' => 'New Ledger'));

		/* Calculating difference in Opening Balance */
		$total_op = 0;
		$ledgers_q = $this->db->query("SELECT * FROM ledgers ORDER BY id");
		foreach ($ledgers_q->result() as $row)
		{
			list ($opbalance, $optype) = $this->Ledger_model->get_op_balance($row->id);
			if ($optype == "D")
			{
				$total_op += $opbalance;
			} else {
				$total_op -= $opbalance;
			}
		}
		$data['total_op'] = $total_op;
		$this->template->load('template', 'account/index', $data);
		return;
	}
}
