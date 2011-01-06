<?php
	echo form_open('admin/setting');

	echo "<p>";
	echo form_label('Number of rows to display per page', 'row_count');
	echo "<br />";
	echo form_dropdown('row_count', $row_count_options, $row_count);
	echo "</p>";

	echo "<p>";
	echo form_checkbox('log', 1, $log) . " Log Messages";
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('admin', 'Back', array('title' => 'Back to admin'));
	echo "</p>";

	echo form_close();

