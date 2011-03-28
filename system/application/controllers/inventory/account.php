<?php

class Account extends Controller {
	function index()
	{
		$this->template->set('page_title', 'Inventory');
		$this->template->set('nav_links', array('inventory/group/add' => 'Add Inventory Group', 'inventory/item/add' => 'Add Inventory Item', 'inventory/unit/add' => 'Add Inventory Unit'));

		/* Stock Units */
		$this->db->from('inventory_units')->order_by('name', 'desc');
		$data['stock_units'] = $this->db->get();

		/* Stocks Tree */
		$this->load->library('inventorytree');
		$inventory_tree = new Inventorytree();
		$data['inventory_tree'] = $inventory_tree->init(0);

		$this->template->load('template', 'inventory/account/index', $data);
		return;
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/inventory/account.php */
