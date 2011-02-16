<script type="text/javascript">

$(document).ready(function() {

	/***************************** STOCK ITEM *****************************/
	/* Stock Item dropdown changed */
	$('.stock-item-dropdown').live('change', function() {
		if ($(this).val() == "0") {
			$(this).parent().next().children().attr('value', "");
			$(this).parent().next().next().children().attr('value', "");
			$(this).parent().next().next().next().children().attr('value', "");
			$(this).parent().next().next().next().next().children().attr('value', "");
			$(this).parent().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().next().next().children().attr('disabled', 'disabled');
		} else {
			$(this).parent().next().children().attr('disabled', '');
			$(this).parent().next().next().children().attr('disabled', '');
			$(this).parent().next().next().next().children().attr('disabled', '');
			$(this).parent().next().next().next().next().children().attr('disabled', '');
			$(this).parent().prev().children().trigger('change');
		}
		var stockid = $(this).val();
		var rowid = $(this);
		if (stockid > 0) {
			$.ajax({
				url: <?php echo '\'' . site_url('inventory/stockitem/balance') . '/\''; ?> + stockid,
				success: function(data) {
					var stock_bal = parseFloat(data);
					if (isNaN(stock_bal))
						stock_bal = 0;
					if (stock_bal == 0)
						rowid.parent().next().next().next().next().next().next().children().text("0");
					else if (stock_bal < 0)
						rowid.parent().next().next().next().next().next().next().next().children().text(data);
					else
						rowid.parent().next().next().next().next().next().next().next().children().text(data);
				}
			});
		} else {
			rowid.parent().next().next().next().next().next().next().next().children().text("");
		}
	});

	$('table td .quantity-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev());
	});

	$('table td .rate-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev().prev());
	});

	$('table td .discount-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev().prev().prev());
	});

	var calculateRowTotal = function(itemrow) {
		var item_quantity = itemrow.next().children().val();
		var item_rate_per_unit = itemrow.next().next().children().val();
		var item_discount = itemrow.next().next().next().children().val();
		var is_percent = false;

		/* check whether discount is in percent or absolute value */
		if (item_discount != "") {
			if (item_discount.match(/%$/))
			{
				is_percent = true;
			}
		}
		item_quantity = parseFloat(item_quantity);
		item_rate_per_unit = parseFloat(item_rate_per_unit);
		item_discount = parseFloat(item_discount);
		if (isNaN(item_discount))
			item_discount = 0;
		if ((!isNaN(item_quantity)) && (!isNaN(item_rate_per_unit)))
		{
			/* calculating total amount for each stock item */
			var item_amount;
			if (is_percent) {
				if (item_discount <= 100)
					item_amount = ((item_quantity * item_rate_per_unit) * (100 - item_discount)) / 100;
			} else {
				item_amount = (item_quantity * item_rate_per_unit) - item_discount;
			}
			/* displaying total amount for each stock item */
			itemrow.next().next().next().next().children().val(item_amount);
			itemrow.next().next().next().next().fadeTo('slow', 0.1).fadeTo('slow', 1);
		}
	}

	$('table td .amount-stock-item').live('change', function() {
		calculateStockTotal();
	});

	/* calculating stock total */
	var calculateStockTotal = function() {
		var stock_total = 0;
		$('table td .amount-stock-item').each(function(index) {
			if ($(this).val() != "")
			{
				var item_amount = parseFloat($(this).val());
				if ( ! isNaN(item_amount))
					stock_total += item_amount;
			}
		});
		return stock_total;
	}

	/* Add stock item row */
	$('table td .addstockrow').live('click', function() {
		var cur_obj = this;
		var add_image_url = $(cur_obj).attr('src');
		$(cur_obj).attr('src', <?php echo '\'' . asset_url() . 'images/icons/ajax.gif' . '\''; ?>);
		$.ajax({
			url: <?php echo '\'' . site_url('inventory/stockvoucher/addstockrow') . '\''; ?>,
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
				$(cur_obj).attr('src', add_image_url);
				$('.stock-item-dropdown').trigger('change');
			}
		});
	});

	/* Delete stock item row */
	$('table td .deletestockrow').live('click', function() {
		$(this).parent().parent().remove();
	});

	/******************************* LEDGER *******************************/
	/* Dr - Cr dropdown changed */
	$('.dc-dropdown').live('change', function() {
	});

	/* Ledger dropdown changed */
	$('.ledger-dropdown').live('change', function() {
		if ($(this).val() == "0") {
			$(this).parent().next().children().attr('value', "");
			$(this).parent().next().next().children().attr('value', "");
			$(this).parent().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().children().attr('disabled', 'disabled');
		} else {
			$(this).parent().next().children().attr('disabled', '');
			$(this).parent().next().next().children().attr('disabled', '');
			$(this).parent().prev().children().trigger('change');
		}
		$(this).parent().next().children().trigger('change');
		$(this).parent().next().next().children().trigger('change');

		var ledgerid = $(this).val();
		var rowid = $(this);
		if (ledgerid > 0) {
			$.ajax({
				url: <?php echo '\'' . site_url('ledger/balance') . '/\''; ?> + ledgerid,
				success: function(data) {
					var ledger_bal = parseFloat(data);
					if (isNaN(ledger_bal))
						ledger_bal = 0;
					if (ledger_bal == 0)
						rowid.parent().next().next().next().next().next().children().text("0");
					else if (ledger_bal < 0)
						rowid.parent().next().next().next().next().next().children().text("Cr " + -data);
					else
						rowid.parent().next().next().next().next().next().children().text("Dr " + data);
				}
			});
		} else {
			rowid.parent().next().next().next().next().next().children().text("");
		}
	});

	/* Recalculate Total */
	$('table td .recalculate').live('click', function() {

	});

	/* Delete ledger row */
	$('table td .deleterow').live('click', function() {
		$(this).parent().parent().remove();
	});

	/* Add ledger row */
	$('table td .addrow').live('click', function() {
		var cur_obj = this;
		var add_image_url = $(cur_obj).attr('src');
		$(cur_obj).attr('src', <?php echo '\'' . asset_url() . 'images/icons/ajax.gif' . '\''; ?>);
		$.ajax({
			url: <?php echo '\'' . site_url('inventory/stockvoucher/addrow') . '\''; ?>,
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
				$(cur_obj).attr('src', add_image_url);
				$('.ledger-dropdown').trigger('change');
			}
		});
	});

	/* On page load initiate all triggers */
	$('.dc-dropdown').trigger('change');
	$('.ledger-dropdown').trigger('change');
	$('.stock-item-dropdown').trigger('change');
});

</script>

<?php
	echo form_open('inventory/stockvoucher/add/' . $current_voucher_type['label']);
	echo "<p>";
	echo "<span id=\"tooltip-target-1\">";
	echo form_label('Voucher Number', 'voucher_number');
	echo " ";
	echo $current_voucher_type['prefix'] . form_input($voucher_number) . $current_voucher_type['suffix'];
	echo "</span>";
	echo "<span id=\"tooltip-content-1\">Leave Voucher Number empty for auto numbering</span>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<span id=\"tooltip-target-2\">";
	echo form_label('Voucher Date', 'voucher_date');
	echo " ";
	echo form_input_date_restrict($voucher_date);
	echo "</span>";
	echo "<span id=\"tooltip-content-2\">Date format is " . $this->config->item('account_date_format') . ".</span>";
	echo "</p>";

	if ($current_voucher_type['stock_voucher_type'] == '1')
	{
		echo "<p>";
		echo "<table border=0 cellpadding=2>";
		echo "<tr>";
		echo "<td align=\"right\">";
		echo form_label('Purchase Ledger A/C', 'main_account');
		echo "</td>";
		echo "<td>";
		echo form_input_ledger('main_account', $main_account_active, '', $type = 'purchase');
		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"right\">";
		echo form_label('Creditor (Supplier)', 'main_entity');
		echo "</td>";
		echo "<td>";
		echo form_input_ledger('main_entity', $main_entity_active, '', $type = 'creditor');
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</p>";
	} else {
		echo "<p>";
		echo "<table border=0 cellpadding=2>";
		echo "<tr>";
		echo "<td align=\"right\">";
		echo form_label('Sales Ledger A/C', 'main_account');
		echo "</td>";
		echo "<td>";
		echo form_input_ledger('main_account', $main_account_active, '', $type = 'sale');
		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"right\">";
		echo form_label('Debtor (Customer)', 'main_entity');
		echo "</td>";
		echo "<td>";
		echo form_input_ledger('main_entity', $main_entity_active, '', $type = 'debtor');
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</p>";
	}

	echo "<p></p>";

	echo "<table class=\"voucher-table\">";
	echo "<thead><tr><th>Stock Item</th><th>Quantity</th><th>Rate Per Unit</th><th>Discount</th><th>Amount</th><th colspan=2></th><th colspan=2>Cur Balance</th></tr></thead>";

	foreach ($stock_item_id as $i => $row)
	{
		$stock_item_quantity_item = array(
			'name' => 'stock_item_quantity[' . $i . ']',
			'id' => 'stock_item_quantity[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($stock_item_quantity[$i]) ? $stock_item_quantity[$i] : '',
			'class' => 'quantity-stock-item',
		);
		$stock_item_rate_per_unit_item = array(
			'name' => 'stock_item_rate_per_unit[' . $i . ']',
			'id' => 'stock_item_rate_per_unit[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($stock_item_rate_per_unit[$i]) ? $stock_item_rate_per_unit[$i] : '',
			'class' => 'rate-stock-item',
		);
		$stock_item_discount_item = array(
			'name' => 'stock_item_discount[' . $i . ']',
			'id' => 'stock_item_discount[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($stock_item_discount[$i]) ? $stock_item_discount[$i] : '',
			'class' => 'discount-stock-item',
		);
		$stock_item_amount_item = array(
			'name' => 'stock_item_amount[' . $i . ']',
			'id' => 'stock_item_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => isset($stock_item_amount[$i]) ? $stock_item_amount[$i] : '',
			'class' => 'amount-stock-item',
		);
		echo "<tr>";

		echo "<td>" . form_input_stock_item('stock_item_id[' . $i . ']', isset($stock_item_id[$i]) ? $stock_item_id[$i] : 0) . "</td>";
		echo "<td>" . form_input($stock_item_quantity_item) . "</td>";
		echo "<td>" . form_input($stock_item_rate_per_unit_item) . "</td>";
		echo "<td>" . form_input($stock_item_discount_item) . "</td>";
		echo "<td>" . form_input($stock_item_amount_item) . "</td>";

		echo "<td>" . img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addstockrow')) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deletestockrow')) . "</td>";

		echo "<td class=\"stock-item-balance\"><div></div></td>";

		echo "</tr>";
	}
	echo "</table>";

	echo "<br />";
	echo "<br />";

	echo "<table class=\"voucher-table\">";
	echo "<thead><tr><th>Type</th><th>Ledger A/C</th><th>Rate %</th><th>Amount</th><th colspan=2></th><th colspan=2>Cur Balance</th></tr></thead>";

	foreach ($ledger_dc as $i => $ledger)
	{
		$rate_item_item = array(
			'name' => 'rate_item[' . $i . ']',
			'id' => 'rate_item[' . $i . ']',
			'maxlength' => '5',
			'size' => '5',
			'value' => isset($rate_item[$i]) ? $rate_item[$i] : '',
			'class' => 'rate-item',
		);
		$amount_item_item = array(
			'name' => 'amount_item[' . $i . ']',
			'id' => 'amount_item[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => isset($amount_item[$i]) ? $amount_item[$i] : '',
			'class' => 'amount-item',
		);
		echo "<tr>";

		echo "<td>" . form_dropdown_dc('ledger_dc[' . $i . ']', isset($ledger_dc[$i]) ? $ledger_dc[$i] : "D") . "</td>";
		echo "<td>" . form_input_ledger('ledger_id[' . $i . ']', isset($ledger_id[$i]) ? $ledger_id[$i] : 0) . "</td>";
		echo "<td>" . form_input($rate_item_item) . "</td>";
		echo "<td>" . form_input($amount_item_item) . "</td>";

		echo "<td>" . img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addrow')) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deleterow')) . "</td>";

		echo "<td class=\"ledger-balance\"><div></div></td>";

		echo "</tr>";
	}

	echo "<tr><td colspan=\"7\"></td></tr>";
	echo "<tr id=\"voucher-total\"><td colspan=3><strong>Total</strong></td><td id=\"cr-total\">0</td><td>" . img(array('src' => asset_url() . "images/icons/gear.png", 'border' => '0', 'alt' => 'Recalculate Total', 'class' => 'recalculate', 'title' => 'Recalculate Total')) . "</td><td></td><td></td></tr>";

	echo "</table>";

	echo "<p>";
	echo form_label('Narration', 'voucher_narration');
	echo "<br />";
	echo form_textarea($voucher_narration);
	echo "</p>";

	echo "<p>";
	echo form_label('Tag', 'voucher_tag');
	echo " ";
	echo form_dropdown('voucher_tag', $voucher_tags, $voucher_tag);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('voucher/show/' . $current_voucher_type['label'], 'Back', array('title' => 'Back to ' . $current_voucher_type['name'] . ' Vouchers'));
	echo "</p>";

	echo form_close();

