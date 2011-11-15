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
	echo form_open('group/edit/' . $group_id);

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
	echo "<span id=\"tooltip-target-1\">";
	echo form_checkbox('affects_gross', 1, $affects_gross) . " Affects Gross Profit/Loss Calculations";
	echo "</span>";
	echo "<span id=\"tooltip-content-1\">If selected the Group account will affect Gross Profit and Loss calculations, otherwise it will affect only Net Profit and Loss calculations.</span>";
	echo "</p>";

	echo "<p>";
	echo form_hidden('group_id', $group_id);
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('account', 'Back', 'Back to Chart of Accounts');
	echo "</p>";

	echo form_close();

