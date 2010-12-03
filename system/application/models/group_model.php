<?php

class Group_model extends Model {

	function Group_model()
	{
		parent::Model();
	}

	function get_all_groups($id = NULL)
	{
		$options = array();
		if ($id == NULL)
			$group_parent_q = $this->db->query('SELECT * FROM groups WHERE id > 0 ORDER BY name');
		else
			$group_parent_q = $this->db->query('SELECT * FROM groups WHERE id > 0 AND id != ? ORDER BY name', array($id));

		foreach ($group_parent_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_ledger_groups()
	{
		$options = array();
		$group_parent_q = $this->db->query('SELECT * FROM groups WHERE id > 4 ORDER BY name');
		foreach ($group_parent_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}
}
