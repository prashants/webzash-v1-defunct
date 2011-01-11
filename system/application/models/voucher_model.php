<?php

class Voucher_model extends Model {

	function Voucher_model()
	{
		parent::Model();
	}

	function next_voucher_number($type_string)
	{
		$type_number = v_to_n($type_string);
		$this->db->select_max('number', 'lastno')->from('vouchers')->where('type', $type_number);
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

	function get_voucher($voucher_id, $type_string)
	{
		$type_number = v_to_n($type_string);
		$this->db->from('vouchers')->where('id', $voucher_id)->where('type', $type_number)->limit(1);
		$voucher_q = $this->db->get();
		return $voucher_q->row();
	}
}
