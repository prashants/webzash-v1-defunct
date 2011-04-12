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

	echo "<p>";
	echo "<span class=\"form-warning-text\"><h3>Warning: Please take backup of your current account database before continuing.</h3></span>";
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('user/account', 'Back', 'Back to account selection');
	echo "</p>";

	echo form_close();

