<?php
	echo form_open('user/account');

	echo "<p>";
	echo "<b>Currently active account : </b>";
	$current_active_account = $this->session->userdata('active_account');
	echo ($current_active_account) ? $current_active_account : "(None)";
	echo "</p>";

	if ($accounts)
	{
		echo "<p>";
		echo "Select account";
		echo "<br />";
		echo form_dropdown('account', $accounts, $active_account);
		echo "</p>";

		echo "<p>";
		echo form_submit('submit', 'Activate');
		echo " ";
		echo anchor('', 'Back', array('title' => 'Back to accounts'));
		echo "</p>";
	} else {
		echo "<p>Please create a account by clicking on the above 'Create account' button.</p>";
	}

	echo form_close();

