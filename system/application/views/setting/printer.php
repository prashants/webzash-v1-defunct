<?php
	echo form_open('setting/printer');

	echo "<p>";
	echo form_fieldset('Paper Size', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_label('Height', 'paper_height');
	echo " ";
	echo form_input($paper_height);
	echo " inches";
	echo "</p>";

	echo "<p>";
	echo "&nbsp;";
	echo form_label('Width', 'paper_width');
	echo " ";
	echo form_input($paper_width);
	echo " inches";
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Paper Margin', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo form_label('Top', 'margin_top');
	echo " ";
	echo form_input($margin_top);
	echo " inches";
	echo "</p>";

	echo "<p>";
	echo form_label('Bottom', 'margin_bottom');
	echo " ";
	echo form_input($margin_bottom);
	echo " inches";
	echo "</p>";

	echo "<p>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo form_label('Left', 'margin_left');
	echo " ";
	echo form_input($margin_left);
	echo " inches";
	echo "</p>";

	echo "<p>";
	echo "&nbsp;&nbsp;&nbsp;";
	echo form_label('Right', 'margin_right');
	echo " ";
	echo form_input($margin_right);
	echo " inches";
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Orientation', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_radio($orientation_potrait);
	echo " Potrait";
	echo form_radio($orientation_landscape);
	echo " Landscape";
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Output Format', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_radio($output_format_html);
	echo " HTML";
	echo form_radio($output_format_text);
	echo " Text";
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('setting', 'Back', array('title' => 'Back to settings'));
	echo "</p>";

	echo form_close();

