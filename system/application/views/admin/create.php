<?php
	echo form_open('admin/create');

	echo "<p>";
	echo form_label('Label', 'account_label');
	echo "<br />";
	echo form_input($account_label);
	echo "<br />";
	echo "<span class=\"form-help-text\">Example : prashant0708</span>";
	echo "</p>";

	echo "<p>";
	echo form_label('Account Name', 'account_name');
	echo "<br />";
	echo form_input($account_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Account Address', 'account_address');
	echo "<br />";
	echo form_textarea($account_address);
	echo "</p>";

	echo "<p>";
	echo form_label('Account Email', 'account_email');
	echo "<br />";
	echo form_input($account_email);
	echo "</p>";

	echo "<p>";
	echo form_label('Currency', 'account_currency');
	echo "<br />";
	echo form_input($account_currency);
	echo "</p>";

	echo "<p>";
	echo form_label('Date Format', 'account_date');
	echo "<br />";
	echo form_dropdown('account_date', $account_date_options, $account_date);
	echo "</p>";

	echo "<p>";
	echo form_label('Financial Year Start', 'fy_start');
	echo "<br />";
	echo form_input_date($fy_start);
	echo "<br />";
	echo "<span class=\"form-help-text\">Warning : Financial Year Start cannot be changed later.<br />Format as per 'Date Foramt' selected abobe.</span>";
	echo "</p>";

	echo "<p>";
	echo form_label('Financial Year End', 'fy_end');
	echo "<br />";
	echo form_input_date($fy_end);
	echo "<br />";
	echo "<span class=\"form-help-text\">Warning : Financial Year End cannot be changed later.<br />Format as per 'Date Foramt' selected abobe.</span>";
	echo "</p>";

	echo "<p>";
	echo form_label('Timezone');
	echo "<br />";
	echo timezone_menu($account_timezone);
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Database Settings', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_checkbox('create_database', 1, $create_database) . " Create database if it does not exists";
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

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('admin', 'Back', array('title' => 'Back to admin'));
	echo "</p>";

	echo form_close();

