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

		$closing_quantity = $opening_quantity + $in_quantity - $out_quantity;
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

		$closing_quantity = $opening_quantity + $in_quantity - $out_quantity;

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

			$closing_amount = $opening_amount + $in_amount - $out_amount;

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

			$counter = 0;
			$counter_remove = 0;
			$inventory_tree[$counter] = array($opening_inventory_quantity, $opening_inventory_rate, $opening_inventory_amount);
			//$this->db->select('inventory_entry_items.type as inventory_entry_items_type');
			$this->db->from('inventory_entry_items')->join('entries', 'inventory_entry_items.entry_id = entries.id', 'left')->where('inventory_entry_items.inventory_item_id', $inventory_item_id);
			$inventory_q = $this->db->get();
			$negative_balance = 0;
			foreach ($inventory_q->result() as $inventory_data)
			{
				if ($inventory_data->type == 1)
				{
					$inventory_tree[$counter] = array($inventory_data->quantity, $inventory_data->rate_per_unit,  $inventory_data->total);
					$counter++;
				} else {
					$sale_count = $inventory_data->quantity + $negative_balance;
					$temp_counter = 0;
					while ($temp_counter <= $counter)
					{
						if ($inventory_tree[$temp_counter] > $sale_count)
						{
							$inventory_tree[$temp_counter][0] = $inventory_tree[$temp_counter][0] - $sale_count;
							$negative_balance = 0;
							break;
						} else if ($inventory_tree[$temp_counter] == $sale_count) {
							array_shift($inventory_tree);
							$counter--;
							$negative_balance = 0;
							break;
						} else {
							array_shift($inventory_tree);
							$counter--;
							$sale_count = $sale_count - $inventory_tree[$temp_counter][0];
							$temp_counter++;
						}
					}
					if ($temp_counter > $counter)
						$negative_balance = $temp_counter - $counter;
				}
			}

			/* closing calculation */
			if ($negative_balance > 0)
			{
				$final_quantity = -$negative_balance;
				$final_amount = 0;
			} else {
				$final_quantity = 0;
				$final_amount = 0;
				foreach ($inventory_tree as $row)
				{
					$final_quantity += $row[0];
					$final_amount += $row[2];
				}
			}
			if ($final_quantity != 0)
				$final_rate = $final_amount / $final_quantity;
			else
				$final_rate = 0;
			return array($final_quantity, $final_rate, $final_amount);
		}

		/* average costing */
		if ($inventory_item->costing_method == 3)
		{
			/* opening */
			$opening_inventory_quantity = $inventory_item->op_balance_quantity;
			$opening_inventory_rate = $inventory_item->op_balance_rate_per_unit;
			$opening_inventory_amount = $inventory_item->op_balance_total_value;

			/* purchase quantity */
			$this->db->select_sum('quantity', 'inquantity')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 1);
			$purchase_quantity_q = $this->db->get();
			if ($purchase_quantity_d = $purchase_quantity_q->row())
				$purchase_quantity = $purchase_quantity_d->inquantity;
			else
				$purchase_quantity = 0;

			/* total in quantity */
			$total_in_quantity = $opening_inventory_quantity + $purchase_quantity;

			/* sale quantity */
			$this->db->select_sum('quantity', 'outquantity')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 2);
			$sale_quantity_q = $this->db->get();
			if ($sale_quantity_d = $sale_quantity_q->row())
				$sale_quantity = $sale_quantity_d->outquantity;
			else
				$sale_quantity = 0;

			/* total out quantity */
			$total_out_quantity = $sale_quantity;

			/* purchase amount */
			$this->db->select_sum('total', 'inamount')->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->where('type', 1);
			$purchase_amount_q = $this->db->get();
			if ($purchase_amount_d = $purchase_amount_q->row())
				$purchase_amount = $purchase_amount_d->inamount;
			else
				$purchase_amount = 0;

			/* total in amount */
			$total_in_amount = $opening_inventory_amount + $purchase_amount;

			/* average rate */
			if ($total_in_quantity == 0)
				$average_rate = 0;
			else
				$average_rate = $total_in_amount / $total_in_quantity;
			
			$final_quantity = $total_in_quantity - $total_out_quantity;
			$final_rate = $average_rate;
			$final_amount = $final_quantity * $final_rate;
			return array($final_quantity, $final_rate, $final_amount);
		}
	}
}
