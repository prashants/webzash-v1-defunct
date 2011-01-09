<?php
echo "<p>";
echo "<b>Currently active account : </b>";
echo $this->session->userdata('active_account');
echo "</p>";

echo "<table border=0 cellpadding=5 class=\"simple-table manage-account-table\">";
echo "<thead><tr><th>Label</th><th>Hostname</th><th>Port</th><th>Database</th><th>Username</th><th></th><th></th></tr></thead>";
echo "<tbody>";
$odd_even = "odd";
foreach ($accounts as $label)
{
	$ini_file = $this->config->item('config_path') . "accounts/" . $label . ".ini";

	/* Check if database ini file exists */
	if (get_file_info($ini_file))
	{
		/* Parsing database ini file */
		$active_accounts = parse_ini_file($ini_file);
		if ($active_accounts)
		{
			$db_host = isset($active_accounts['db_hostname']) ? $active_accounts['db_hostname'] : "-";
			$db_port = isset($active_accounts['db_port']) ? $active_accounts['db_port'] : "-";
			$db_name = isset($active_accounts['db_name']) ? $active_accounts['db_name'] : "-";
			$db_user = isset($active_accounts['db_username']) ? $active_accounts['db_username'] : "-";
		}
	}

	echo "<tr class=\"tr-" . $odd_even;
	if ($this->session->userdata('active_account') == $label)
		echo " tr-active";
	echo "\">";
	echo "<td>";
	echo $label;
	echo "</td>";
	echo "<td>" . $db_host . "</td>";
	echo "<td>" . $db_port . "</td>";
	echo "<td>" . $db_name . "</td>";
	echo "<td>" . $db_user . "</td>";

	echo "<td>";
	if ($this->session->userdata('active_account') == $label)
		echo "Active";
	echo "</td>";

	echo "<td>";
	echo anchor("admin/manage/edit/" . $label, "Edit", array('title' => 'Edit ' . ucfirst($label) . ' Account', 'class' => 'red-link'));
	echo " &nbsp;" . anchor('admin/manage/delete/' .  $label, img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Account Settings', 'class' => "confirmClick", 'title' => "Delete Account Settings")), array('title' => 'Delete Account Settings')) . " ";
	echo "</td>";

	echo "</tr>";
	$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
echo "</tbody>";
echo "</table>";
echo "<br />";
echo anchor('admin', 'Back', array('title' => 'Back to admin'));

