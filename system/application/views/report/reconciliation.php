<?php
	$this->load->model('Ledger_model');
	if ( ! $print_preview)
	{
		echo form_open('report/reconciliation/' . $reconciliation_type . '/' . $ledger_id);
		echo "<p>";
		echo form_input_ledger('ledger_id', $ledger_id, '', $type = 'reconciliation');
		echo "</p>";
		echo "<p>";
		echo form_checkbox('show_all', 1, $show_all) . " Show All Entries";
		echo "</p>";
		echo "<p>";
		echo form_submit('submit', 'Submit');
		echo "</p>";
		echo form_close();
	}

	/* Pagination configuration */
	if ( ! $print_preview)
	{
		$pagination_counter = $this->config->item('row_count');
		$page_count = (int)$this->uri->segment(5);
		$page_count = $this->input->xss_clean($page_count);
		if ( ! $page_count)
			$page_count = "0";
		$config['base_url'] = site_url('report/reconciliation/' . $reconciliation_type . '/' . $ledger_id);
		$config['num_links'] = 10;
		$config['per_page'] = $pagination_counter;
		$config['uri_segment'] = 5;
		if ($reconciliation_type == 'all')
			$config['total_rows'] = (int)$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->count_all_results();
		else
			$config['total_rows'] = (int)$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.reconciliation_date', NULL)->count_all_results();
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

	if ($ledger_id != 0)
	{
		list ($opbalance, $optype) = $this->Ledger_model->get_op_balance($ledger_id); /* Opening Balance */
		$clbalance = $this->Ledger_model->get_ledger_balance($ledger_id); /* Final Closing Balance */

		/* Reconciliation Balance - Dr */
		$this->db->select_sum('amount', 'drtotal')->from('entry_items')->join('entries', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.dc', 'D')->where('entry_items.reconciliation_date IS NOT NULL');
		$dr_total_q = $this->db->get();
		if ($dr_total = $dr_total_q->row())
			$reconciliation_dr_total = $dr_total->drtotal;
		else
			$reconciliation_dr_total = 0;

		/* Reconciliation Balance - Cr */
		$this->db->select_sum('amount', 'crtotal')->from('entry_items')->join('entries', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.dc', 'C')->where('entry_items.reconciliation_date IS NOT NULL');
		$cr_total_q = $this->db->get();
		if ($cr_total = $cr_total_q->row())
			$reconciliation_cr_total = $cr_total->crtotal;
		else
			$reconciliation_cr_total = 0;

		$reconciliation_total = float_ops($reconciliation_dr_total, $reconciliation_cr_total, '-');
		$reconciliation_pending = float_ops($clbalance, $reconciliation_total, '-');

		/* Ledger and Reconciliation Summary */
		echo "<table class=\"reconciliation-summary\">";
		echo "<tr>";
		echo "<td><b>Opening Balance</b></td><td>" . convert_opening($opbalance, $optype) . "</td>";
		echo "<td width=\"20px\"></td>";
		echo "<td><b>Reconciliation Pending</b></td><td>" . convert_amount_dc($reconciliation_pending) . "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><b>Closing Balance</b></td><td>" . convert_amount_dc($clbalance) . "</td>";
		echo "<td width=\"20px\"></td>";
		echo "<td><b>Reconciliation Total</b></td><td>" . convert_amount_dc($reconciliation_total) . "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<br />";
		if ( ! $print_preview)
		{
			$this->db->select('entries.id as entries_id, entries.number as entries_number, entries.date as entries_date, entries.narration as entries_narration, entries.entry_type as entries_entry_type, entry_items.id as entry_items_id, entry_items.amount as entry_items_amount, entry_items.dc as entry_items_dc, entry_items.reconciliation_date as entry_items_reconciliation_date');
			if ($reconciliation_type == 'all')
				$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc')->limit($pagination_counter, $page_count);
			else
				$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.reconciliation_date', NULL)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc')->limit($pagination_counter, $page_count);
			$ledgerst_q = $this->db->get();
		} else {
			$page_count = 0;
			$this->db->select('entries.id as entries_id, entries.number as entries_number, entries.date as entries_date, entries.narration as entries_narration, entries.entry_type as entries_entry_type, entry_items.id as entry_items_id, entry_items.amount as entry_items_amount, entry_items.dc as entry_items_dc, entry_items.reconciliation_date as entry_items_reconciliation_date');
			if ($reconciliation_type == 'all')
				$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc');
			else
				$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.reconciliation_date', NULL)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc');
			$ledgerst_q = $this->db->get();
		}

		if ( ! $print_preview)
		{
			echo form_open('report/reconciliation/' . $reconciliation_type . '/' . $ledger_id . "/" . $page_count);
		}
		echo "<table border=0 cellpadding=5 class=\"simple-table reconciliation-table\">";

		echo "<thead><tr><th>Date</th><th>No.</th><th>Ledger Name</th><th>Type</th><th>Dr Amount</th><th>Cr Amount</th><th>Reconciliation Date</th></tr></thead>";
		$odd_even = "odd";

		foreach ($ledgerst_q->result() as $row)
		{
			$current_entry_type = entry_type_info($row->entries_entry_type);

			echo "<tr class=\"tr-" . $odd_even;
			if ($row->entry_items_reconciliation_date)
				echo " tr-reconciled";
			echo "\">";
			echo "<td>";
			echo date_mysql_to_php_display($row->entries_date);
			echo "</td>";
			echo "<td>";
			echo anchor('entry/view/' . $current_entry_type['label'] . '/' . $row->entries_id, full_entry_number($row->entries_entry_type, $row->entries_number), array('title' => 'View ' . $current_entry_type['name'] . ' Entry', 'class' => 'anchor-link-a'));
			echo "</td>";

			/* Getting opposite Ledger name */
			echo "<td>";
			echo $this->Ledger_model->get_opp_ledger_name($row->entries_id, $current_entry_type['label'], $row->entry_items_dc, 'html');
			if ($row->entries_narration)
				echo "<div class=\"small-font\">" . character_limiter($row->entries_narration, 50) . "</div>";
			echo "</td>";

			echo "<td>";
			echo $current_entry_type['name'];
			echo "</td>";
			if ($row->entry_items_dc == "D")
			{
				echo "<td>";
				echo convert_dc($row->entry_items_dc);
				echo " ";
				echo $row->entry_items_amount;
				echo "</td>";
				echo "<td></td>";
			} else {
				echo "<td></td>";
				echo "<td>";
				echo convert_dc($row->entry_items_dc);
				echo " ";
				echo $row->entry_items_amount;
				echo "</td>";
			}

			echo "<td>";
			if ( ! $print_preview)
			{
				$reconciliation_date = array(
					'name' => 'reconciliation_date[' . $row->entry_items_id . ']',
					'id' => 'reconciliation_date',
					'maxlength' => '11',
					'size' => '11',
					'value' => '',
				);
				if ($row->entry_items_reconciliation_date)
					$reconciliation_date['value'] = date_mysql_to_php($row->entry_items_reconciliation_date);
				echo form_input_date_restrict($reconciliation_date);
			} else {
				if ($row->entry_items_reconciliation_date)
					echo date_mysql_to_php($row->entry_items_reconciliation_date);
				else
					echo "-";
			}
			echo "</td>";
			echo "</tr>";
			$odd_even = ($odd_even == "odd") ? "even" : "odd";
		}

		echo "</table>";
		if ( ! $print_preview)
		{
			echo "<p>";
			echo form_submit('submit', 'Update');
			echo "</p>";
			echo form_close();
		}
	}
?>
<?php if ( ! $print_preview) { ?>
<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>
<?php } ?>
