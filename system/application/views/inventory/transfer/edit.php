<script type="text/javascript">

$(document).ready(function() {

	var firstTime = true;

	/********************** SOURCE INVENTORY ITEM *************************/
	/* Stock Item dropdown changed */
	$('.stock-item-dropdown').live('change', function() {
		if ($(this).val() == "0") {
			$(this).parent().next().children().attr('value', "");
			$(this).parent().next().next().children().attr('value', "");
			$(this).parent().next().next().next().children().attr('value', "");
			$(this).parent().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().next().children().attr('disabled', 'disabled');
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
				url: <?php echo '\'' . site_url('inventory/item/balance') . '/\''; ?> + stockid,
				success: function(data) {
					rowid.parent().next().next().next().next().next().next().children().text(data);
					rowid.parent().next().next().next().next().next().next().children().text(data);
				}
			});

			if (!firstTime) {
				$.ajax({
					url: <?php echo '\'' . site_url('inventory/item/sellprice') . '/\''; ?> + stockid,
					success: function(data) {
						var sell_price = parseFloat(data);
						if (isNaN(sell_price))
							sell_price = 0;
						if (sell_price <= 0)
							rowid.parent().next().next().children().val("");
						else
							rowid.parent().next().next().children().val(sell_price);
					}
				});
			}
		} else {
			rowid.parent().next().next().next().next().next().next().children().text("");
		}
	});

	$('table td .source-quantity-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev());
	});

	$('table td .source-rate-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev().prev());
	});

	var calculateRowTotal = function(itemrow) {
		var item_quantity = itemrow.next().children().val();
		var item_rate_per_unit = itemrow.next().next().children().val();

		item_quantity = parseFloat(item_quantity);
		item_rate_per_unit = parseFloat(item_rate_per_unit);

		if ((!isNaN(item_quantity)) && (!isNaN(item_rate_per_unit)))
		{
			/* calculating total amount for each inventory item */
			var item_amount;
			item_amount = (item_quantity * item_rate_per_unit);

			/* displaying total amount for each inventory item */
			itemrow.next().next().next().children().val(item_amount);
			itemrow.next().next().next().fadeTo('slow', 0.1).fadeTo('slow', 1);
		}
		$('.source-recalculate').trigger('click');
	}

	$('table td .source-amount-stock-item').live('change', function() {
		$('.source-recalculate').trigger('click');
	});

	/* calculating inventory total */
	var calculateSourceStockTotal = function() {
		var stock_total = 0;
		$('table td .source-amount-stock-item').each(function(index) {
			if ($(this).val() != "")
			{
				var item_amount = parseFloat($(this).val());
				if ( ! isNaN(item_amount))
					stock_total += item_amount;
			}
		});
		return stock_total;
	}

	/* Add inventory item row */
	$('table td .addstockrow').live('click', function() {
		var cur_obj = this;
		var add_image_url = $(cur_obj).attr('src');
		$(cur_obj).attr('src', <?php echo '\'' . asset_url() . 'images/icons/ajax.gif' . '\''; ?>);
		$.ajax({
			url: <?php echo '\'' . site_url('inventory/transfer/addstockrow/source') . '\''; ?>,
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
				$(cur_obj).attr('src', add_image_url);
				$('.stock-item-dropdown').trigger('change');
			}
		});
	});

	/* Delete inventory item row */
	$('table td .deletestockrow').live('click', function() {
		$(this).parent().parent().remove();
	});

	$('.stock-item-dropdown').trigger('change');

	/* Recalculate Source Total */
	$('table td .source-recalculate').live('click', function() {
		var sourceTotal = calculateSourceStockTotal();
		$("table tr #source-total").text(sourceTotal);
		if (sourceTotal >= 0)
			$("table tr #source-total").css("background-color", "#FFFF99");
		else
			$("table tr #source-total").css("background-color", "#FFE9E8");
	});

	/************************ DEST INVENTORY ITEM *************************/
	/* Inventory Item dropdown changed */
	$('.stock-item-dropdown').live('change', function() {
		if ($(this).val() == "0") {
			$(this).parent().next().children().attr('value', "");
			$(this).parent().next().next().children().attr('value', "");
			$(this).parent().next().next().next().children().attr('value', "");
			$(this).parent().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().children().attr('disabled', 'disabled');
			$(this).parent().next().next().next().children().attr('disabled', 'disabled');
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
				url: <?php echo '\'' . site_url('inventory/item/balance') . '/\''; ?> + stockid,
				success: function(data) {
					rowid.parent().next().next().next().next().next().next().children().text(data);
					rowid.parent().next().next().next().next().next().next().children().text(data);
				}
			});

			if (!firstTime) {
				$.ajax({
					url: <?php echo '\'' . site_url('inventory/item/sellprice') . '/\''; ?> + stockid,
					success: function(data) {
						var sell_price = parseFloat(data);
						if (isNaN(sell_price))
							sell_price = 0;
						if (sell_price <= 0)
							rowid.parent().next().next().children().val("");
						else
							rowid.parent().next().next().children().val(sell_price);
					}
				});
			}
		} else {
			rowid.parent().next().next().next().next().next().next().children().text("");
		}
	});

	$('table td .dest-quantity-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev());
	});

	$('table td .dest-rate-stock-item').live('change', function() {
		var rowid = $(this);
		calculateRowTotal(rowid.parent().prev().prev());
	});

	var calculateRowTotal = function(itemrow) {
		var item_quantity = itemrow.next().children().val();
		var item_rate_per_unit = itemrow.next().next().children().val();

		item_quantity = parseFloat(item_quantity);
		item_rate_per_unit = parseFloat(item_rate_per_unit);

		if ((!isNaN(item_quantity)) && (!isNaN(item_rate_per_unit)))
		{
			/* calculating total amount for each inventory item */
			var item_amount;
			item_amount = (item_quantity * item_rate_per_unit);

			/* displaying total amount for each inventory item */
			itemrow.next().next().next().children().val(item_amount);
			itemrow.next().next().next().fadeTo('slow', 0.1).fadeTo('slow', 1);
		}
		$('.dest-recalculate').trigger('click');
	}

	$('table td .dest-amount-stock-item').live('change', function() {
		$('.dest-recalculate').trigger('click');
	});

	/* calculating inventory total */
	var calculateDestStockTotal = function() {
		var stock_total = 0;
		$('table td .dest-amount-stock-item').each(function(index) {
			if ($(this).val() != "")
			{
				var item_amount = parseFloat($(this).val());
				if ( ! isNaN(item_amount))
					stock_total += item_amount;
			}
		});
		return stock_total;
	}

	/* Add inventory item row */
	$('table td .addstockrow').live('click', function() {
		var cur_obj = this;
		var add_image_url = $(cur_obj).attr('src');
		$(cur_obj).attr('src', <?php echo '\'' . asset_url() . 'images/icons/ajax.gif' . '\''; ?>);
		$.ajax({
			url: <?php echo '\'' . site_url('inventory/transfer/addstockrow/dest') . '\''; ?>,
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
				$(cur_obj).attr('src', add_image_url);
				$('.stock-item-dropdown').trigger('change');
			}
		});
	});

	/* Delete inventory item row */
	$('table td .deletestockrow').live('click', function() {
		$(this).parent().parent().remove();
	});

	/* Recalculate Dest Total */
	$('table td .dest-recalculate').live('click', function() {
		var destTotal = calculateDestStockTotal();
		$("table tr #dest-total").text(destTotal);
		if (destTotal >= 0)
			$("table tr #dest-total").css("background-color", "#FFFF99");
		else
			$("table tr #dest-total").css("background-color", "#FFE9E8");
	});

	$('.stock-item-dropdown').trigger('change');
	$('table td .dest-recalculate').trigger('click');
	$('table td .source-recalculate').trigger('click');
	firstTime = false;
});

</script>

<?php
	echo form_open('inventory/transfer/edit/' . $current_voucher_type['label'] . '/' . $voucher_id);
	echo "<p>";
	echo "<span id=\"tooltip-target-1\">";
	echo form_label('Entry Number', 'voucher_number');
	echo " ";
	echo $current_voucher_type['prefix'] . form_input($voucher_number) . $current_voucher_type['suffix'];
	echo "</span>";
	echo "<span id=\"tooltip-content-1\">Leave Entry Number empty for auto numbering</span>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<span id=\"tooltip-target-2\">";
	echo form_label('Entry Date', 'voucher_date');
	echo " ";
	echo form_input_date_restrict($voucher_date);
	echo "</span>";
	echo "<span id=\"tooltip-content-2\">Date format is " . $this->config->item('account_date_format') . ".</span>";
	echo "</p>";

	echo "<h3>Source</h3>";
	echo "<table class=\"voucher-table\">";
	echo "<thead><tr><th>Inventory Item</th><th>Quantity</th><th>Rate Per Unit</th><th>Amount</th><th colspan=2></th><th colspan=2>Cur Balance</th></tr></thead>";

	foreach ($source_stock_item_id as $i => $row)
	{
		$source_stock_item_quantity_item = array(
			'name' => 'source_stock_item_quantity[' . $i . ']',
			'id' => 'source_stock_item_quantity[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($source_stock_item_quantity[$i]) ? $source_stock_item_quantity[$i] : '',
			'class' => 'source-quantity-stock-item',
		);
		$source_stock_item_rate_per_unit_item = array(
			'name' => 'source_stock_item_rate_per_unit[' . $i . ']',
			'id' => 'source_stock_item_rate_per_unit[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($source_stock_item_rate_per_unit[$i]) ? $source_stock_item_rate_per_unit[$i] : '',
			'class' => 'source-rate-stock-item',
		);
		$source_stock_item_amount_item = array(
			'name' => 'source_stock_item_amount[' . $i . ']',
			'id' => 'source_stock_item_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => isset($source_stock_item_amount[$i]) ? $source_stock_item_amount[$i] : '',
			'class' => 'source-amount-stock-item',
		);
		echo "<tr>";

		echo "<td>" . form_input_stock_item('source_stock_item_id[' . $i . ']', isset($source_stock_item_id[$i]) ? $source_stock_item_id[$i] : 0) . "</td>";
		echo "<td>" . form_input($source_stock_item_quantity_item) . "</td>";
		echo "<td>" . form_input($source_stock_item_rate_per_unit_item) . "</td>";
		echo "<td>" . form_input($source_stock_item_amount_item) . "</td>";

		echo "<td>" . img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addstockrow')) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deletestockrow')) . "</td>";

		echo "<td class=\"source-stock-item-balance\"><div></div></td>";

		echo "</tr>";
	}
	echo "<tr id=\"source-total\"><td colspan=3><strong>Total</strong></td><td id=\"source-total\">0</td><td>" . img(array('src' => asset_url() . "images/icons/gear.png", 'border' => '0', 'alt' => 'Recalculate Total', 'class' => 'source-recalculate', 'title' => 'Recalculate Total')) . "</td><td></td><td></td></tr>";
	echo "</table>";

	echo "<h3>Destination</h3>";
	echo "<table class=\"voucher-table\">";
	echo "<thead><tr><th>Inventory Item</th><th>Quantity</th><th>Rate Per Unit</th><th>Amount</th><th colspan=2></th><th colspan=2>Cur Balance</th></tr></thead>";

	foreach ($dest_stock_item_id as $i => $row)
	{
		$dest_stock_item_quantity_item = array(
			'name' => 'dest_stock_item_quantity[' . $i . ']',
			'id' => 'dest_stock_item_quantity[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($dest_stock_item_quantity[$i]) ? $dest_stock_item_quantity[$i] : '',
			'class' => 'dest-quantity-stock-item',
		);
		$dest_stock_item_rate_per_unit_item = array(
			'name' => 'dest_stock_item_rate_per_unit[' . $i . ']',
			'id' => 'dest_stock_item_rate_per_unit[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => isset($dest_stock_item_rate_per_unit[$i]) ? $dest_stock_item_rate_per_unit[$i] : '',
			'class' => 'dest-rate-stock-item',
		);
		$dest_stock_item_amount_item = array(
			'name' => 'dest_stock_item_amount[' . $i . ']',
			'id' => 'dest_stock_item_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => isset($dest_stock_item_amount[$i]) ? $dest_stock_item_amount[$i] : '',
			'class' => 'dest-amount-stock-item',
		);
		echo "<tr>";

		echo "<td>" . form_input_stock_item('dest_stock_item_id[' . $i . ']', isset($dest_stock_item_id[$i]) ? $dest_stock_item_id[$i] : 0) . "</td>";
		echo "<td>" . form_input($dest_stock_item_quantity_item) . "</td>";
		echo "<td>" . form_input($dest_stock_item_rate_per_unit_item) . "</td>";
		echo "<td>" . form_input($dest_stock_item_amount_item) . "</td>";

		echo "<td>" . img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addstockrow')) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deletestockrow')) . "</td>";

		echo "<td class=\"dest-stock-item-balance\"><div></div></td>";

		echo "</tr>";
	}
	echo "<tr id=\"dest-total\"><td colspan=3><strong>Total</strong></td><td id=\"dest-total\">0</td><td>" . img(array('src' => asset_url() . "images/icons/gear.png", 'border' => '0', 'alt' => 'Recalculate Total', 'class' => 'dest-recalculate', 'title' => 'Recalculate Total')) . "</td><td></td><td></td></tr>";
	echo "</table>";

	echo "<br />";
	echo "<br />";

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
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('voucher/show/' . $current_voucher_type['label'], 'Back', array('title' => 'Back to ' . $current_voucher_type['name'] . ' Entries'));
	echo "</p>";

	echo form_close();

