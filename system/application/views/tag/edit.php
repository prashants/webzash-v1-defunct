<?php
	echo form_open('tag/edit/' . $tag_id);

	echo "<p>";
	echo form_label('Tag Title', 'tag_title');
	echo "<br />";
	echo form_input($tag_title);
	echo "</p>";

	echo "<p>";
	echo form_label('Tag Color', 'tag_color');
	echo "<br />";
	echo "#" . form_input($tag_color);
	echo " &nbsp&nbsp";
	echo "<span id=\"preview_tag_color\" style=\"padding:3px 20px 3px 20px;\"></span>";
	echo "</p>";

	echo "<p>";
	echo form_label('Background Color', 'tag_background');
	echo "<br />";
	echo "#" . form_input($tag_background);
	echo " &nbsp&nbsp";
	echo "<span id=\"preview_tag_background\" style=\"padding:3px 20px 3px 20px;\"></span>";
	echo "</p>";

	echo "<p>";
	echo form_hidden('tag_id', $tag_id);
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('tag', 'Back', 'Back to Tags');
	echo "</p>";

	echo form_close();

