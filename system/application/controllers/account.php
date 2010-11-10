<?php
class Account extends Controller {
	protected $account_list = "";
	protected $account_counter = 0;
	function index()
	{
		$this->template->set('page_title', 'Chart of accounts');
		$this->template->set('nav_links', array('group/add' => 'New Group', 'ledger/add' => 'New Ledger'));

		$this->_get_groups(0);
		$data['account_list'] = "<table border=0 cellpadding=5 class=\"generaltable\">";
		$data['account_list'] .= "<thead><tr><th>Name</th><th>Type</th><th></th><th>O/P Balance</th><th colspan=2>Actions</th></tr></thead>";
		$data['account_list'] .= "<tbody>" . $this->account_list . "</tbody>";
		$data['account_list'] .= "</table>";

		$this->template->load('template', 'account/index', $data);
	}

	function _get_groups($group_id)
	{
		if ($group_id > 0)
		{
			$group_q = $this->db->query('SELECT * FROM groups WHERE id = ?', array($group_id));
			$group = $group_q->row();
			$this->account_list .= "<tr class=\"group-tr\">";
			$this->account_list .= "<td class=\"group-td\">";
			$this->account_list .= $this->_add_tree_margin($this->account_counter);
			$this->account_list .= "&nbsp;" .  $group->name;
			$this->account_list .= "</td>";
			$this->account_list .= "<td>Group A/C</td>";
			$this->account_list .= "<td>-</td>";
			$this->account_list .= "<td align=\"right\">-</td>";
			$this->account_list .= "<td>" . anchor('group/edit/' . $group_id , img(array('src' => asset_url() . "images/icons/edit.png", 'border' => '0', 'alt' => 'Edit group'))) . "</td>";
			$this->account_list .= "<td>" . anchor('group/delete/' . $group_id, img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete group')), array('class' => "confirmClick", 'title' => "Delete this group")) . "</td>";
			$this->account_list .= "</tr>";
		}
		$child_group_q = $this->db->query('SELECT * FROM groups WHERE parent_id = ?', array($group_id));
		foreach ($child_group_q->result() as $row)
		{
			$this->account_counter++;
			$this->_get_groups($row->id);
			$this->account_counter--;
		}
		$ledger_q = $this->db->query('SELECT * FROM ledgers WHERE group_id = ?', array($group_id));
		if ($ledger_q->num_rows() > 0)
		{
			$this->account_counter++;
			foreach ($ledger_q->result() as $row)
			{
				$this->account_list .= "<tr class=\"ledger-tr\">";
				$this->account_list .= "<td class=\"ledger-td\">";
				$this->account_list .= $this->_add_tree_margin($this->account_counter);
				$this->account_list .= $row->name;
				$this->account_list .= "</td>";
				$this->account_list .= "<td>Ledger A/C</td>";
				$this->account_list .= "<td>".  convert_dc($row->op_balance_dc) . "</td>";
				$this->account_list .= "<td align=\"right\">" . $row->op_balance . "</td>";
				$this->account_list .= "<td>" . anchor('ledger/edit/' . $row->id, img(array('src' => asset_url() . "images/icons/edit.png", 'border' => '0', 'alt' => 'Edit ledger'))) . "</td>";
				$this->account_list .= "<td>" . anchor('ledger/delete/' . $row->id, img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete ledger')), array('class' => "confirmClick", 'title' => "Delete this ledger")) . "</td>";
				$this->account_list .= "</tr>";
			}
			$this->account_counter--;
		}
	}

	function _add_tree_margin($counter)
	{
		$html = "";
		for ($i = 2; $i <= $counter; $i++)
		{
			$html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		return $html;
	}
}
