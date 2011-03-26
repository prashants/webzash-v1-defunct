<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stockstree
{
	public static $counter = 0;

	function Stockstree()
	{
		return;
	}

	function init($start_id = 0)
	{
		$CI =& get_instance();
		$stock_tree = $this->add_sub_groups($start_id);
		return $stock_tree;
	}

	function add_sub_groups($id)
	{
		$CI =& get_instance();
		/* Adding current group */
		if ($id == 0)
		{
			$stock_group[$id] = array(
				'id' => $id,
				'name' => '',
				'type' => 'R',
				'sub_stock_groups' => array(),
				'sub_stock_items' => array(),
			);
		} else {
			$CI->db->from('stock_groups')->where('id', $id)->limit(1);
			$current_stock_group_q = $CI->db->get();
			if ($current_stock_group = $current_stock_group_q->row())
			{
				$stock_group[$id] = array(
					'id' => $id,
					'name' => $current_stock_group->name,
					'type' => 'G',
					'sub_stock_groups' => array(),
					'sub_stock_items' => array(),
				);
			}
		}

		/* Adding sub groups */
		$CI->db->from('stock_groups')->where('parent_id', $id)->order_by('name', 'asc');
		$stock_group_q = $CI->db->get();
		foreach ($stock_group_q->result() as $row)
		{
			$stock_group[$id]['sub_stock_groups'][$row->id] = $this->add_sub_groups($row->id);
		}

		/* Adding sub item */
		$stock_group[$id]['sub_stock_items'] = $this->add_sub_item($id);

		return $stock_group;
	}

	function add_sub_item($id)
	{
		$CI =& get_instance();
		$stock_items = array();
		$CI->db->from('stock_items')->where('stock_group_id', $id)->order_by('name', 'asc');
		$stock_item_q = $CI->db->get();
		foreach ($stock_item_q->result() as $row)
		{
			$stock_items[$row->id] = array(
				'id' => $row->id,
				'name' => $row->name,
				'type' => 'I',
				'costing_method' => $row->costing_method,
				'op_balance_quantity' => $row->op_balance_quantity,
				'op_balance_rate_per_unit' => $row->op_balance_rate_per_unit,
				'op_balance_total_value' => $row->op_balance_total_value,
			);
		}
		return $stock_items;
	}

	/*
	 * Prints the entire stock tree as required in stock account list
	 */
	function print_tree($stock_tree)
	{
		self::$counter++;
		foreach ($stock_tree as $row)
		{
			if ($row['id'] != 0)
			{
				echo "<tr class=\"tr-group\">";
				echo "<td>" . self::print_spaces(self::$counter) . $row['name'] . "</td>";
				echo "<td>Group</td>";
				echo "<td>-</td>";
				echo "<td>-</td>";
				echo "<td class=\"td-actions\">" . anchor('inventory/stockgroup/edit/' . $row['id'] , "Edit", array('title' => 'Edit Stock Group', 'class' => 'red-link'));
				echo " &nbsp;" . anchor('inventory/stockgroup/delete/' . $row['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Stock Group')), array('class' => "confirmClick", 'title' => "Delete Stock Group")) . "</td>";
				echo "</tr>";
			}
			if ($row['sub_stock_items'])
			{
				self::$counter++;
				foreach ($row['sub_stock_items'] as $row_item)
				{
					echo "<tr>";
					echo "<td>" . self::print_spaces(self::$counter) . $row_item['name'] . "</td>";
					echo "<td>Item</td>";
					echo "<td>" . convert_amount_dc($row_item['op_balance_total_value']) . "</td>";
					echo "<td></td>";
					echo "<td class=\"td-actions\">" . anchor('inventory/stockitem/edit/' . $row_item['id'] , "Edit", array('title' => 'Edit Stock Item', 'class' => 'red-link'));
					echo " &nbsp;" . anchor('inventory/stockitem/delete/' . $row_item['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Stock Item')), array('class' => "confirmClick", 'title' => "Delete Stock Item")) . "</td>";
					echo "</tr>";
				}
				self::$counter--;
			}
			if ($row['sub_stock_groups'])
			{
				foreach ($row['sub_stock_groups'] as $row_item)
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

