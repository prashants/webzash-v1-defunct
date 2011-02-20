<?php
	echo form_open('inventory/stockitem/add');

	echo "<p>";
	echo form_label('Stock item name', 'stock_item_name');
	echo "<br />";
	echo form_input($stock_item_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Stock group', 'stock_item_group');
	echo "<br />";
	echo form_dropdown('stock_item_group', $stock_item_groups, $stock_item_group_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Stock unit', 'stock_item_unit');
	echo "<br />";
	echo form_dropdown('stock_item_unit', $stock_item_units, $stock_item_unit_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Costing method', 'stock_item_costing_method');
	echo "<br />";
	echo form_dropdown('stock_item_costing_method', $stock_item_costing_methods, $stock_item_costing_method_active);
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Opening Balance', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_label('Quantity', 'stock_item_op_quantity');
	echo "<br />";
	echo form_input($stock_item_op_quantity);
	echo "</p>";

	echo "<p>";
	echo form_label('Rate per unit', 'stock_item_op_rate_per_unit');
	echo "<br />";
	echo form_input($stock_item_op_rate_per_unit);
	echo "</p>";

	echo "<p>";
	echo form_label('Total value', 'stock_item_op_total');
	echo "<br />";
	echo form_input($stock_item_op_total);
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_label('Default selling price', 'stock_item_default_sell_price');
	echo "<br />";
	echo form_input($stock_item_default_sell_price);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('inventory/account', 'Back', array('title' => 'Back to Inventory'));
	echo "</p>";

	echo form_close();
