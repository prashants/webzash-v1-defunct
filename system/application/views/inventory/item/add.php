<script type="text/javascript">
$(document).ready(function() {

	/********************** STOCK ITEM TOTAL ******************************/
	$('#stock_item_op_quantity').live('change', function() {
		updateTotalValue();
	});

	$('#stock_item_op_rate_per_unit').live('change', function() {
		updateTotalValue();
	});

	var updateTotalValue = function() {
		var quantity = $('#stock_item_op_quantity').val();
		var rate_per_unit = $('#stock_item_op_rate_per_unit').val();

		quantity = parseFloat(quantity);
		rate_per_unit = parseFloat(rate_per_unit);

		if ((!isNaN(quantity)) && (!isNaN(rate_per_unit)))
		{
			/* calculating total amount */
			var total_value;
			total_value = quantity * rate_per_unit;

			/* displaying total amount for each stock item */
			$('#stock_item_op_total').val(total_value);
			$('#stock_item_op_total').fadeTo('slow', 0.1).fadeTo('slow', 1);
		}
	}
});
</script>

<?php
	echo form_open('inventory/stockitem/add');

	echo "<p>";
	echo form_label('Stock item name', 'stock_item_name');
	echo "<br />";
	echo form_input($stock_item_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Stock group', 'stock_item_group');
	echo "<br />";
	echo form_dropdown('stock_item_group', $stock_item_groups, $stock_item_group_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Stock unit', 'stock_item_unit');
	echo "<br />";
	echo form_dropdown('stock_item_unit', $stock_item_units, $stock_item_unit_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Costing method', 'stock_item_costing_method');
	echo "<br />";
	echo form_dropdown('stock_item_costing_method', $stock_item_costing_methods, $stock_item_costing_method_active);
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Opening Balance', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_label('Quantity', 'stock_item_op_quantity');
	echo "<br />";
	echo form_input($stock_item_op_quantity);
	echo "</p>";

	echo "<p>";
	echo form_label('Rate per unit', 'stock_item_op_rate_per_unit');
	echo "<br />";
	echo form_input($stock_item_op_rate_per_unit);
	echo "</p>";

	echo "<p>";
	echo form_label('Total value', 'stock_item_op_total');
	echo "<br />";
	echo form_input($stock_item_op_total);
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_label('Default selling price', 'stock_item_default_sell_price');
	echo "<br />";
	echo form_input($stock_item_default_sell_price);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('inventory/account', 'Back', array('title' => 'Back to Inventory'));
	echo "</p>";

	echo form_close();
