<?php
	echo form_open('admin/manage/edit/' . $database_label);

	echo "<p>";
	echo "Label";
	echo "<br />";
	echo $database_label;
	echo "</p>";

	echo "<p>";
	echo form_label('Database Name', 'database_name');
	echo "<br />";
	echo form_input($database_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Database Username', 'database_username');
	echo "<br />";
	echo form_input($database_username);
	echo "</p>";

	echo "<p>";
	echo form_label('Database Password', 'database_password');
	echo "<br />";
	echo form_password($database_password);
	echo "</p>";

	echo "<p>";
	echo form_label('Database Host', 'database_host');
	echo "<br />";
	echo form_input($database_host);
	echo "</p>";

	echo "<p>";
	echo form_label('Database Port', 'database_port');
	echo "<br />";
	echo form_input($database_port);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Edit');
	echo " ";
	echo anchor('admin/manage', 'Back', array('title' => 'Back to active account list'));
	echo "</p>";

	echo form_close();

