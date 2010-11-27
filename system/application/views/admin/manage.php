<?php
	echo form_open('admin/manage');

	echo "<p>";
	echo form_label('Currently Active Accounts List', 'account_list');
	echo "<br />";
	echo form_textarea($account_list);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Save');
	echo " ";
	echo anchor('admin', 'Back', 'Back to admin');
	echo form_close();
	echo "</p>";
?>
