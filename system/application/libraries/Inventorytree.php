<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventorytree
{
	public static $counter = 0;

	function Inventorytree()
	{
		return;
	}

	function init($start_id = 0)
	{
		$CI =& get_instance();
		$inventory_tree = $this->add_sub_groups($start_id);
		return $inventory_tree;
	}

	function add_sub_groups($id)
	{
		$CI =& get_instance();
		/* Adding current group */
		if ($id == 0)
		{
			$inventory_group[$id] = array(
				'id' => $id,
				'name' => '',
				'type' => 'R',
				'sub_inventory_groups' => array(),
				'sub_inventory_items' => array(),
			);
		} else {
			$CI->db->from('inventory_groups')->where('id', $id)->limit(1);
			$current_inventory_group_q = $CI->db->get();
			if ($current_inventory_group = $current_inventory_group_q->row())
			{
				$inventory_group[$id] = array(
					'id' => $id,
					'name' => $current_inventory_group->name,
					'type' => 'G',
					'sub_inventory_groups' => array(),
					'sub_inventory_items' => array(),
				);
			}
		}

		/* Adding sub groups */
		$CI->db->from('inventory_groups')->where('parent_id', $id)->order_by('name', 'asc');
		$inventory_group_q = $CI->db->get();
		foreach ($inventory_group_q->result() as $row)
		{
			$inventory_group[$id]['sub_inventory_groups'][$row->id] = $this->add_sub_groups($row->id);
		}

		/* Adding sub item */
		$inventory_group[$id]['sub_inventory_items'] = $this->add_sub_item($id);

		return $inventory_group;
	}

	function add_sub_item($id)
	{
		$CI =& get_instance();
		$inventory_items = array();
		$CI->db->from('inventory_items')->where('inventory_group_id', $id)->order_by('name', 'asc');
		$inventory_item_q = $CI->db->get();
		foreach ($inventory_item_q->result() as $row)
		{
			$inventory_items[$row->id] = array(
				'id' => $row->id,
				'name' => $row->name,
				'type' => 'I',
				'costing_method' => $row->costing_method,
				'op_balance_quantity' => $row->op_balance_quantity,
				'op_balance_rate_per_unit' => $row->op_balance_rate_per_unit,
				'op_balance_total_value' => $row->op_balance_total_value,
			);
		}
		return $inventory_items;
	}

	/*
	 * Prints the entire inventory tree as required in inventory account list
	 */
	function print_tree($inventory_tree)
	{
		self::$counter++;
		foreach ($inventory_tree as $row)
		{
			if ($row['id'] != 0)
			{
				echo "<tr class=\"tr-group\">";
				echo "<td>" . self::print_spaces(self::$counter) . $row['name'] . "</td>";
				echo "<td>Group</td>";
				echo "<td>-</td>";
				echo "<td>-</td>";
				echo "<td class=\"td-actions\">" . anchor('inventory/group/edit/' . $row['id'] , "Edit", array('title' => 'Edit Inventory Group', 'class' => 'red-link'));
				echo " &nbsp;" . anchor('inventory/group/delete/' . $row['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Inventory Group')), array('class' => "confirmClick", 'title' => "Delete Inventory Group")) . "</td>";
				echo "</tr>";
			}
			if ($row['sub_inventory_items'])
			{
				self::$counter++;
				foreach ($row['sub_inventory_items'] as $row_item)
				{
					echo "<tr>";
					echo "<td>" . self::print_spaces(self::$counter) . $row_item['name'] . "</td>";
					echo "<td>Item</td>";
					echo "<td>" . convert_amount_dc($row_item['op_balance_total_value']) . "</td>";
					echo "<td></td>";
					echo "<td class=\"td-actions\">" . anchor('inventory/item/edit/' . $row_item['id'] , "Edit", array('title' => 'Edit Inventory Item', 'class' => 'red-link'));
					echo " &nbsp;" . anchor('inventory/item/delete/' . $row_item['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Inventory Item')), array('class' => "confirmClick", 'title' => "Delete Inventory Item")) . "</td>";
					echo "</tr>";
				}
				self::$counter--;
			}
			if ($row['sub_inventory_groups'])
			{
				foreach ($row['sub_inventory_groups'] as $row_item)
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

