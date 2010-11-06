<?php
	echo form_open('group/add');
	echo "<p>";
	echo form_label('Group name', 'group_name');
	echo "<br />";
	echo form_input($group_name);
	echo "</p>";
	echo "<p>";
	echo form_label('Parent group', 'group_parent');
	echo "<br />";
	echo form_dropdown('group_parent', $group_parent);
	echo "</p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo form_close();
?>
