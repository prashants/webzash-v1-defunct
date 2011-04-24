<?php
	if ( ! $print_preview)
	{
		echo form_open('report/inventory_statement/' . $inventory_item_id);
		echo "<p>";
		echo form_input_inventory_item('inventory_item_id', $inventory_item_id);
		echo " ";
		echo form_submit('submit', 'Show');
		echo "</p>";
		echo form_close();
	}

	/* Pagination configuration */
	if ( ! $print_preview)
	{
		$pagination_counter = $this->config->item('row_count');
		$page_count = (int)$this->uri->segment(4);
		$page_count = $this->input->xss_clean($page_count);
		if ( ! $page_count)
			$page_count = "0";
		$config['base_url'] = site_url('report/inventory_statement/' . $inventory_item_id);
		$config['num_links'] = 10;
		$config['per_page'] = $pagination_counter;
		$config['uri_segment'] = 4;
		$config['total_rows'] = (int)$this->db->from('inventory_entry_items')->where('inventory_item_id', $inventory_item_id)->count_all_results();
		$config['full_tag_open'] = '<ul id="pagination-flickr">';
		$config['full_close_open'] = '</ul>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		$config['next_link'] = 'Next &#187;';
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&#171; Previous';
		$config['prev_tag_open'] = '<li class="previous">';
		$config['prev_tag_close'] = '</li>';
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li class="first">';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li class="last">';
		$config['last_tag_close'] = '</li>';
		$this->pagination->initialize($config);
	}

	if ($inventory_item_id != 0)
	{
		$op_quantity = $inventory_item_data->op_balance_quantity;
		$op_rate = $inventory_item_data->op_balance_rate_per_unit;
		$op_value = $inventory_item_data->op_balance_total_value;

		$cur_quantity = $op_quantity;
		$cur_rate = $op_rate;
		$cur_value = $op_value;

		list($cl_quantity, $cl_rate, $cl_value) = $this->Inventory_item_model->closing_inventory($inventory_item_id); /* Final Closing Balance */

		/* Inventory Summary */
		echo "<table class=\"ledger-summary\">";
		echo "<tr>";
		echo "<td></td><td><b>Quantity</b></td><td><b>Rate</b></td><td><b>Value</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><b>Opening</b></td><td>" . $op_quantity . "</td><td>" . convert_cur($op_rate) . "</td><td>" . convert_cur($op_value) . "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><b>Closing</b></td><td>" . $cl_quantity . "</td><td>" . convert_cur($cl_rate) . "</td><td>" . convert_cur($cl_value) . "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<br />";

		if ( ! $print_preview) {
			$this->db->from('inventory_entry_items')->join('entries', 'inventory_entry_items.entry_id = entries.id', 'left')->where('inventory_entry_items.inventory_item_id', $inventory_item_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc')->limit($pagination_counter, $page_count);
			$inventory_entry_items_q = $this->db->get();
		} else {
			$page_count = 0;
			$this->db->from('inventory_entry_items')->join('entries', 'inventory_entry_items.entry_id = entries.id', 'left')->where('inventory_entry_items.inventory_item_id', $inventory_item_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc');
			$inventory_entry_items_q = $this->db->get();
		}

		echo "<table border=0 cellpadding=5 class=\"simple-table ledgerst-table\">";

		echo "<thead><tr><th>Date</th><th>No.</th><th>Ledger Name</th><th>Type</th><th>In Quantity</th><th>In Rate</th><th>In Value</th><th>Out Quantity</th><th>Out Rate</th><th>Out Value</th></tr></thead>";
		$odd_even = "odd";

		if ($page_count <= 0)
		{
			/* Show opening balance */
			echo "<tr class=\"tr-balance\"><td colspan=4>Opening Inventory</td><td>" . $op_quantity . "</td><td>" . convert_cur($op_rate) . "</td><td>" . convert_cur($op_value) . "</td><td></td><td></td><td></td></tr>";
		} else {

			/* Opening balance */
			$cur_quantity = $op_quantity;
			$cur_rate = $op_rate;
			$cur_value = $op_value;

			/* Calculating previous balance */
			$this->db->select('entries.id as entries_id, entries.number as entries_number, entries.date as entries_date, entries.entry_type as entries_entry_type');
			$this->db->select('inventory_entry_items.id as inventory_entry_items_id, inventory_entry_items.type as inventory_entry_items_type, inventory_entry_items.quantity as inventory_entry_items_quantity, inventory_entry_items.rate_per_unit as inventory_entry_items_rate_per_unit, inventory_entry_items.total as inventory_entry_items_total');
			$this->db->from('inventory_entry_items')->join('entries', 'inventory_entry_items.entry_id = entries.id', 'left')->where('inventory_entry_items.inventory_item_id', $inventory_item_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc')->limit($page_count, 0);
			$previous_q = $this->db->get();
			foreach ($previous_q->result() as $row)
			{
				if ($row->inventory_entry_items_type == 1)
				{
					$cur_quantity += $row->inventory_entry_items_quantity;
					$cur_rate += $row->inventory_entry_items_rate_per_unit;
					$cur_value += $row->inventory_entry_items_total;
				} else {
					$cur_quantity -= $row->inventory_entry_items_quantity;
					$cur_rate -= $row->inventory_entry_items_rate_per_unit;
					$cur_value -= $row->inventory_entry_items_total;
				}
			}
			/* Show relative opening balance */
			echo "<tr class=\"tr-balance\"><td colspan=4>Opening Inventory</td><td>" . $cur_quantity . "</td><td>" . convert_cur($cur_rate) . "</td><td>" . convert_cur($cur_value) . "</td><td></td><td></td><td></td></tr>";
		}

		foreach ($inventory_entry_items_q->result() as $row)
		{
		  $this->db->from('entries')->where('id', $row->entry_id)->limit(1);
		  $entries_q = $this->db->get();
		  $entries_data = $entries_q->row();

			$current_entry_type = entry_type_info($entries_data->entry_type);

			echo "<tr class=\"tr-" . $odd_even . "\">";
			echo "<td>";
			echo date_mysql_to_php_display($entries_data->date);
			echo "</td>";
			echo "<td>";
			if ($current_entry_type['inventory_entry_type'] == 3)
				echo anchor('inventory/transfer/view/' . $current_entry_type['label'] . '/' . $entries_data->id, full_entry_number($entries_data->entry_type, $entries_data->number), array('title' => 'View ' . ' Entry', 'class' => 'anchor-link-a'));
			else
				echo anchor('inventory/entry/view/' . $current_entry_type['label'] . '/' . $entries_data->id, full_entry_number($entries_data->entry_type, $entries_data->number), array('title' => 'View ' . ' Entry', 'class' => 'anchor-link-a'));
			echo "</td>";

			/* Getting opposite Ledger name */
			echo "<td>";
			echo $this->Ledger_model->get_entry_name($entries_data->id, $entries_data->entry_type);
			echo "</td>";

			echo "<td>";
			echo $current_entry_type['name'];
			echo "</td>";
			if ($row->type == 1)
			{
				$cur_quantity += $row->quantity;
				$cur_rate += $row->rate_per_unit;
				$cur_value += $row->total;
				echo "<td>" . $row->quantity . "</td>";
				echo "<td>" . convert_cur($row->rate_per_unit). "</td>";
				echo "<td>" . convert_cur($row->total) . "</td>";
				echo "<td></td><td></td><td></td>";
			} else if ($row->type == 2) {
				$cur_quantity -= $row->quantity;
				$cur_rate -= $row->rate_per_unit;
				$cur_value -= $row->total;
				echo "<td></td><td></td><td></td>";
				echo "<td>" . $row->quantity . "</td>";
				echo "<td>" . convert_cur($row->rate_per_unit). "</td>";
				echo "<td>" . convert_cur($row->total) . "</td>";
			}
			echo "</tr>";
			$odd_even = ($odd_even == "odd") ? "even" : "odd";
		}

		/* Current Page Closing Balance */
		echo "<tr class=\"tr-balance\"><td colspan=7>Closing Inventory</td><td>" . $cur_quantity . "</td><td>" . convert_cur($cur_rate) . "</td><td>" . convert_cur($cur_value) . "</td>";
		echo "</table>";
	}
?>
<?php if ( ! $print_preview) { ?>
<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>
<?php } ?>
