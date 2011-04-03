<style type="text/css">
#error-box {
	border:solid 1px #C34A2C;
	background:#FFBABA;
	color:#222222;
	padding:0 10px 0 10px;
	margin:0 0 10px 0;
	text-align:left;
}

#message-box {
	border:solid 1px #FFEC8B;
	background:#FFF8C6;
	color:#222222;
	padding:0 10px 0 10px;
	margin:0 0 10px 0;
	text-align:left;
}
</style>
<?php
	if (isset($error) && $error)
	{
		echo "<div id=\"error-box\">";
		echo "<ul>";
		echo ($error);
		echo "</ul>";
		echo "</div>";
	}

	if (isset($message) && $message)
	{
		echo "<div id=\"message-box\">";
		echo "<ul>";
		echo ($message);
		echo "</ul>";
		echo "</div>";
	}

	echo form_open('entry/email/' . $current_entry_type['label'] . "/" . $entry_id);

	echo "Emailing " .  $current_entry_type['name'] . " Entry No. " . $entry_number . "<br />";

	echo "<p>";
	echo form_label('Email to', 'email_to');
	echo "<br />";
	echo form_input($email_to);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Send Email');
	echo "</p>";

	echo form_close();

