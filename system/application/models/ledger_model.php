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

	function get_all_ledgers_bankcash()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$ledger_q = $this->db->query('SELECT * FROM ledgers WHERE type = ? ORDER BY name ASC', array('B'));
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_all_ledgers_nobankcash()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$ledger_q = $this->db->query('SELECT * FROM ledgers WHERE type != ? ORDER BY name ASC', array('B'));
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_name($ledger_id)
	{
		$ledger_q = $this->db->query('SELECT name FROM ledgers WHERE id = ? LIMIT 1', array($ledger_id));
		$ledger = $ledger_q->row();
		return $ledger->name;
	}

	function get_ledger_balance($ledger_id)
	{
		list ($op_bal, $op_bal_type) = $this->get_op_balance($ledger_id);

		if ($op_bal_type == "C")
			$op_bal = $op_bal * -1;

		$dr_total = $this->get_dr_total($ledger_id);
		$cr_total = $this->get_cr_total($ledger_id);

		$total = $op_bal + $dr_total - $cr_total;
		return $total;
	}

	function get_op_balance($ledger_id)
	{
		if ($op_bal_q = $this->db->query('SELECT * FROM ledgers WHERE id = ? LIMIT 1', $ledger_id))
		{
			$op_bal = $op_bal_q->row();
			return array($op_bal->op_balance, $op_bal->op_balance_dc);
		} else {
			return array(0, "D");
		}
	}

	function get_diff_op_balance()
	{
		/* Calculating difference in Opening Balance */
		$total_op = 0;
		$ledgers_q = $this->db->query("SELECT * FROM ledgers ORDER BY id");
		foreach ($ledgers_q->result() as $row)
		{
			list ($opbalance, $optype) = $this->get_op_balance($row->id);
			if ($optype == "D")
			{
				$total_op += $opbalance;
			} else {
				$total_op -= $opbalance;
			}
		}
		return $total_op;
	}

	/* Return debit total as positive value */
	function get_dr_total($ledger_id)
	{
		$dr_total_q = $this->db->query('SELECT SUM(amount) AS drtotal FROM voucher_items join vouchers on  vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND voucher_items.dc = "D"', $ledger_id);
		$dr_total = $dr_total_q->row();
		return $dr_total->drtotal;
	}

	/* Return credit total as positive value */
	function get_cr_total($ledger_id)
	{
		$cr_total_q = $this->db->query('SELECT SUM(amount) AS crtotal FROM voucher_items join vouchers on  vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND voucher_items.dc = "C"', $ledger_id);
		$cr_total = $cr_total_q->row();
		return $cr_total->crtotal;
	}
}
