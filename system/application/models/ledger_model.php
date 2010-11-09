<?php

class Ledger_model extends Model {

	function Ledger_model()
	{
		parent::Model();
	}

	function get_all_ledgers()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$ledger_q = $this->db->query('SELECT * FROM ledgers ORDER BY name ASC');
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_ledger_balance($ledger_id)
	{
		$op_bal_q = $this->db->query('SELECT * FROM ledgers WHERE id = ?', $ledger_id);
		$op_bal_r = $op_bal_q->row();
		$op_bal = $op_bal_r->op_balance;
		$op_bal_type = $op_bal_r->op_balance_dc;

		if ($op_bal_type == "C")
			$op_bal = $op_bal * -1;

		$dr_total_q = $this->db->query('SELECT SUM(amount) AS drtotal FROM voucher_items join vouchers on  vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 AND voucher_items.dc = "D"', $ledger_id);
		$dr_total_r = $dr_total_q->row();
		$dr_total = $dr_total_r->drtotal;

		$cr_total_q = $this->db->query('SELECT SUM(amount) AS crtotal FROM voucher_items join vouchers on  vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 AND voucher_items.dc = "C"', $ledger_id);
		$cr_total_r = $cr_total_q->row();
		$cr_total = $cr_total_r->crtotal;

		$total = $op_bal + $dr_total - $cr_total;
		return $total;
	}

	function get_op_balance($ledger_id)
	{
		$op_bal_q = $this->db->query('SELECT * FROM ledgers WHERE id = ?', $ledger_id);
		$op_bal = $op_bal_q->row();
		return array($op_bal->op_balance, $op_bal->op_balance_dc);
	}

	function get_dr_total($ledger_id)
	{
		$dr_total_q = $this->db->query('SELECT SUM(amount) AS drtotal FROM voucher_items join vouchers on  vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 AND voucher_items.dc = "D"', $ledger_id);
		$dr_total = $dr_total_q->row();
		return $dr_total->drtotal;
	}

	function get_cr_total($ledger_id)
	{
		$cr_total_q = $this->db->query('SELECT SUM(amount) AS crtotal FROM voucher_items join vouchers on  vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 AND voucher_items.dc = "C"', $ledger_id);
		$cr_total = $cr_total_q->row();
		return $cr_total->crtotal;
	}
}
