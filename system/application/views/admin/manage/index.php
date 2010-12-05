<?php
echo "<p>";
echo "<b>Currently active account : </b>";
echo $this->session->userdata('db_active_label');
echo "</p>";

echo "<table border=0 cellpadding=5 class=\"generaltable\">";
echo "<thead><tr><th>Label</th><th>Hostname</th><th>Port</th><th>Database</th><th>Username</th><th>Actions</th></tr></thead>";
echo "<tbody>";
$odd_even = "odd";
foreach ($accounts as $label)
{
	$ini_file = "system/application/config/accounts/" . $label . ".ini";

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
	if ($this->session->userdata('db_active_label') == $label)
		echo " tr-draft";
	echo "\">";
	echo "<td>";
	echo $label;
	echo "</td>";
	echo "<td>" . $db_host . "</td>";
	echo "<td>" . $db_port . "</td>";
	echo "<td>" . $db_name . "</td>";
	echo "<td>" . $db_user . "</td>";
	echo "<td>" . anchor("admin/active/index/" . $label, "Activate", array('title' => 'Activate ' . ucfirst($label) . ' Account', 'style' => 'color:#000000;')) . "</td>";
	echo "</tr>";
	$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
echo "</tbody>";
echo "</table>";
echo "<br />";
echo anchor('admin', 'Back', array('title' => 'Back to admin'));

