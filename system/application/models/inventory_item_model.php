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
		$stock_item_q = $this->db->get();
		if ($stock_item = $stock_item_q->row())
			return $stock_item->name;
		else
			return "(Error)";
	}

	function get_closing_quantity($inventory_item_id)
	{
		$this->db->from('inventory_items')->where('id', $inventory_item_id)->limit(1);
		$stock_item_q = $this->db->get();
		if ( ! $stock_item = $stock_item_q->row())
			return 0;

		/* closing quantity */
		$opening_quantity = $stock_item->op_balance_quantity;

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
		$stock_item_q = $this->db->get();
		if ( ! $stock_item = $stock_item_q->row())
			return array(0, 0);

		/* closing quantity */
		$opening_quantity = $stock_item->op_balance_quantity;
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
		$opening_amount = $stock_item->op_balance_total_value;

		/* standard method */
		if ($stock_item->costing_method == 1)
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
		$stock_item_q = $this->db->get();
		if ($stock_item = $stock_item_q->row())
			return $stock_item->default_sell_price;
		else
			return "";
	}
}
