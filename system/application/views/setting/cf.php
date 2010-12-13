<?php
	echo form_open('setting/cf');

	echo "<p>";
	echo form_label('C/F Label', 'account_label');
	echo "<br />";
	echo form_input($account_label);
	echo "<br />";
	echo "<span class=\"form-help-text\">Example : prashant0708</span>";
	echo "</p>";

	echo "<p>";
	echo form_label('C/F Account Name', 'account_name');
	echo "<br />";
	echo form_input($account_name);
	echo "</p>";

	echo "<p>";
	echo form_label('C/F Financial Year Start', 'fy_start');
	echo "<br />";
	echo form_input_date($fy_start);
	echo "<br />";
	echo "<span class=\"form-help-text\">Foramt : " . $this->config->item('account_date_format') . "</span>";
	echo "</p>";

	echo "<p>";
	echo form_label('C/F Financial Year End', 'fy_end');
	echo "<br />";
	echo form_input_date($fy_end);
	echo "<br />";
	echo "<span class=\"form-help-text\">Format : " . $this->config->item('account_date_format') . "</span>";
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
	echo form_submit('submit', 'Carrfy forward');
	echo " ";
	echo anchor('setting', 'Back', array('title' => 'Back to settings'));
	echo "</p>";

	echo form_close();

