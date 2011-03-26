<?php

class Inventory_Unit_model extends Model {

	function Inventory_Unit_model()
	{
		parent::Model();
	}

	function get_all_units()
	{
		$options = array();
		$this->db->from('stock_units')->where('id >', 0)->order_by('symbol', 'asc');
		$stock_unit_q = $this->db->get();
		foreach ($stock_unit_q->result() as $row)
		{
			$options[$row->id] = $row->symbol;
		}
		return $options;
	}
}
