<?php
	echo form_open('tag/add');

	echo "<p>";
	echo form_label('Tag Title', 'tag_title');
	echo "<br />";
	echo form_input($tag_title);
	echo "</p>";

	echo "<p>";
	echo form_label('Tag Color', 'tag_color');
	echo "<br />";
	echo "#" . form_input($tag_color);
	echo "</p>";

	echo "<p>";
	echo form_label('Background Color', 'tag_background');
	echo "<br />";
	echo "#" . form_input($tag_background);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('tag', 'Back', 'Back to Tags');
	echo "</p>";

	echo form_close();

