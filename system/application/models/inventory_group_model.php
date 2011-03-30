<?php

class Inventory_Group_model extends Model {

	function Inventory_Group_model()
	{
		parent::Model();
	}

	function get_all_groups($id = NULL)
	{
		$options = array();
		$options[0] = '(None)';
		if ($id == NULL)
			$this->db->from('inventory_groups')->where('id >', 0)->order_by('name', 'asc');
		else
			$this->db->from('inventory_groups')->where('id >', 0)->where('id !=', $id)->order_by('name', 'asc');
		$inventory_group_parent_q = $this->db->get();
		foreach ($inventory_group_parent_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_item_groups()
	{
		$options = array();
		$this->db->from('inventory_groups')->where('id >', 0)->order_by('name', 'asc');
		$inventory_item_parent_q = $this->db->get();
		foreach ($inventory_item_parent_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}
}
