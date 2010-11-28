<?php

class Voucher_model extends Model {

	function Voucher_model()
	{
		parent::Model();
	}

	function next_voucher_number($type_string)
	{
		$type_number = v_to_n($type_string);
		$last_no_q = $this->db->query('SELECT MAX(number) AS lastno FROM vouchers WHERE type = ?', $type_number);
		$row = $last_no_q->row();
		$last_no = (int)$row->lastno;
		$last_no++;
		return $last_no;
	}

	function get_voucher($voucher_id, $voucher_type_string)
	{
		$voucher_q = $this->db->query('SELECT * FROM vouchers WHERE id = ? AND type = ? LIMIT 1', array($voucher_id, v_to_n($voucher_type_string)));
		$row = $voucher_q->row();
		return $row;
	}
}
