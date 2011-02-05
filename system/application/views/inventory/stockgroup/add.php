<?php
	echo form_open('inventory/stockgroup/add');

	echo "<p>";
	echo form_label('Stock group name', 'stock_group_name');
	echo "<br />";
	echo form_input($stock_group_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Parent stock group', 'stock_group_parent');
	echo "<br />";
	echo form_dropdown('stock_group_parent', $stock_group_parents, $stock_group_parent_active);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('inventory/account', 'Back', array('title' => 'Back to Inventory'));
	echo "</p>";

	echo form_close();
