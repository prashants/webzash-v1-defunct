<?php
	echo form_open('voucher/add/' . $voucher_type);
	echo "<p>";
	echo form_label('Voucher Number', 'voucher_number');
	echo " ";
	echo form_input($voucher_number);
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo form_label('Voucher Date', 'voucher_date');
	echo " ";
	echo form_input_date($voucher_date);
	echo "</p>";

	echo "<table class=\"generaltable\">";
	echo "<thead><tr><th>Type</th><th>Ledger A/C</th><th>Dr Amount</th><th>Cr Amount</th><th>Actions</th></tr></thead>";

	for ($i = 0; $i < 5; $i++)
	{
		$dr_amount = array(
			'name' => 'dr_amount[' . $i . ']',
			'id' => 'dr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$cr_amount = array(
			'name' => 'cr_amount[' . $i . ']',
			'id' => 'cr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		echo "<tr>";
		echo "<td>" . form_dropdown_dc('ledger_dc[' . $i . ']') . "</td>";
		echo "<td>" . form_input_ledger('ledger_id[' . $i . ']') . "</td>";
		echo "<td>" . form_input($dr_amount) . "</td>";
		echo "<td>" . form_input($cr_amount) . "</td>";
		echo "<td> - + </td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<br />";

	echo "<p>";
	echo form_label('Narration', 'voucher_narration');
	echo "<br />";
	echo form_textarea($voucher_narration);
	echo "</p>";

	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('voucher/show/receipt', 'Back', 'Back to Receipt Vouchers');
	echo form_close();
?>
