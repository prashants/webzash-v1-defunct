<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounttree
{
	public static $counter = 0;

	function Accounttree()
	{
		return;
	}

	function init($start_id = 0)
	{
		$CI =& get_instance();
		$account_tree = $this->add_sub_groups($start_id);
		return $account_tree;
	}

	function add_sub_groups($id)
	{
		$CI =& get_instance();
		/* Adding current group */
		if ($id == 0)
		{
			$group[$id] = array(
				'id' => $id,
				'name' => '',
				'type' => 'G',
				'sub_groups' => array(),
				'sub_ledgers' => array(),
			);
		} else {
			$CI->db->from('groups')->where('id', $id)->limit(1);
			$current_group_q = $CI->db->get();
			if ($current_group = $current_group_q->row())
			{
				$group[$id] = array(
					'id' => $id,
					'name' => $current_group->name,
					'type' => 'G',
					'sub_groups' => array(),
					'sub_ledgers' => array(),
				);
			}
		}

		/* Adding sub groups */
		$CI->db->from('groups')->where('parent_id', $id)->order_by('name', 'asc');
		$group_q = $CI->db->get();
		foreach ($group_q->result() as $row)
		{
			$group[$id]['sub_groups'][$row->id] = $this->add_sub_groups($row->id);
		}

		/* Adding sub ledgers */
		$group[$id]['sub_ledgers'] = $this->add_sub_ledgers($id);

		return $group;
	}

	function add_sub_ledgers($id)
	{
		$CI =& get_instance();
		$ledgers = array();
		$CI->db->from('ledgers')->where('group_id', $id)->order_by('name', 'asc');
		$ledger_q = $CI->db->get();
		if ($ledger_q)
		{
			foreach ($ledger_q->result() as $row)
			{
				$ledgers[$row->id] = array(
					'id' => $row->id,
					'name' => $row->name,
					'type' => 'L',
					'op_balance' => $row->op_balance,
					'op_balance_dc' => $row->op_balance_dc,
					'cl_balance' => $CI->Ledger_model->get_ledger_balance($row->id),
					'type' => $row->type,
					'reconciliation' => $row->reconciliation,
				);
			}
		}
		return $ledgers;
	}

	/*
	 * Prints the entire account tree as required in Account page
	 */
	function print_tree($account_tree)
	{
		self::$counter++;
		foreach ($account_tree as $row)
		{
			if ($row['id'] > 0)
			{
				echo "<tr class=\"tr-group\">";
				if ($row['id'] <= 4)
				{
					echo "<td><strong>" . self::print_spaces(self::$counter) . $row['name'] . "</strong></td>";
					echo "<td>Group</td>";
					echo "<td>-</td>";
					echo "<td>-</td>";
					echo "<td class=\"td-actions\">";
					echo "</td>";
					echo "</tr>";
				} else {
					echo "<td>" . self::print_spaces(self::$counter) . $row['name'] . "</td>";
					echo "<td>Group</td>";
					echo "<td>-</td>";
					echo "<td>-</td>";
					echo "<td class=\"td-actions\">" . anchor('group/edit/' . $row['id'] , "Edit", array('title' => 'Edit Group', 'class' => 'red-link'));
					echo " &nbsp;" . anchor('group/delete/' . $row['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Group')), array('class' => "confirmClick", 'title' => "Delete Group")) . "</td>";
					echo "</tr>";
				}
			}
			if ($row['sub_ledgers'])
			{
				self::$counter++;
				foreach ($row['sub_ledgers'] as $row_item)
				{
					echo "<tr>";
					echo "<td>" . self::print_spaces(self::$counter) . $row_item['name'] . "</td>";
					echo "<td>Ledger</td>";
					echo "<td>" . convert_opening($row_item['op_balance'], $row_item['op_balance_dc']) . "</td>";
					echo "<td>" . convert_amount_dc($row_item['cl_balance']) . "</td>";
					echo "<td class=\"td-actions\">" . anchor('ledger/edit/' . $row_item['id'] , "Edit", array('title' => 'Edit Ledger', 'class' => 'red-link'));
					echo " &nbsp;" . anchor('ledger/delete/' . $row_item['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Ledger')), array('class' => "confirmClick", 'title' => "Delete Ledger")) . "</td>";
					echo "</tr>";
				}
				self::$counter--;
			}
			if ($row['sub_groups'])
			{
				foreach ($row['sub_groups'] as $row_item)
				{
					self::print_tree($row_item);
				}
			}
		}
		self::$counter--;
	}

	/*
	 * Prints empty spaces
	 */
	function print_spaces($counter)
	{
		$return_html = '';
		for ($c = 2; $c < $counter; $c++)
			$return_html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		return $return_html;
	}
}

