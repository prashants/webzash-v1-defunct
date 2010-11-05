<?php
	echo form_open('group/add');
	echo "<p>";
	echo form_label('Group name', 'groupname');
	echo "<br />";
	echo form_input($groupname);
	echo "</p>";
	echo "<p>";
	echo form_label('Parent group', 'groupparent');
	echo "<br />";
	echo form_dropdown('groupparent', $groupparent);
	echo "</p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo form_close();
?>
