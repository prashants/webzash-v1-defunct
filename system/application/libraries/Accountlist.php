<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accountlist
{
	var $id = 0;
	var $name = "";
	var $total = 0;
	var $children_groups = array();
	var $children_ledgers = array();
	var $counter = 0;

	function Accountlist()
	{
		return;
	}

	function init($id)
	{
		$CI =& get_instance();
		if ($id == 0)
		{
			$this->id = 0;
			$this->name = "None";
			$this->total = 0;

		} else {
			$group_q = $CI->db->query('SELECT * FROM groups WHERE id = ?', array($id));
			$group = $group_q->row();
			$this->id = $group->id;
			$this->name = $group->name;
			$this->total = 0;
		}
		$this->add_sub_ledgers();
		$this->add_sub_groups();
	}

	function add_sub_groups()
	{
		$CI =& get_instance();
		$child_group_q = $CI->db->query('SELECT * FROM groups WHERE parent_id = ?', array($this->id));
		$counter = 0;
		foreach ($child_group_q->result() as $row)
		{
			$this->children_groups[$counter] = new Accountlist();
			$this->children_groups[$counter]->init($row->id);
			$this->total += $this->children_groups[$counter]->total;
			$counter++;
		}
	}
	function add_sub_ledgers()
	{
		$CI =& get_instance();
		$CI->load->model('Ledger_model');
		$child_ledger_q = $CI->db->query('SELECT * FROM ledgers WHERE group_id = ?', array($this->id));
		$counter = 0;
		foreach ($child_ledger_q->result() as $row)
		{
			$this->children_ledgers[$counter]['id'] = $row->id;
			$this->children_ledgers[$counter]['name'] = $row->name;
			$this->children_ledgers[$counter]['total'] = $CI->Ledger_model->get_ledger_balance($row->id);
			$this->total += $this->children_ledgers[$counter]['total'];
			$counter++;
		}
	}

	function account_st_short($c = 0)
	{
		$this->counter = $c;
		if ($this->id != 0)
		{
			echo "<tr class=\"group-tr\">";
			echo "<td class=\"group-td\">";
			echo $this->print_space($this->counter);
			echo "&nbsp;" .  $this->name;
			echo "</td>";
			echo "<td align=\"right\">" . $this->total . $this->print_space($this->counter) . "</td>";
			echo "</tr>";
		}
		foreach ($this->children_groups as $id => $data)
		{
			$this->counter++;
			$data->account_st_short($this->counter);
			$this->counter--;
		}
		if (count($this->children_ledgers) > 0)
		{
			$this->counter++;
			foreach ($this->children_ledgers as $id => $data)
			{
				echo "<tr class=\"ledger-tr\">";
				echo "<td class=\"ledger-td\">";
				echo $this->print_space($this->counter);
				echo "&nbsp;" .  $this->name;
				echo "</td>";
				echo "<td align=\"right\">" . $this->total . $this->print_space($this->counter) . "</td>";
				echo "</tr>";
			}
			$this->counter--;
		}
	}

	function print_space($count)
	{
		$html = "";
		for ($i = 1; $i <= $count; $i++)
		{
			$html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;";
		}
		return $html;
	}
}

