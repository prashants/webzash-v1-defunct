<?php

class User extends Controller {
	function index()
	{
		redirect('user/login');
		return;
	}

	function login()
	{
		$this->template->set('page_title', 'Login');

		/* Form fields */
		$data['user_name'] = array(
			'name' => 'user_name',
			'id' => 'user_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['user_password'] = array(
			'name' => 'user_password',
			'id' => 'user_password',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		/* Form validations */
		$this->form_validation->set_rules('user_name', 'User name', 'trim|required|min_length[1]|max_length[100]');
		$this->form_validation->set_rules('user_password', 'Password', 'trim|required|min_length[1]|max_length[100]');

		/* Re-populating form */
		if ($_POST)
		{
			$data['user_name']['value'] = $this->input->post('user_name', TRUE);
			$data['user_password']['value'] = $this->input->post('user_password', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('user_template', 'user/login', $data);
			return;
		}
		else
		{
			$data_user_name = $this->input->post('user_name', TRUE);
			$data_user_password = $this->input->post('user_password', TRUE);

			/* Dummy accounts */
			if ($data_user_name == "admin" && $data_user_password = "admin")
			{
				$this->messages->add('Logged in as ' . 'admin' . '.', 'success');
				$this->session->set_userdata('user_name', 'admin');
				$this->session->set_userdata('user_role', 'administrator');
				redirect('');
				return;
			} else if ($data_user_name == "guest" && $data_user_password = "guest")
			{
				$this->messages->add('Logged in as ' . 'guest' . '.', 'success');
				$this->session->set_userdata('user_name', 'guest');
				$this->session->set_userdata('user_role', 'guest');
				redirect('');
				return;
			} else {
				$this->session->sess_destroy();
				$this->messages->add('Invalid User name or Password.', 'error');
				$this->template->load('user_template', 'user/login', $data);
				return;
			}
		}
	}

	function logout()
	{
		$this->session->sess_destroy();
		$this->messages->add('Logged out.', 'success');
		redirect('user/login');
	}
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */
