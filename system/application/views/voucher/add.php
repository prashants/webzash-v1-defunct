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
		echo "<tr>";
		echo "<td>" . form_dropdown_dc('ledger_dc', isset($ledger_dc) ? $ledger_dc : '') . "</td>";
		echo "<td>" . form_input_ledger('voucher_ledger_id') . "</td>";
		echo "<td>" . form_input($dr_amount) . "</td>";
		echo "<td>" . form_input($cr_amount) . "</td>";
		echo "<td>- +</td>";
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
