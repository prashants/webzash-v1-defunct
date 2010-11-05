<?php
class Account extends Controller {
	protected $tree = "";
	protected $tree_counter = 0;
	function index()
	{
		$this->_get_groups(0);
		$data['tree'] = "<table border=0 cellpadding=5>";
		$data['tree'] .= "<thead><tr><th width=70%>Name</th><th>Type</th><th colspan=2>Actions</th></tr></thead>";
		$data['tree'] .= "<tbody>" . $this->tree . "</tbody>";
		$data['tree'] .= "</table>";
		$page_data['page_title'] = "Chart of accounts";
		$page_data['page_links'] = array('group/add' => 'New Group', 'ledger/add' => 'New Ledger');
		$this->load->view('template/header', $page_data);
		$this->load->view('welcome_message', $data);
		$this->load->view('template/footer');
	}

	function _get_groups($group_id)
	{
		if ($group_id > 0)
		{
			$group_q = $this->db->query('SELECT * FROM groups WHERE id = ?', array($group_id));
			$group = $group_q->row();
			$this->tree .= "<tr class=\"group-tr\">";
			$this->tree .= "<td class=\"group-td\">";
			$this->tree .= $this->_add_tree_margin($this->tree_counter);
			$this->tree .= $group->name;
			$this->tree .= "</td>";
			$this->tree .= "<td>GROUP</td>";
			$this->tree .= "<td>" . anchor('group/edit/' . $group_id , img(array('src' => asset_url() . "/images/icons/edit.png", 'border' => '0', 'alt' => 'Edit group'))) . "</td>";
			$this->tree .= "<td>" . anchor('group/delete' . $group_id, img(array('src' => asset_url() . "/images/icons/delete.png", 'border' => '0', 'alt' => 'Delete group'))) . "</td>";
			$this->tree .= "</tr>";
		}
		$child_group_q = $this->db->query('SELECT * FROM groups WHERE parent_id = ?', array($group_id));
		foreach ($child_group_q->result() as $row)
		{
			$this->tree_counter++;
			$this->_get_groups($row->id);
			$this->tree_counter--;
		}
		$ledger_q = $this->db->query('SELECT * FROM ledgers WHERE group_id = ?', array($group_id));
		if ($ledger_q->num_rows() > 0)
		{
			$this->tree_counter++;
			foreach ($ledger_q->result() as $row)
			{
				$this->tree .= "<tr class=\"ledger-tr\">";
				$this->tree .= "<td class=\"ledger-td\">";
				$this->tree .= $this->_add_tree_margin($this->tree_counter);
				$this->tree .= $row->name;
				$this->tree .= "</td>";
				$this->tree .= "<td>LEDGER</td>";
			$this->tree .= "<td>" . anchor('ledger/edit/' . $row->id, img(array('src' => asset_url() . "/images/icons/edit.png", 'border' => '0', 'alt' => 'Edit ledger'))) . "</td>";
			$this->tree .= "<td>" . anchor('ledger/delete/' . $row->id, img(array('src' => asset_url() . "/images/icons/delete.png", 'border' => '0', 'alt' => 'Delete ledger'))) . "</td>";
				$this->tree .= "</tr>";
			}
			$this->tree_counter--;
		}
	}

	function _add_tree_margin($counter)
	{
		$html = "";
		for ($i = 2; $i <= $counter; $i++)
		{
			$html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		return $html;
	}
}
