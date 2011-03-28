<?php
	echo form_open('inventory/group/edit/' . $inventory_group_id);

	echo "<p>";
	echo form_label('Inventory group name', 'inventory_group_name');
	echo "<br />";
	echo form_input($inventory_group_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Parent inventory group', 'inventory_group_parent');
	echo "<br />";
	echo form_dropdown('inventory_group_parent', $inventory_group_parents, $inventory_group_parent_active);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('inventory/account', 'Back', array('title' => 'Back to Inventory'));
	echo "</p>";

	echo form_close();
