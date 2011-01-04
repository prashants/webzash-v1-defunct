<?php
	echo form_open('admin/active');

	echo "<p>";
	echo "<b>Currently active account : </b>";
	echo $this->session->userdata('active_account');
	echo "</p>";

	echo "<p>";
	echo "Currently available accounts";
	echo "<br />";
	echo form_dropdown('account', $accounts, $account);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Change');
	echo " ";
	echo anchor('admin', 'Back', array('title' => 'Back to admin'));
	echo "</p>";

	echo form_close();

