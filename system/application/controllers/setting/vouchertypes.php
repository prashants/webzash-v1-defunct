<?php

class VoucherTypes extends Controller {

	function VoucherTypes()
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
		$this->template->set('page_title', 'Voucher Types');
		$this->template->set('nav_links', array('setting/vouchertypes/add' => 'New Voucher Type'));

		$this->db->from('voucher_types')->order_by('id', 'asc');
		$data['voucher_type_data'] = $this->db->get();

		$this->template->load('template', 'setting/vouchertypes/index', $data);
		return;
	}
}

/* End of file vouchertypes.php */
/* Location: ./system/application/controllers/setting/vouchertypes.php */
