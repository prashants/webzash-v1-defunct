<?php
echo "<table border=0 cellpadding=5 class=\"generaltable\">";
echo "<thead><tr><th>No</th><th>Account details</th></tr></thead>";
echo "<tbody>";
$odd_even = "odd";
foreach ($accounts as $id => $row)
{
	if (strlen($row) < 5)
		continue;
	echo "<tr class=\"tr-" . $odd_even;
	echo "\">";
	echo "<td>";
	echo $id;
	echo "</td>";
	echo "<td>";
	echo $row;
	echo "</td>";
	echo "</tr>";
	$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
echo "</tbody>";
echo "</table>";
echo "<br />";
echo anchor('admin', 'Back', array('title' => 'Back to admin'));
?>
