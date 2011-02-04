<?php

class Stock_Group_model extends Model {

	function Stock_Group_model()
	{
		parent::Model();
	}

	function get_all_stock_groups($id = NULL)
	{
		$options = array();
		$options[0] = '(None)';
		if ($id == NULL)
			$this->db->from('stock_groups')->where('id >', 0)->order_by('name', 'asc');
		else
			$this->db->from('stock_groups')->where('id >', 0)->where('id !=', $id)->order_by('name', 'asc');
		$stock_group_parent_q = $this->db->get();
		foreach ($stock_group_parent_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}
}
