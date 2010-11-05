<?php
class Account extends Controller {
	protected $tree = "";
	function index()
	{
		$this->_get_groups(0);
		$data['tree'] = $this->tree;
		$page_data['page_title'] = "Chart of accounts";
		$page_data['page_links'] = array('group/new' => 'New Group', 'ledger/new' => 'New Ledger');
		$this->load->view('template/header', $page_data);
		$this->load->view('welcome_message', $data);
		$this->load->view('template/footer');
	}

	function _get_groups($group_id)
	{
		$this->tree .= "<ul class=\"group-head";
		$this->tree .= (in_array($group_id, array(0, 1, 2, 3, 4))) ? " group-first" : "";
		$this->tree .= "\">";
		if ($group_id > 0)
		{
			$group_q = $this->db->query('SELECT * FROM groups WHERE id = ?', array($group_id));
			$group = $group_q->row();
			$this->tree .= "<li class=\"group-item\">" . $group->name;
		}
		$child_group_q = $this->db->query('SELECT * FROM groups WHERE parent_id = ?', array($group_id));
		foreach ($child_group_q->result() as $row)
		{
			$this->_get_groups($row->id);
		}
		$ledger_q = $this->db->query('SELECT * FROM ledgers WHERE group_id = ?', array($group_id));
		if ($ledger_q->num_rows() > 0)
		{
			$this->tree .= "<ul class=\"ledger-head\">";
			foreach ($ledger_q->result() as $row)
			{
				$this->tree .= "<li class=\"ledger-item\">";
				$this->tree .= $row->name;
				$this->tree .= "</li>";
			}
			$this->tree .= "</ul>";
		}
		$this->tree .= "</li>";
		$this->tree .= "</ul>";
	}
}
