<?php
	echo form_open('setting/vouchertypes/edit/' . $voucher_type_id);

	echo "<p>";
	echo form_label('Label', 'voucher_type_label');
	echo "<br />";
	echo form_input($voucher_type_label);
	echo "<br />";
	echo "<span class=\"form-help-text\">Note: Only alphabets are allowed without any spaces or any special characters</span>";
	echo "</p>";

	echo "<p>";
	echo form_label('Name', 'voucher_type_name');
	echo "<br />";
	echo form_input($voucher_type_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Description', 'voucher_type_description');
	echo "<br />";
	echo form_textarea($voucher_type_description);
	echo "</p>";

	echo "<p>";
	echo form_label('Base Type', 'voucher_type_base_type');
	echo "<br />";
	echo form_dropdown('voucher_type_base_type', $voucher_type_base_types, $voucher_type_base_type_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Numbering', 'voucher_type_numbering');
	echo "<br />";
	echo form_dropdown('voucher_type_numbering', $voucher_type_numberings, $voucher_type_numbering_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Restrictions', 'bank_cash_ledger_restriction');
	echo "<br />";
	echo form_dropdown('bank_cash_ledger_restriction', $bank_cash_ledger_restrictions, $bank_cash_ledger_restriction_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Prefix', 'voucher_type_prefix');
	echo "<br />";
	echo form_input($voucher_type_prefix);
	echo "</p>";

	echo "<p>";
	echo form_label('Suffix', 'voucher_type_suffix');
	echo "<br />";
	echo form_input($voucher_type_suffix);
	echo "</p>";

	echo "<p>";
	echo form_label('Zero Padding', 'voucher_type_zero_padding');
	echo "<br />";
	echo form_input($voucher_type_zero_padding);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('setting/vouchertypes', 'Back', 'Back to Voucher Types');
	echo "</p>";

	echo form_close();

