<?php
	echo form_open('update/index');

	echo "<p>";
	echo "Required database version : " . "<strong>" .  $this->config->item('required_database_version') . "</strong><br />";
	echo "Current database version : " . "<strong>" .  $account->database_version . "</strong><br />";
	echo "</p>";

	echo "<p>";
	echo "<strong><u>Account Information:</u></strong>" . "<br />";
	echo "&nbsp;&nbsp;&nbsp;<strong>Account Label:</strong> " . $this->session->userdata('active_account') . "<br />";
	echo "&nbsp;&nbsp;&nbsp;<strong>Account Name:</strong> " . $account->name . "<br />";
	echo "&nbsp;&nbsp;&nbsp;<strong>Financial Year Start:</strong> " . date_mysql_to_php_display($account->fy_start) . "<br />";
	echo "&nbsp;&nbsp;&nbsp;<strong>Financial Year End:</strong> " . date_mysql_to_php_display($account->fy_end) . "<br />";
	echo "</p>";

	if ($this->config->item('required_database_version') > $account->database_version)
	{
		echo "<p>";
		echo "<span class=\"form-warning-text\"><h3>Warning: This process will change your database structure.
		It is important to take backup of your current database before continuing, so that you can recover incase of emergency.
		Without taking any backups you risk to loose all your data if this process fails.</h3></span>";
		echo "</p>";

		echo "<p>";
		echo form_submit('submit', 'Update');
		echo " ";
		echo anchor('user/account', 'Back', 'Back to account selection');
		echo "</p>";
	} else {
		echo "<p>";
		echo "<span class=\"form-warning-text\"><h3>Datebase version is already upto date.</h3></span>";
		echo "</p>";

		echo "<p>";
		echo anchor('user/account', 'Back', 'Back to account selection');
		echo "</p>";

	}

	echo form_close();

