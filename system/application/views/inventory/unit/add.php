<?php
	echo form_open('inventory/unit/add');

	echo "<p>";
	echo form_label('Inventory unit symbol', 'inventory_unit_symbol');
	echo "<br />";
	echo form_input($inventory_unit_symbol);
	echo "</p>";

	echo "<p>";
	echo form_label('Inventory unit name', 'inventory_unit_name');
	echo "<br />";
	echo form_input($inventory_unit_name);
	echo "</p>";

	echo "<p class=\"affects-gross\">";
	echo "<span id=\"tooltip-target-1\">";
	echo form_label('Decimal Places', 'inventory_unit_decimal');
	echo "<br />";
	echo form_input($inventory_unit_decimal);
	echo "</span>";
	echo "<span id=\"tooltip-content-1\">Maximum number of decimal places supported is 4.</span>";
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('inventory/account', 'Back', array('title' => 'Back to Inventory'));
	echo "</p>";

	echo form_close();
