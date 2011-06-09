<?php
	$this->load->model('Ledger_model');
	if ( ! $print_preview)
	{
		echo form_open('report/reconciliation/' . $reconciliation_type . '/' . $ledger_id);
		echo "<p>";
		echo form_input_ledger('ledger_id', $ledger_id, '', $type = 'reconciliation');
		echo "</p>";
		echo "<p>";
		echo form_checkbox('show_all', 1, $show_all) . " Show All Vouchers";
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
			$config['total_rows'] = (int)$this->db->from('vouchers')->join('voucher_items', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->count_all_results();
		else
			$config['total_rows'] = (int)$this->db->from('vouchers')->join('voucher_items', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.reconciliation_date', NULL)->count_all_results();
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
		$this->db->select_sum('amount', 'drtotal')->from('voucher_items')->join('vouchers', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.dc', 'D')->where('voucher_items.reconciliation_date IS NOT NULL');
		$dr_total_q = $this->db->get();
		if ($dr_total = $dr_total_q->row())
			$reconciliation_dr_total = $dr_total->drtotal;
		else
			$reconciliation_dr_total = 0;

		/* Reconciliation Balance - Cr */
		$this->db->select_sum('amount', 'crtotal')->from('voucher_items')->join('vouchers', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.dc', 'C')->where('voucher_items.reconciliation_date IS NOT NULL');
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
			$this->db->select('vouchers.id as vid, vouchers.number as vnumber, vouchers.date as vdate, vouchers.narration as vnarration, vouchers.voucher_type as vtype, voucher_items.id as lid, voucher_items.amount as lamount, voucher_items.dc as ldc, voucher_items.reconciliation_date as lreconciliation');
			if ($reconciliation_type == 'all')
				$this->db->from('vouchers')->join('voucher_items', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->order_by('vouchers.date', 'asc')->order_by('vouchers.number', 'asc')->limit($pagination_counter, $page_count);
			else
				$this->db->from('vouchers')->join('voucher_items', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.reconciliation_date', NULL)->order_by('vouchers.date', 'asc')->order_by('vouchers.number', 'asc')->limit($pagination_counter, $page_count);
			$ledgerst_q = $this->db->get();
		} else {
			$page_count = 0;
			$this->db->select('vouchers.id as vid, vouchers.number as vnumber, vouchers.date as vdate, vouchers.narration as vnarration, vouchers.voucher_type as vtype, voucher_items.id as lid, voucher_items.amount as lamount, voucher_items.dc as ldc, voucher_items.reconciliation_date as lreconciliation');
			if ($reconciliation_type == 'all')
				$this->db->from('vouchers')->join('voucher_items', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->order_by('vouchers.date', 'asc')->order_by('vouchers.number', 'asc');
			else
				$this->db->from('vouchers')->join('voucher_items', 'vouchers.id = voucher_items.voucher_id')->where('voucher_items.ledger_id', $ledger_id)->where('voucher_items.reconciliation_date', NULL)->order_by('vouchers.date', 'asc')->order_by('vouchers.number', 'asc');
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
			$current_voucher_type = voucher_type_info($row->vtype);

			echo "<tr class=\"tr-" . $odd_even;
			if ($row->lreconciliation)
				echo " tr-reconciled";
			echo "\">";
			echo "<td>";
			echo date_mysql_to_php_display($row->vdate);
			echo "</td>";
			echo "<td>";
			echo anchor('voucher/view/' . $current_voucher_type['label'] . '/' . $row->vid, full_voucher_number($row->vtype, $row->vnumber), array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a'));
			echo "</td>";

			/* Getting opposite Ledger name */
			echo "<td>";
			echo $this->Ledger_model->get_opp_ledger_name($row->vid, $current_voucher_type['label'], $row->ldc, 'html');
			if ($row->vnarration)
				echo "<div class=\"small-font\">" . character_limiter($row->vnarration, 50) . "</div>";
			echo "</td>";

			echo "<td>";
			echo $current_voucher_type['name'];
			echo "</td>";
			if ($row->ldc == "D")
			{
				echo "<td>";
				echo convert_dc($row->ldc);
				echo " ";
				echo $row->lamount;
				echo "</td>";
				echo "<td></td>";
			} else {
				echo "<td></td>";
				echo "<td>";
				echo convert_dc($row->ldc);
				echo " ";
				echo $row->lamount;
				echo "</td>";
			}

			echo "<td>";
			if ( ! $print_preview)
			{
				$reconciliation_date = array(
					'name' => 'reconciliation_date[' . $row->lid . ']',
					'id' => 'reconciliation_date',
					'maxlength' => '11',
					'size' => '11',
					'value' => '',
				);
				if ($row->lreconciliation)
					$reconciliation_date['value'] = date_mysql_to_php($row->lreconciliation);
				echo form_input_date_restrict($reconciliation_date);
			} else {
				if ($row->lreconciliation)
					echo date_mysql_to_php($row->lreconciliation);
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
