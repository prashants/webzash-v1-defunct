<?php

class Account extends Controller {
	function index()
	{
		$this->template->set('page_title', 'Inventory');
		$this->template->set('nav_links', array('inventory/stockgroup/add' => 'New Stock Group', 'inventory/stockitem/add' => 'New Stock Item', 'inventory/stockunit/add' => 'New Stock Unit'));

		/* Stock Units */
		$this->db->from('stock_units')->order_by('name', 'desc');
		$data['stock_units'] = $this->db->get();

		$this->template->load('template', 'inventory/account/index', $data);
		return;
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/inventory/account.php */
