<?php

class Ledger_model extends Model {

	function Ledger_model()
	{
		parent::Model();
	}

	/**************************** GET LEDGERS METHODS *********************/
	function get_all_ledgers()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('ledgers')->order_by('name', 'asc');
		$ledger_q = $this->db->get();
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
		$this->db->from('ledgers')->where('type', 1)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
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
		$this->db->from('ledgers')->where('type !=', 1)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_all_ledgers_reconciliation()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('ledgers')->where('reconciliation', 1)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_all_ledgers_purchase()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('ledgers')->where('type', 2)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_all_ledgers_creditor()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('ledgers')->where('type', 4)->or_where('type', 1)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_all_ledgers_sale()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('ledgers')->where('type', 3)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_all_ledgers_debtor()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('ledgers')->where('type', 5)->or_where('type', 1)->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_name($ledger_id)
	{
		$this->db->from('ledgers')->where('id', $ledger_id)->limit(1);
		$ledger_q = $this->db->get();
		if ($ledger = $ledger_q->row())
			return $ledger->name;
		else
			return "(Error)";
	}

	function get_voucher_name($voucher_id, $voucher_type_id)
	{
		/* Selecting whether to show debit side Ledger or credit side Ledger */
		$current_voucher_type = voucher_type_info($voucher_type_id);

		/* If Stock Transfer */
		if ($current_voucher_type['stock_voucher_type'] == '3')
		{
			$html = anchor('inventory/stocktransfer/view/' . $current_voucher_type['label'] . "/" . $voucher_id, "[Stock Transfer]", array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a'));
			return $html;
		}

		$ledger_type = 'C';

		if ($current_voucher_type['bank_cash_ledger_restriction'] == '3')
			$ledger_type = 'D';

		$this->db->select('ledgers.name as name');
		$this->db->from('voucher_items')->join('ledgers', 'voucher_items.ledger_id = ledgers.id')->where('voucher_items.voucher_id', $voucher_id);
		if ($current_voucher_type['base_type'] == '2')
		{
			$this->db->where('voucher_items.stock_type', 2);
		} else {
			$this->db->where('voucher_items.dc', $ledger_type);
		}

		$ledger_q = $this->db->get();
		if ( ! $ledger = $ledger_q->row())
		{
			return "(Invalid)";
		} else {
			$ledger_multiple = ($ledger_q->num_rows() > 1) ? TRUE : FALSE;
			$html = '';
			if ($ledger_multiple)
				if ($current_voucher_type['base_type'] == '1')
					$html .= anchor('voucher/view/' . $current_voucher_type['label'] . "/" . $voucher_id, "(" . $ledger->name . ")", array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a'));
				else
					$html .= anchor('inventory/stockvoucher/view/' . $current_voucher_type['label'] . "/" . $voucher_id, "(" . $ledger->name . ")", array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a'));
			else
				if ($current_voucher_type['base_type'] == '1')
					$html .= anchor('voucher/view/' . $current_voucher_type['label'] . "/" . $voucher_id, $ledger->name, array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a'));
				else
					$html .= anchor('inventory/stockvoucher/view/' . $current_voucher_type['label'] . "/" . $voucher_id, $ledger->name, array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a'));
			return $html;
		}
		return;
	}

	function get_opp_ledger_name($voucher_id, $voucher_type_label, $ledger_type, $output_type)
	{
		$output = '';
		if ($ledger_type == 'D')
			$opp_ledger_type = 'C';
		else
			$opp_ledger_type = 'D';
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('dc', $opp_ledger_type);
		$opp_voucher_name_q = $this->db->get();
		if ($opp_voucher_name_d = $opp_voucher_name_q->row())
		{
			$opp_ledger_name = $this->get_name($opp_voucher_name_d->ledger_id);
			if ($opp_voucher_name_q->num_rows() > 1)
			{
				if ($output_type == 'html')
					$output = anchor('voucher/view/' . $voucher_type_label . '/' . $voucher_id, "(" . $opp_ledger_name . ")", array('title' => 'View ' . ' Voucher', 'class' => 'anchor-link-a'));
				else
					$output = "(" . $opp_ledger_name . ")";
			} else {
				if ($output_type == 'html')
					$output = anchor('voucher/view/' . $voucher_type_label . '/' . $voucher_id, $opp_ledger_name, array('title' => 'View ' . ' Voucher', 'class' => 'anchor-link-a'));
				else
					$output = $opp_ledger_name;
			}
		}
		return $output;
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
		$this->db->from('ledgers')->where('id', $ledger_id)->limit(1);
		$op_bal_q = $this->db->get();
		if ($op_bal = $op_bal_q->row())
			return array($op_bal->op_balance, $op_bal->op_balance_dc);
		else
			return array(0, "D");
	}

	function get_diff_op_balance()
	{
		/* Calculating difference in Opening Balance */
		$total_op = 0;
		$this->db->from('ledgers')->order_by('id', 'asc');
		$ledgers_q = $this->db->get();
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
		$this->db->select_sum('amount', 'drtotal')->from('voucher_items')->join('vouchers', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.dc', 'D');
		$dr_total_q = $this->db->get();
		if ($dr_total = $dr_total_q->row())
			return $dr_total->drtotal;
		else
			return 0;
	}

	/* Return credit total as positive value */
	function get_cr_total($ledger_id)
	{
		$this->db->select_sum('amount', 'crtotal')->from('voucher_items')->join('vouchers', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.dc', 'C');
		$cr_total_q = $this->db->get();
		if ($cr_total = $cr_total_q->row())
			return $cr_total->crtotal;
		else
			return 0;
	}

	/* Delete reconciliation entries for a Ledger account */
	function delete_reconciliation($ledger_id)
	{
		$update_data = array(
			'reconciliation_date' => NULL,
		);
		$this->db->where('ledger_id', $ledger_id)->update('voucher_items', $update_data);
		return;
	}
}
