<?php

class Inventory_Item_model extends Model {

	function Inventory_Item_model()
	{
		parent::Model();
	}

	function get_all_item()
	{
		$options = array();
		$options[0] = "(Please Select)";
		$this->db->from('inventory_items')->order_by('name', 'asc');
		$ledger_q = $this->db->get();
		foreach ($ledger_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		return $options;
	}

	function get_name($inventory_item_id)
	{
		$this->db->from('inventory_items')->where('id', $inventory_item_id)->limit(1);
		$inventory_item_q = $this->db->get();
		if ($inventory_item = $inventory_item_q->row())
			return $inventory_item->name;
		else
			return "(Error)";
	}

	function get_closing_quantity($inventory_item_id)
	{
		$this->db->from('inventory_items')->where('id', $inventory_item_id)->limit(1);
		$inventory_item_q = $this->db->get();
		if ( ! $inventory_item = $inventory_item_q->row())
			return 0;

		/* closing quantity */
		$opening_quantity = $inventory_item->op_balance_quantity;

		$in_quantity = 0;
		$this->db->select_sum('quantity', 'inquantity')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 1);
		$in_quantity_q = $this->db->get();
		if ($in_quantity_d = $in_quantity_q->row())
			$in_quantity = $in_quantity_d->inquantity;

		$out_quantity = 0;
		$this->db->select_sum('quantity', 'outquantity')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 2);
		$out_quantity_q = $this->db->get();
		if ($out_quantity_d = $out_quantity_q->row())
			$out_quantity = $out_quantity_d->outquantity;

		$closing_quantity = float_ops(float_ops($opening_quantity, $in_quantity, '+'), $out_quantity, '-');
		return $closing_quantity;
	}

	/* TODO */
	function get_balance($inventory_item_id)
	{
		$this->db->from('inventory_items')->where('id', $inventory_item_id)->limit(1);
		$inventory_item_q = $this->db->get();
		if ( ! $inventory_item = $inventory_item_q->row())
			return array(0, 0);

		/* closing quantity */
		$opening_quantity = $inventory_item->op_balance_quantity;
		$in_quantity = 0;
		$this->db->select_sum('quantity', 'inquantity')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 1);
		$in_quantity_q = $this->db->get();
		if ($in_quantity_d = $in_quantity_q->row())
			$in_quantity = $in_quantity_d->inquantity;

		$out_quantity = 0;
		$this->db->select_sum('quantity', 'outquantity')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 2);
		$out_quantity_q = $this->db->get();
		if ($out_quantity_d = $out_quantity_q->row())
			$out_quantity = $out_quantity_d->outquantity;

		$closing_quantity = float_ops(float_ops($opening_quantity, $in_quantity, '+'), $out_quantity, '-');

		/* closing profit or loss */
		$opening_amount = $inventory_item->op_balance_total_value;

		/* standard method */
		if ($inventory_item->costing_method == 1)
		{
			$in_amount = 0;
			$this->db->select_sum('total', 'inamount')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 1);
			$in_amount_q = $this->db->get();
			if ($in_amount_d = $in_amount_q->row())
				$in_amount = $in_amount_d->inamount;

			$out_amount = 0;
			$this->db->select_sum('total', 'outamount')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 2);
			$out_amount_q = $this->db->get();
			if ($out_amount_d = $out_amount_q->row())
				$out_amount = $out_amount_d->outamount;

			$closing_amount = float_ops(float_ops($opening_amount, $in_amount, '+'), $out_amount, '-');

			return array($closing_amount, $closing_quantity);
		}
	}

	function get_selling_price($inventory_item_id)
	{
		$this->db->from('inventory_items')->where('id', $inventory_item_id)->limit(1);
		$inventory_item_q = $this->db->get();
		if ($inventory_item = $inventory_item_q->row())
			return $inventory_item->default_sell_price;
		else
			return "";
	}

	function closing_inventory($inventory_item_id)
	{
		$this->db->from('inventory_items')->where('id', $inventory_item_id)->limit(1);
		$inventory_item_q = $this->db->get();
		if ( ! $inventory_item = $inventory_item_q->row())
			return array(0, 0, 0);

		/* FIFO costing */
		if ($inventory_item->costing_method == 1)
		{
			$opening_inventory_quantity = $inventory_item->op_balance_quantity;
			$opening_inventory_rate = $inventory_item->op_balance_rate_per_unit;
			$opening_inventory_amount = $inventory_item->op_balance_total_value;

			$in_counter = -1;
			$out_counter = -1;
			$inventory_in_tree = array();
			$inventory_out_tree = array();

			/* add opening */
			if ($opening_inventory_quantity != 0)
			{
				$in_counter++;
				$inventory_in_tree[$in_counter] = array(0 => '1', 1 => $opening_inventory_quantity, 2 => $opening_inventory_rate, 3 => $opening_inventory_amount);
			}

			/* setting up inventory in and out arrays */
			$this->db->from('inventory_entry_items')->join('entries', 'inventory_entry_items.entry_id = entries.id', 'left')->where('inventory_entry_items.inventory_item_id', $inventory_item_id);
			$inventory_q = $this->db->get();
			foreach ($inventory_q->result() as $inventory_data)
			{
				if ($inventory_data->type == 1)
				{
					$in_counter++;
					$inventory_in_tree[$in_counter] = array(0 => '1', 1 => $inventory_data->quantity, 2 => $inventory_data->rate_per_unit,  3 => $inventory_data->total);
				} else {
					$out_counter++;
					$inventory_out_tree[$out_counter] = array(0 => '1', 1 => $inventory_data->quantity, 2 => $inventory_data->rate_per_unit, 3 => $inventory_data->total);
				}
			}

			/* inventory closing calculation */
			$cl_in_counter = 0;
			$cl_out_counter = 0;
			$negative_balance = FALSE;
			while (1)
			{
				/* loop exit conditions */
				if ($cl_out_counter > $out_counter)
				{
					$negative_balance = FALSE;
					break;
				}
				if ($cl_in_counter > $in_counter)
				{
					$negative_balance = TRUE;
					break;
				}

				$current_transaction_quantity = $inventory_out_tree[$cl_out_counter][1] - $inventory_in_tree[$cl_in_counter][1];
				if ($current_transaction_quantity == 0)
				{
					/* mark entry as invalid in both in and out array */
					$inventory_in_tree[$cl_in_counter] = array(0, 0, 0, 0);
					$inventory_out_tree[$cl_out_counter] = array(0, 0, 0, 0);
					$cl_in_counter++;
					$cl_out_counter++;
				} else if ($current_transaction_quantity < 0) {
					/* mark entry as invalid in out array and update in array */
					$updated_quantity = -$current_transaction_quantity;
					$updated_rate = $inventory_in_tree[$cl_in_counter][3] / $inventory_in_tree[$cl_in_counter][1];
					$updated_value = $updated_rate * $updated_quantity;

					$inventory_in_tree[$cl_in_counter] = array(0 => 1, 1 => $updated_quantity, 2 => $updated_rate, 3 => $updated_value);
					$inventory_out_tree[$cl_out_counter] = array(0, 0, 0, 0);
					$cl_out_counter++;
				} else {
					/* mark entry as invalid in in array and update out array */
					$updated_quantity = $current_transaction_quantity;
					$updated_rate = $inventory_out_tree[$cl_out_counter][3] / $inventory_out_tree[$cl_out_counter][1];
					$updated_value = $updated_rate * $updated_quantity;

					$inventory_in_tree[$cl_in_counter] = array(0, 0, 0, 0);
					$inventory_out_tree[$cl_out_counter] = array(0 => 1, 1 => $updated_quantity, 2 => $updated_rate, 3 => $updated_value);
					$cl_in_counter++;
				}
			}

			/* final calculations */
			$final_quantity = 0; $final_rate = 0; $final_value = 0;
			if (!$negative_balance)
			{
				foreach ($inventory_in_tree as $in_row)
				{
					/* skip entries marked as invalid */
					if ($in_row[0] == 0)
						continue;
					$final_quantity += $in_row[1];
					$final_value = float_ops($final_value, $in_row[3], '+');
				}
				if ($final_quantity != 0)
				{
					$final_rate = $final_value / $final_quantity;
				} else {
					$final_rate = 0;
					$final_value = 0;
				}
				return array($final_quantity, $final_rate, $final_value);
			} else {
				foreach ($inventory_out_tree as $out_row)
				{
					/* skip entries marked as invalid */
					if ($out_row[0] == 0)
						continue;
					$final_quantity += $out_row[1];
					$final_value = float_ops($final_value, $out_row[3], '+');
				}
				if ($final_quantity != 0)
				{
					$final_rate = $final_value / $final_quantity;
				} else {
					$final_rate = 0;
					$final_value = 0;
				}
				return array(-$final_quantity, $final_rate, -$final_value);
			}
		}

		/* LIFO costing */
		if ($inventory_item->costing_method == 1)
		{
			$opening_inventory_quantity = $inventory_item->op_balance_quantity;
			$opening_inventory_rate = $inventory_item->op_balance_rate_per_unit;
			$opening_inventory_amount = $inventory_item->op_balance_total_value;

			$in_counter = -1;
			$out_counter = -1;
			$inventory_in_tree = array();
			$inventory_out_tree = array();

			/* add opening */
			if ($opening_inventory_quantity != 0)
			{
				$in_counter++;
				$out_counter++;
				$inventory_in_tree[$in_counter] = array(0 => '1', 1 => $opening_inventory_quantity, 2 => $opening_inventory_rate, 3 => $opening_inventory_amount);
			}

			/* setting up inventory in and out arrays */
			$this->db->from('inventory_entry_items')->join('entries', 'inventory_entry_items.entry_id = entries.id', 'left')->where('inventory_entry_items.inventory_item_id', $inventory_item_id);
			$inventory_q = $this->db->get();
			foreach ($inventory_q->result() as $inventory_data)
			{
				if ($inventory_data->type == 1)
				{
					$in_counter++;
					$out_counter++;
					$inventory_in_tree[$in_counter] = array(0 => '1', 1 => $inventory_data->quantity, 2 => $inventory_data->rate_per_unit,  3 => $inventory_data->total);
				} else {
					$in_counter++;
					$out_counter++;
					$inventory_out_tree[$out_counter] = array(0 => '1', 1 => $inventory_data->quantity, 2 => $inventory_data->rate_per_unit, 3 => $inventory_data->total);
				}
			}

			/* inventory closing calculation */
			$cl_in_counter = 0;
			$cl_out_counter = 0;
			$negative_balance = FALSE;
			while (1)
			{
				/* loop exit conditions */
				if ($cl_out_counter > $out_counter)
					break;

				/* set the out counter */
				if (!isset($inventory_out_tree[$cl_out_counter]))
				{
					$cl_out_counter++;
					continue;
				}

				/* set in counter to a valid value */
				$reached_in_last = FALSE;
				$cl_in_counter = $cl_out_counter - 1;
				while (1)
				{
					if ($cl_in_counter < 0)
					{
						$reached_in_last = TRUE;
						break;
					}

					if (!isset($inventory_in_tree[$cl_in_counter]))
					{
						$cl_in_counter--;
					} else {
						if ($inventory_in_tree[$cl_in_counter][0] == 0)
						{
							$cl_in_counter--;
							continue;
						} else {
							break;
						}
					}
				}
				if ($reached_in_last)
				{
					$cl_out_counter++;
					continue;
				}
////
				$current_transaction_quantity = $inventory_out_tree[$cl_out_counter][1] - $inventory_in_tree[$cl_in_counter][1];
				if ($current_transaction_quantity == 0)
				{
					/* mark entry as invalid in both in and out array */
					$inventory_in_tree[$cl_in_counter] = array(0, 0, 0, 0);
					$inventory_out_tree[$cl_out_counter] = array(0, 0, 0, 0);
					$cl_in_counter++;
					$cl_out_counter++;
				} else if ($current_transaction_quantity < 0) {
					/* mark entry as invalid in out array and update in array */
					$updated_quantity = -$current_transaction_quantity;
					$updated_rate = $inventory_in_tree[$cl_in_counter][3] / $inventory_in_tree[$cl_in_counter][1];
					$updated_value = $updated_rate * $updated_quantity;

					$inventory_in_tree[$cl_in_counter] = array(0 => 1, 1 => $updated_quantity, 2 => $updated_rate, 3 => $updated_value);
					$inventory_out_tree[$cl_out_counter] = array(0, 0, 0, 0);
					$cl_out_counter++;
				} else {
					/* mark entry as invalid in in array and update out array */
					$updated_quantity = $current_transaction_quantity;
					$updated_rate = $inventory_out_tree[$cl_out_counter][3] / $inventory_out_tree[$cl_out_counter][1];
					$updated_value = $updated_rate * $updated_quantity;

					$inventory_in_tree[$cl_in_counter] = array(0, 0, 0, 0);
					$inventory_out_tree[$cl_out_counter] = array(0 => 1, 1 => $updated_quantity, 2 => $updated_rate, 3 => $updated_value);
					$cl_in_counter++;
				}
			}

			/* final calculations */
			$final_quantity = 0; $final_rate = 0; $final_value = 0;
			if (!$negative_balance)
			{
				foreach ($inventory_in_tree as $in_row)
				{
					/* skip entries marked as invalid */
					if ($in_row[0] == 0)
						continue;
					$final_quantity += $in_row[1];
					$final_value = float_ops($final_value, $in_row[3], '+');
				}
				if ($final_quantity != 0)
				{
					$final_rate = $final_value / $final_quantity;
				} else {
					$final_rate = 0;
					$final_value = 0;
				}
				return array($final_quantity, $final_rate, $final_value);
			} else {
				foreach ($inventory_out_tree as $out_row)
				{
					/* skip entries marked as invalid */
					if ($out_row[0] == 0)
						continue;
					$final_quantity += $out_row[1];
					$final_value = float_ops($final_value, $out_row[3], '+');
				}
				if ($final_quantity != 0)
				{
					$final_rate = $final_value / $final_quantity;
				} else {
					$final_rate = 0;
					$final_value = 0;
				}
				return array(-$final_quantity, $final_rate, -$final_value);
			}
		}
	}
}
