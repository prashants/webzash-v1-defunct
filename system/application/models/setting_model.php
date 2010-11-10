<?php

class Setting_model extends Model {

	function Setting_model()
	{
		parent::Model();
	}

	function get_current()
	{
		$account_q = $this->db->query('SELECT * FROM settings WHERE id = 1');
		return $account_q->row();
	}
}
