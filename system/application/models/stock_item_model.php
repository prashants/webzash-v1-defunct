<?php

class Stock_Item_model extends Model {

	function Stock_Item_model()
	{
		parent::Model();
	}

	function get_all_item()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('stock_items')->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_name($stock_item_id)
	{
		$this->db->from('stock_items')->where('id', $stock_item_id)->limit(1);
		$stock_item_q = $this->db->get();
		if ($stock_item = $stock_item_q->row())
			return $stock_item->name;
		else
			return "(Error)";
	}
}
