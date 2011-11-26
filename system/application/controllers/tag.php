<?php

class Tag extends Controller {

	function Tag()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->load->model('Tag_model');
		$this->template->set('page_title', 'Tags');
		$this->template->set('nav_links', array('tag/add' => 'New Tag'));
		$this->template->load('template', 'tag/index');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'New Tag');

		/* Check access */
		if ( ! check_access('create tag'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('tag');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('tag');
			return;
		}

		/* Colorpicker JS and CSS */
		$this->template->set('add_css', array(
			"plugins/colorpicker/css/colorpicker.css",
		));

		$this->template->set('add_javascript', array(
			"plugins/colorpicker/js/colorpicker.js",
			"plugins/colorpicker/js/eye.js",
			"plugins/colorpicker/js/utils.js",
			"plugins/colorpicker/js/layout.js",
			"plugins/colorpicker/js/startup.js",
		));

		/* Form fields */
		$data['tag_title'] = array(
			'name' => 'tag_title',
			'id' => 'tag_title',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$data['tag_color'] = array(
			'name' => 'tag_color',
			'id' => 'tag_color',
			'maxlength' => '6',
			'size' => '6',
			'value' => '',
		);
		$data['tag_background'] = array(
			'name' => 'tag_background',
			'id' => 'tag_background',
			'maxlength' => '6',
			'size' => '6',
			'value' => '',
		);

		/* Form validations */
		$this->form_validation->set_rules('tag_title', 'Tag title', 'trim|required|min_length[2]|max_length[15]|unique[tags.title]');
		$this->form_validation->set_rules('tag_color', 'Tag color', 'trim|required|exact_length[6]|is_hex');
		$this->form_validation->set_rules('tag_background', 'Background color', 'trim|required|exact_length[6]|is_hex');

		/* Re-populating form */
		if ($_POST)
		{
			$data['tag_title']['value'] = $this->input->post('tag_title', TRUE);
			$data['tag_color']['value'] = $this->input->post('tag_color', TRUE);
			$data['tag_background']['value'] = $this->input->post('tag_background', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'tag/add', $data);
			return;
		}
		else
		{
			$data_tag_title = $this->input->post('tag_title', TRUE);
			$data_tag_color = $this->input->post('tag_color', TRUE);
			$data_tag_color = strtoupper($data_tag_color);
			$data_tag_background = $this->input->post('tag_background', TRUE);
			$data_tag_background = strtoupper($data_tag_background);

			$this->db->trans_start();
			$insert_data = array(
				'title' => $data_tag_title,
				'color' => $data_tag_color,
				'background' => $data_tag_background,
			);
			if ( ! $this->db->insert('tags', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Tag - ' . $data_tag_title . '.', 'error');
				$this->logger->write_message("error", "Error adding tag called " . $data_tag_title);
				$this->template->load('template', 'tag/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Tag - ' . $data_tag_title . '.', 'success');
				$this->logger->write_message("success", "Added tag called " . $data_tag_title);
				redirect('tag');
				return;
			}
		}
		return;

	}

	function edit($id = 0)
	{
		$this->template->set('page_title', 'Edit Tag');

		/* Check access */
		if ( ! check_access('edit tag'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('tag');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('tag');
			return;
		}

		/* Colorpicker JS and CSS */
		$this->template->set('add_css', array(
			"plugins/colorpicker/css/colorpicker.css",
		));

		$this->template->set('add_javascript', array(
			"plugins/colorpicker/js/colorpicker.js",
			"plugins/colorpicker/js/eye.js",
			"plugins/colorpicker/js/utils.js",
			"plugins/colorpicker/js/layout.js",
			"plugins/colorpicker/js/startup.js",
		));

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Tag.', 'error');
			redirect('tag');
			return;
		}

		/* Loading current group */
		$this->db->from('tags')->where('id', $id);
		$tag_data_q = $this->db->get();
		if ($tag_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Tag.', 'error');
			redirect('tag');
			return;
		}
		$tag_data = $tag_data_q->row();

		/* Form fields */
		$data['tag_title'] = array(
			'name' => 'tag_title',
			'id' => 'tag_title',
			'maxlength' => '15',
			'size' => '15',
			'value' => $tag_data->title,
		);
		$data['tag_color'] = array(
			'name' => 'tag_color',
			'id' => 'tag_color',
			'maxlength' => '6',
			'size' => '6',
			'value' => $tag_data->color,
		);
		$data['tag_background'] = array(
			'name' => 'tag_background',
			'id' => 'tag_background',
			'maxlength' => '6',
			'size' => '6',
			'value' => $tag_data->background,
		);
		$data['tag_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('tag_title', 'Tag title', 'trim|required|min_length[2]|max_length[15]|uniquewithid[tags.title.' . $id . ']');
		$this->form_validation->set_rules('tag_color', 'Tag color', 'trim|required|exact_length[6]|is_hex');
		$this->form_validation->set_rules('tag_background', 'Background color', 'trim|required|exact_length[6]|is_hex');

		/* Re-populating form */
		if ($_POST)
		{
			$data['tag_title']['value'] = $this->input->post('tag_title', TRUE);
			$data['tag_color']['value'] = $this->input->post('tag_color', TRUE);
			$data['tag_background']['value'] = $this->input->post('tag_background', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'tag/edit', $data);
			return;
		}
		else
		{
			$data_tag_title = $this->input->post('tag_title', TRUE);
			$data_tag_color = $this->input->post('tag_color', TRUE);
			$data_tag_color = strtoupper($data_tag_color);
			$data_tag_background = $this->input->post('tag_background', TRUE);
			$data_tag_background = strtoupper($data_tag_background);

			$this->db->trans_start();
			$update_data = array(
				'title' => $data_tag_title,
				'color' => $data_tag_color,
				'background' => $data_tag_background,
			);
			if ( ! $this->db->where('id', $id)->update('tags', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Tag - ' . $data_tag_title . '.', 'error');
				$this->logger->write_message("error", "Error updating tag called " . $data_tag_title . " [id:" . $id . "]");
				$this->template->load('template', 'tag/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Tag - ' . $data_tag_title . '.', 'success');
				$this->logger->write_message("success", "Updated tag called " . $data_tag_title . " [id:" . $id . "]");
				redirect('tag');
				return;
			}
		}
		return;

	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete tag'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('tag');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('tag');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Tag.', 'error');
			redirect('tag');
			return;
		}
		$this->db->from('tags')->where('id', $id);
		$data_valid_q = $this->db->get();
		if ($data_valid_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Tag.', 'error');
			redirect('tag');
			return;
		}
		$data_tag = $data_valid_q->row();

		/* Deleting Tag */
		$this->db->trans_start();
		$update_data = array(
			'tag_id' => NULL,
		);
		if ( !  $this->db->where('tag_id', $id)->update('entries', $update_data))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Tag from Entries.', 'error');
			$this->logger->write_message("error", "Error deleting tag called " . $data_tag->title . " [id:" . $id . "] from entries");
			redirect('tag');
			return;
		} else {
			if ( ! $this->db->delete('tags', array('id' => $id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting Tag.', 'error');
				$this->logger->write_message("error", "Error deleting tag called " . $data_tag->title . " [id:" . $id . "]");
				redirect('tag');
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Tag deleted.', 'success');
				$this->logger->write_message("success", "Deleted tag called " . $data_tag->title . " [id:" . $id . "]");
				redirect('tag');
				return;
			}
		}
		return;
	}

}

/* End of file tag.php */
/* Location: ./system/application/controllers/tag.php */
