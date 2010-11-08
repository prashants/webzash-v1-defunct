<?php
	echo form_open('setting/company');

	echo "<p>";
	echo form_label('Company Name', 'company_name');
	echo "<br />";
	echo form_input($company_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Company Address', 'company_address');
	echo "<br />";
	echo form_textarea($company_address);
	echo "</p>";

	echo "<p>";
	echo form_label('Company Email', 'company_email');
	echo "<br />";
	echo form_input($company_email);
	echo "</p>";

	echo "<p>";
	echo form_label('Assessment Year Start', 'assy_start');
	echo "<br />";
	echo form_input_date($assy_start);
	echo "</p>";

	echo "<p>";
	echo form_label('Assessment Year End', 'assy_end');
	echo "<br />";
	echo form_input_date($assy_end);
	echo "</p>";

	echo "<p>";
	echo form_label('Currency', 'company_currency');
	echo "<br />";
	echo form_input($company_currency);
	echo "</p>";

	echo "<p>";
	echo form_label('Date Format', 'company_date');
	echo "<br />";
	echo form_input($company_date);
	echo "</p>";

	echo "<p>";
	echo form_label('Timezone');
	echo "<br />";
	echo timezone_menu($company_timezone);
	echo "</p>";

	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('setting', 'Back', 'Back to Settings');
	echo form_close();
?>
