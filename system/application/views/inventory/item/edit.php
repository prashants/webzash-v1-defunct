<script type="text/javascript">
$(document).ready(function() {

	/******************** INVENTORY ITEM TOTAL ****************************/
	$('#inventory_item_op_quantity').live('change', function() {
		updateTotalValue();
	});

	$('#inventory_item_op_rate_per_unit').live('change', function() {
		updateTotalValue();
	});

	var updateTotalValue = function() {
		var quantity = $('#inventory_item_op_quantity').val();
		var rate_per_unit = $('#inventory_item_op_rate_per_unit').val();

		quantity = parseFloat(quantity);
		rate_per_unit = parseFloat(rate_per_unit);

		if ((!isNaN(quantity)) && (!isNaN(rate_per_unit)))
		{
			/* calculating total amount */
			var total_value;
			total_value = quantity * rate_per_unit;

			/* displaying total amount for each inventory item */
			$('#inventory_item_op_total').val(total_value);
			$('#inventory_item_op_total').fadeTo('slow', 0.1).fadeTo('slow', 1);
		}
	}
});
</script>

<?php
	echo form_open('inventory/item/edit/' . $inventory_item_id);

	echo "<p>";
	echo form_label('Inventory item name', 'inventory_item_name');
	echo "<br />";
	echo form_input($inventory_item_name);
	echo "</p>";

	echo "<p>";
	echo form_label('Inventory group', 'inventory_item_group');
	echo "<br />";
	echo form_dropdown('inventory_item_group', $inventory_item_groups, $inventory_item_group_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Inventory unit', 'inventory_item_unit');
	echo "<br />";
	echo form_dropdown('inventory_item_unit', $inventory_item_units, $inventory_item_unit_active);
	echo "</p>";

	echo "<p>";
	echo form_label('Costing method', 'inventory_item_costing_method');
	echo "<br />";
	echo form_dropdown('inventory_item_costing_method', $inventory_item_costing_methods, $inventory_item_costing_method_active);
	echo "</p>";

	echo "<p>";
	echo form_fieldset('Opening Balance', array('class' => "fieldset-auto-width"));

	echo "<p>";
	echo form_label('Quantity', 'inventory_item_op_quantity');
	echo "<br />";
	echo form_input($inventory_item_op_quantity);
	echo "</p>";

	echo "<p>";
	echo form_label('Rate per unit', 'inventory_item_op_rate_per_unit');
	echo "<br />";
	echo form_input($inventory_item_op_rate_per_unit);
	echo "</p>";

	echo "<p>";
	echo form_label('Total value', 'inventory_item_op_total');
	echo "<br />";
	echo form_input($inventory_item_op_total);
	echo "</p>";

	echo form_fieldset_close();
	echo "</p>";

	echo "<p>";
	echo form_label('Default selling price', 'inventory_item_default_sell_price');
	echo "<br />";
	echo form_input($inventory_item_default_sell_price);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('inventory/account', 'Back', array('title' => 'Back to Inventory'));
	echo "</p>";

	echo form_close();
