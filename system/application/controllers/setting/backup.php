<?php

class Backup extends Controller {

	function Backup()
	{
		parent::Controller();
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('change account settings'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}

	function index()
	{
		$this->load->dbutil();
		$this->load->helper('download');

		/* Check access */
		if ( ! check_access('backup account'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('setting');
			return;
		}

		$backup_filename = "backup" . date("dmYHis") . ".gz";

		/* Backup your entire database and assign it to a variable */
		$backup_data =& $this->dbutil->backup();

		/* Write the backup file to server */
		if ( ! write_file($this->config->item('backup_path') . $backup_filename, $backup_data))
		{
			$this->messages->add('Error saving backup file to server.' . ' Check if "' . $this->config->item('backup_path') . '" folder is writable.', 'error');
			redirect('setting');
			return;
		}

		/* Send the file to your desktop */
		force_download($backup_filename, $backup_data);
		$this->logger->write_message("success", "Downloaded account backup");
		redirect('setting');
		return;
	}
}

/* End of file backup.php */
/* Location: ./system/application/controllers/setting/backup.php */
