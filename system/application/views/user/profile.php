<?php
	echo "<p>";
	echo "You are currently logged in as: " . "<strong>" .  $this->session->userdata('user_name') . "</strong>";
	echo " (";
	echo anchor('user/logout', 'Logout', array('title' => 'Logout', 'class' => 'anchor-link-a'));
	echo ")";
	echo "</p>";

	echo "<p>";
	echo "Your current role is: " . "<strong>" .  $this->session->userdata('user_role') . "</strong>";
	echo "</p>";

	echo "<p>";
	echo "Currently active account is: " . "<strong>";
	if ($this->session->userdata('active_account'))
		echo $this->session->userdata('active_account');
	else
		echo "(None)";
	echo "</strong>";
	echo " (";
	echo anchor('user/account', 'Change', array('title' => 'Change Account', 'class' => 'anchor-link-a'));
	echo ")";
	echo "</p>";

	echo "<p>";
	echo "Application version is: " . "<strong>" .  $this->config->item('application_version') . "</strong>";
	echo "</p>";
