<?php
	echo form_open('group/edit/' . $group_id);
	echo "<p>";
	echo form_label('Group name', 'group_name');
	echo "<br />";
	echo form_input($group_name);
	echo "</p>";
	echo "<p>";
	echo form_label('Parent group', 'group_parent');
	echo "<br />";
	echo form_dropdown('group_parent', $group_parent, $group_parent_active);
	echo "</p>";
	echo form_hidden('group_id', $group_id);
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo form_close();
?>
