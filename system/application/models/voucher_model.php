<?php

class Voucher_model extends Model {

	function Voucher_model()
	{
		parent::Model();
	}

	function next_voucher_number()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$last_no_q = $this->db->query('SELECT MAX(number) AS lastno FROM vouchers');
		$row = $last_no_q->row();
		$last_no = (int)$row->lastno;
		$last_no++;
		return $last_no;
	}
}
