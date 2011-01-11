<?php
	$this->db->from('logs')->order_by('id', 'desc');
	$logs_q = $this->db->get();
	echo "<table border=0 class=\"simple-table\">";
	echo "<thead><tr><th width=\"90\">Date</th><th>Host IP</th><th>User</th><th>Message</th><th width=\"30\">URL</th><th>Browser</th></tr></thead>";
	foreach ($logs_q->result() as $row)
	{
		echo "<tr>";
		echo "<td>" . date_mysql_to_php_display($row->date) . "</td>";
		echo "<td>" . $row->host_ip . "</td>";
		echo "<td>" . $row->user . "</td>";
		echo "<td>" . $row->message_title . "</td>";
		echo "<td>" .  anchor($row->url, "Link", array('title' => 'Link to action', 'class' => 'anchor-link-a')) . "</td>";
		echo "<td>" . character_limiter($row->user_agent, 30) . "</td>";
		echo "</tr>";
	}
	echo "</table>";
