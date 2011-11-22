<?php

class Tag_model extends Model {

	function Tag_model()
	{
		parent::Model();
	}

	function get_all_tags($allow_none = TRUE)
	{
		$options = array();
		if ($allow_none)
			$options[0] = "(None)";
		$this->db->from('tags')->order_by('title', 'asc');
		$tag_q = $this->db->get();
		foreach ($tag_q->result() as $row)
		{
			$options[$row->id] = $row->title;
		}
		return $options;
	}

	function show_entry_tag($tag_id)
	{
		if ($tag_id < 1)
			return "";
		$this->db->from('tags')->where('id', $tag_id)->limit(1);
		$tag_q = $this->db->get();
		if ($tag = $tag_q->row())
			return "<span class=\"tags\" style=\"color:#" . $tag->color . "; background-color:#" . $tag->background . "\">" . $tag->title . "</span>";
		else
			return "";
	}

	function show_entry_tag_link($tag_id)
	{
		if ($tag_id < 1)
			return "";
		$this->db->from('tags')->where('id', $tag_id)->limit(1);
		$tag_q = $this->db->get();
		if ($tag = $tag_q->row())
			return "<span class=\"tags\" style=\"color:#" . $tag->color . "; background-color:#" . $tag->background . "\">" . anchor("entry/show/tag/" . $tag->id , $tag->title, array('style' => 'text-decoration:none;color:#' . $tag->color . ';')) . "</span>";
		else
			return "";
	}
	
	function tag_name($tag_id)
	{
		if ($tag_id < 1)
			return "";
		$this->db->from('tags')->where('id', $tag_id)->limit(1);
		$tag_q = $this->db->get();
		if ($tag = $tag_q->row())
			return $tag->title;
		else
			return "";
	}
}
