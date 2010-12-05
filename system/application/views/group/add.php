<script type="text/javascript">
$(document).ready(function() {
	/* Show and Hide affects_gross */
	$('.group-parent').change(function() {
		if ($(this).val() == "3" || $(this).val() == "4") {
			$('.affects-gross').show();
		} else {
			$('.affects-gross').hide();
		}
	});
	$('.group-parent').trigger('change');
});
</script>
<?php
	echo form_open('group/add');
	echo "<p>";
	echo form_label('Group name', 'group_name');
	echo "<br />";
	echo form_input($group_name);
	echo "</p>";
	echo "<p>";
	echo form_label('Parent group', 'group_parent');
	echo "<br />";
	echo form_dropdown('group_parent', $group_parent, $group_parent_active, "class = \"group-parent\"");
	echo "</p>";
	echo "<p class=\"affects-gross\">";
	echo form_checkbox('affects_gross', 1, $affects_gross) . " Affects Gross Profit/Loss Calculations";
	echo "</p>";
	echo form_submit('submit', 'Create');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo form_close();

