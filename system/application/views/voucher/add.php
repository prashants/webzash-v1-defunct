<script type="text/javascript">
$(document).ready(function() {
	$('table td .deleterow').live('click', function() {
		$(this).parent().parent().remove();
	});

	$('table td .addrow').live('click', function() {
		var cur_obj = this;
		$.ajax({
			url: <?php echo '\'' . site_url('voucher/addrow') . '\''; ?>,
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
			}
		});
	});
});
</script>
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
	echo "<thead><tr><th>Type</th><th>Ledger A/C</th><th>Dr Amount</th><th>Cr Amount</th><th colspan=2>Actions</th></tr></thead>";


	for ($i = 0; $i < 5; $i++)
	{
		$dr_amount = array(
			'name' => 'dr_amount[' . $i . ']',
			'id' => 'dr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => ($_POST) ? $dr_amount_p[$i] : '',
		);
		$cr_amount = array(
			'name' => 'cr_amount[' . $i . ']',
			'id' => 'cr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => ($_POST) ? $cr_amount_p[$i] : '',
		);
		echo "<tr>";
		echo "<td>" . form_dropdown_dc('ledger_dc[' . $i . ']', ($_POST) ? $ledger_dc_p[$i] : '') . "</td>";
		echo "<td>" . form_input_ledger('ledger_id[' . $i . ']', ($_POST) ? $ledger_id_p[$i] : '') . "</td>";
		echo "<td>" . form_input($dr_amount) . "</td>";
		echo "<td>" . form_input($cr_amount) . "</td>";

		//echo "<td>" . anchor('group/edit/', img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger'))) . "</td>";
		//echo "<td>" . anchor('group/delete/', img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deleterow'))) . "</td>";

		echo "<td>" . img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addrow')) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deleterow')) . "</td>";
		echo "</tr>";
	}
	echo "<tr><td colspan=2>TOTAL</td><td>0</td><td>0</td><td></td></tr>";
	echo "</table>";

	echo "<p>";
	echo form_label('Narration', 'voucher_narration');
	echo "<br />";
	echo form_textarea($voucher_narration);
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Options', array('class' => "fieldset-auto-width"));
	echo form_checkbox('draft', 1) . "Create as Draft";
	echo "<br /><br />";
	echo form_checkbox('print', 1) . "Print";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo form_checkbox('email', 1) . "Email";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo form_checkbox('pdf', 1) . "Download PDF";
	echo form_fieldset_close();
	echo "</p>";
	echo "<br /><br />";

	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('voucher/show/receipt', 'Back', 'Back to Receipt Vouchers');
	echo form_close();
?>
