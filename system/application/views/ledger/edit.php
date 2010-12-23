<?php
	echo form_open('ledger/edit/' . $ledger_id);

	echo "<p>";
	echo form_label('Ledger name', 'ledger_name');
	echo "<br />";
	echo form_input($ledger_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Parent group', 'ledger_group_id');
	echo "<br />";
	echo form_dropdown('ledger_group_id', $ledger_group_id, $ledger_group_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Opening balance', 'op_balance');
	echo "<br />";
	echo form_dropdown_dc('op_balance_dc', $op_balance_dc);
	echo " ";
	echo form_input($op_balance);
	echo "</p>";

	echo "<p>";
	echo "<span id=\"tooltip-target-1\">";
	echo form_checkbox('ledger_type_cashbank', 1, $ledger_type_cashbank) . " Cash / Bank Account";
	echo "</span>";
	echo "<span id=\"tooltip-content-1\">Select if Ledger A/C is a Bank A/C or a Cash A/C.</span>";
	echo "</p>";

	echo "<p>";
	echo form_hidden('ledger_id', $ledger_id);
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo "</p>";

	echo form_close();

