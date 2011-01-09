<?php
echo "<p>";
echo "<b>You are logged in as : </b>";
echo $this->session->userdata('user_name');
echo "</p>";

echo "<table border=0 cellpadding=5 class=\"simple-table manage-account-table\">";
echo "<thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>";
echo "<tbody>";
$odd_even = "odd";
foreach ($users as $row)
{
	$ini_file = $this->config->item('config_path') . "users/" . $row . ".ini";

	/* Check if database ini file exists */
	if (get_file_info($ini_file))
	{
		/* Parsing database ini file */
		$active_users = parse_ini_file($ini_file);
		if ($active_users)
		{
			$username = isset($active_users['username']) ? $active_users['username'] : "-";
			$email = isset($active_users['email']) ? $active_users['email'] : "-";
			$role = isset($active_users['role']) ? $active_users['role'] : "-";
			$status = isset($active_users['status']) ? $active_users['status'] : "-";
		}
	}

	echo "<tr class=\"tr-" . $odd_even;
	if ($this->session->userdata('user_name') == $row)
		echo " tr-active";
	echo "\">";
	echo "<td>" . $username . "</td>";
	echo "<td>" . $email . "</td>";

	echo "<td>";
	switch ($role)
	{
		case "administrator": echo "administrator"; break;
		case "manager": echo "manager"; break;
		case "accountant": echo "accountant"; break;
		case "dataentry": echo "dataentry"; break;
		case "guest": echo "guest"; break;
		default: echo "(unknown)"; break;
	}
	echo "</td>";

	echo "<td>";
	switch ($status)
	{
		case 0: echo "Disabled"; break;
		case 1: echo "Active"; break;
		default: echo "(unknown)"; break;
	}
	echo "</td>";

	if ($this->session->userdata('user_name') == $row)
	{
		echo "<td>";
		echo anchor("admin/user/edit/" . $row, "Edit", array('title' => 'Edit User', 'class' => 'red-link'));
		echo "</td>";
	} else {
		echo "<td>";
		echo anchor("admin/user/edit/" . $row, "Edit", array('title' => 'Edit User', 'class' => 'red-link'));
		echo " &nbsp;" . anchor('admin/user/delete/' .  $row, img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete User', 'class' => "confirmClick", 'title' => "Delete User")), array('title' => 'Delete User')) . " ";
		echo "</td>";
	}

	echo "</tr>";
	$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
echo "</tbody>";
echo "</table>";
echo "<br />";
echo anchor('admin', 'Back', array('title' => 'Back to admin'));

