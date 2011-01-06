<?php

class Setting extends Controller {

	function Setting()
	{
		parent::Controller();

		/* Check access */
		if ( ! check_access('administer'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'General Settings');

		/* Default settings */
		$data['row_count'] = 20;
		$data['log'] = 1;

		/* Loading settings from ini file */
		$ini_file = $this->config->item('config_path') . "settings/general.ini";

		/* Check if database ini file exists */
		if (get_file_info($ini_file))
		{
			/* Parsing database ini file */
			$cur_setting = parse_ini_file($ini_file);
			if ($cur_setting)
			{
				$data['row_count'] = isset($cur_setting['row_count']) ? $cur_setting['row_count'] : "20";
				$data['log'] = isset($cur_setting['log']) ? $cur_setting['log'] : "1";
			}
		}

		/* Form fields */
		$data['row_count_options'] = array(
			'10' => 10,
			'20' => 20,
			'50' => 50,
			'100' => 100,
			'200' => 200,
		);

		/* Form validations */
		$this->form_validation->set_rules('row_count', 'Row Count', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('log', 'Log Messages', 'trim');

		/* Repopulating form */
		if ($_POST)
		{
			$data['row_count'] = $this->input->post('row_count', TRUE);
			$data['log'] = $this->input->post('log', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('admin_template', 'admin/setting', $data);
			return;
		}
		else
		{
			$data_row_count = $this->input->post('row_count', TRUE);
			$data_log = $this->input->post('log', TRUE);

			if ($data_row_count < 0 || $data_row_count > 200)
			{
				$this->messages->add('Invalid number of rows.', 'error');
				$this->template->load('admin_template', 'admin/setting');
				return;
			}

			if ($data_log == 1)
				$data_log = 1;
			else
				$data_log = 0;

			$new_setting = "[general]" . "\r\n" . "row_count = \"" . $data_row_count . "\"" . "\r\n" . "log = \"" . $data_log . "\"" . "\r\n";

			$new_setting_html = '[general]<br />row_count = "' . $data_row_count . '"<br />' . "log = \"" . $data_log . "\"" . "<br />";

			/* Writing the connection string to end of file - writing in 'a' append mode */
			if ( ! write_file($ini_file, $new_setting))
			{
				$this->messages->add('Failed to update settings file. Check if "' . $ini_file . '" file is writable.', 'error');
				$this->messages->add('You can manually create a text file "' . $ini_file . '" with the following content :<br /><br />' . $new_setting_html, 'error');
				$this->template->load('admin_template', 'admin/setting', $data);
				return;
			} else {
				$this->messages->add('General settings updated.', 'success');
				redirect('admin/setting');
				return;
			}
		}
		return;
	}
}

/* End of file setting.php */
/* Location: ./system/application/controllers/admin/setting.php */
