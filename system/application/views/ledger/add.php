<script type="text/javascript">
$(document).ready(function() {
	/* initialize */
	if ($('#affects_inventory').attr("checked") == true)
		$('#ledger_type_cashbank').attr("checked", false);
	else
		$('#affects_inventory_option').hide();
	if ($('#ledger_type_cashbank').attr("checked") == true)
		$('#affects_inventory').attr("checked", false);

	$('#ledger_type_cashbank').click(function() {
		if (this.checked == true) {
			$('#affects_inventory').attr("checked", false);
			$('#affects_inventory_option').fadeOut();
		}
	});
	$('#affects_inventory').click(function() {
		if (this.checked == true) {
			$('#ledger_type_cashbank').attr("checked", false);
			$('#affects_inventory_option').fadeIn();
		} else {
			$('#affects_inventory_option').fadeOut();
		}
	});
});
</script>

<?php
	echo form_open('ledger/add');

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
	echo "<span id=\"tooltip-target-1\">";
	echo form_dropdown_dc('op_balance_dc', $op_balance_dc);
	echo " ";
	echo form_input($op_balance);
	echo "</span>";
	echo "<span id=\"tooltip-content-1\">&nbsp;&nbsp;Assets / Expenses => Dr. Balance<br />Liabilities / Incomes => Cr. Balance</span>";
	echo "</p>";

	echo "<p>";
	echo "<span id=\"tooltip-target-2\">";
	echo form_checkbox('ledger_type_cashbank', 1, $ledger_type_cashbank, 'id="ledger_type_cashbank"') . " Bank or Cash Account";
	echo "</span>";
	echo "<span id=\"tooltip-content-2\">Select if Ledger Account is of type Bank or Cash Account.</span>";
	echo "</p>";

	echo "<p>";
	echo form_checkbox('affects_inventory', 1, $affects_inventory, 'id="affects_inventory"') . " Affects Inventory ";
	echo form_dropdown('affects_inventory_option', $affects_inventory_options, $affects_inventory_option_active, 'id="affects_inventory_option"');
	echo "</p>";

	echo "<p>";
	echo "<span id=\"tooltip-target-3\">";
	echo form_checkbox('reconciliation', 1, $reconciliation) . " Reconciliation";
	echo "</span>";
	echo "<span id=\"tooltip-content-3\">If enabled account can be reconciled from Reports > Reconciliation</span>";
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo "</p>";

	echo form_close();

