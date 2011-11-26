<?php

class Entry_model extends Model {

	function Entry_model()
	{
		parent::Model();
	}

	function next_entry_number($entry_type_id)
	{
		$this->db->select_max('number', 'lastno')->from('entries')->where('entry_type', $entry_type_id);
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

	function get_entry($entry_id, $entry_type_id)
	{
		$this->db->from('entries')->where('id', $entry_id)->where('entry_type', $entry_type_id)->limit(1);
		$entry_q = $this->db->get();
		return $entry_q->row();
	}
}
