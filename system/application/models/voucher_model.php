<?php

class Voucher_model extends Model {

	function Voucher_model()
	{
		parent::Model();
	}

	function next_voucher_number($voucher_type_id)
	{
		$this->db->select_max('number', 'lastno')->from('vouchers')->where('voucher_type', $voucher_type_id);
		$last_no_q = $this->db->get();
		if ($row = $last_no_q->row())
		{
			$last_no = (int)$row->lastno;
			$last_no++;
			return $last_no;
		} else {
			return 1;
		}
	}

	function get_voucher($voucher_id, $voucher_type_id)
	{
		$this->db->from('vouchers')->where('id', $voucher_id)->where('voucher_type', $voucher_type_id)->limit(1);
		$voucher_q = $this->db->get();
		return $voucher_q->row();
	}
}
