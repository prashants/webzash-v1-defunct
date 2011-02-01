<?php

class Printer extends Controller {

	function Printer()
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
		$this->template->set('page_title', 'Printer Settings');
		$account_data = $this->Setting_model->get_current();

		/* Form fields */
		$data['paper_height'] = array(
			'name' => 'paper_height',
			'id' => 'paper_height',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['paper_width'] = array(
			'name' => 'paper_width',
			'id' => 'paper_width',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_top'] = array(
			'name' => 'margin_top',
			'id' => 'margin_top',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_bottom'] = array(
			'name' => 'margin_bottom',
			'id' => 'margin_bottom',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_left'] = array(
			'name' => 'margin_left',
			'id' => 'margin_left',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_right'] = array(
			'name' => 'margin_right',
			'id' => 'margin_right',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['orientation_potrait'] = array(
			'name' => 'orientation',
			'id' => 'orientation_potrait',
			'value' => 'P',
			'checked' => TRUE,
		);
		$data['orientation_landscape'] = array(
			'name' => 'orientation',
			'id' => 'orientation_landscape',
			'value' => 'L',
			'checked' => FALSE,
		);
		$data['output_format_html'] = array(
			'name' => 'output_format',
			'id' => 'output_format_html',
			'value' => 'H',
			'checked' => TRUE,
		);
		$data['output_format_text'] = array(
			'name' => 'output_format',
			'id' => 'output_format_text',
			'value' => 'T',
			'checked' => FALSE,
		);

		if ($account_data)
		{
			$data['paper_height']['value'] = ($account_data->print_paper_height) ? print_value($account_data->print_paper_height) : '';
			$data['paper_width']['value'] = ($account_data->print_paper_width) ? print_value($account_data->print_paper_width) : '';
			$data['margin_top']['value'] = ($account_data->print_margin_top) ? print_value($account_data->print_margin_top) : '';
			$data['margin_bottom']['value'] = ($account_data->print_margin_bottom) ? print_value($account_data->print_margin_bottom) : '';
			$data['margin_left']['value'] = ($account_data->print_margin_left) ? print_value($account_data->print_margin_left) : '';
			$data['margin_right']['value'] = ($account_data->print_margin_right) ? print_value($account_data->print_margin_right) : '';
			if ($account_data->print_orientation)
			{
				if ($account_data->print_orientation == "P")
				{
					$data['orientation_potrait']['checked'] = TRUE;
					$data['orientation_landscape']['checked'] = FALSE;
				} else {
					$data['orientation_potrait']['checked'] = FALSE;
					$data['orientation_landscape']['checked'] = TRUE;
				}
			}
			if ($account_data->print_page_format)
			{
				if ($account_data->print_page_format == "H")
				{
					$data['output_format_html']['checked'] = TRUE;
					$data['output_format_text']['checked'] = FALSE;
				} else {
					$data['output_format_html']['checked'] = FALSE;
					$data['output_format_text']['checked'] = TRUE;
				}
			}
		}

		/* Form validations */
		$this->form_validation->set_rules('paper_height', 'Paper Height', 'trim|required|numeric');
		$this->form_validation->set_rules('paper_width', 'Paper Width', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_top', 'Top Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_bottom', 'Bottom Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_left', 'Left Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_right', 'Right Margin', 'trim|required|numeric');

		/* Repopulating form */
		if ($_POST)
		{
			$data['paper_height']['value'] = $this->input->post('paper_height', TRUE);
			$data['paper_width']['value'] = $this->input->post('paper_width', TRUE);
			$data['margin_top']['value'] = $this->input->post('margin_top', TRUE);
			$data['margin_bottom']['value'] = $this->input->post('margin_bottom', TRUE);
			$data['margin_left']['value'] = $this->input->post('margin_left', TRUE);
			$data['margin_right']['value'] = $this->input->post('margin_right', TRUE);

			$data['orientation'] = $this->input->post('orientation', TRUE);
			if ($data['orientation'] == "P")
			{
				$data['orientation_potrait']['checked'] = TRUE;
				$data['orientation_landscape']['checked'] = FALSE;
			} else {
				$data['orientation_potrait']['checked'] = FALSE;
				$data['orientation_landscape']['checked'] = TRUE;
			}
			$data['output_format'] = $this->input->post('output_format', TRUE);
			if ($data['output_format'] == "H")
			{
				$data['output_format_html']['checked'] = TRUE;
				$data['output_format_text']['checked'] = FALSE;
			} else {
				$data['output_format_html']['checked'] = FALSE;
				$data['output_format_text']['checked'] = TRUE;
			}
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/printer', $data);
			return;
		}
		else
		{
			$data_paper_height = $this->input->post('paper_height', TRUE);
			$data_paper_width = $this->input->post('paper_width', TRUE);
			$data_margin_top = $this->input->post('margin_top', TRUE);
			$data_margin_bottom = $this->input->post('margin_bottom', TRUE);
			$data_margin_left = $this->input->post('margin_left', TRUE);
			$data_margin_right = $this->input->post('margin_right', TRUE);

			if ($this->input->post('orientation', TRUE) == "P")
			{
				$data_orientation = "P";
			} else {
				$data_orientation = "L";
			}
			if ($this->input->post('output_format', TRUE) == "H")
			{
				$data_output_format = "H";
			} else {
				$data_output_format = "T";
			}

			/* Update settings */
			$this->db->trans_start();
			$update_data = array(
				'print_paper_height' => $data_paper_height,
				'print_paper_width' => $data_paper_width,
				'print_margin_top' => $data_margin_top,
				'print_margin_bottom' => $data_margin_bottom,
				'print_margin_left' => $data_margin_left,
				'print_margin_right' => $data_margin_right,
				'print_orientation' => $data_orientation,
				'print_page_format' => $data_output_format,
			);
			if ( ! $this->db->where('id', 1)->update('settings', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating printer settings.', 'error');
				$this->logger->write_message("error", "Error updating printer settings");
				$this->template->load('template', 'setting/printer');
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Printer settings updated.', 'success');
				$this->logger->write_message("success", "Updated printer settings");
				redirect('setting');
				return;
			}
		}
		return;
	}
}

/* End of file printer.php */
/* Location: ./system/application/controllers/setting/printer.php */
