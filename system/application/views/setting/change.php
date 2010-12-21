<?php
	/* Getting list of files in the config - accounts directory */
	$accounts_list = get_filenames($this->config->item('config_path') . 'accounts');
	$select_account_options = array();
	if ($accounts_list)
	{
		foreach ($accounts_list as $row)
		{
			/* Only include file ending with .ini */
			if (substr($row, -4) == ".ini")
			{
				$ini_label = substr($row, 0, -4);
				$select_account_options[$ini_label] = $ini_label;
			}
		}
	}

	echo form_open('setting/change');

	echo "<p>";
	echo form_label('Select account', 'select_account');
	echo "<br />";
	echo form_dropdown('select_account', $select_account_options);
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Change');
	echo "</p>";
	echo "<p>";
	echo anchor('admin/manage', 'Manage Accounts', 'Manage Accounts');
	echo "</p>";

	echo form_close();

