<?php
	echo form_open('admin/user/add');

	echo "<p>";
	echo form_label('Username', 'user_name');
	echo "<br />";
	echo form_input($user_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Password', 'user_password');
	echo "<br />";
	echo form_password($user_password);
	echo "</p>";

	echo "<p>";
	echo form_label('Email', 'user_email');
	echo "<br />";
	echo form_input($user_email);
	echo "</p>";

	echo "<p>";
	echo form_label('Role', 'user_role');
	echo "<br />";
	echo form_dropdown('user_role', $user_roles, $active_user_role);
	echo "</p>";

	echo "<p>";
	echo form_checkbox('user_status', 1, $user_status) . " Active";
	echo "</p>";

	echo "<p>";
	echo form_label('Select accounts', 'accounts[]');
	echo "<br />";
	echo form_multiselect('accounts[]', $accounts, $accounts_active);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Add');
	echo " ";
	echo anchor('admin/user', 'Back', array('title' => 'Back to user list'));
	echo "</p>";

	echo form_close();

