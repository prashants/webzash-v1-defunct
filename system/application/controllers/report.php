<?php

class Report extends Controller {
	var $acc_array;
	var $account_counter;
	function Report()
	{
		parent::Controller();
		$this->load->model('Ledger_model');
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Reports');
		$this->template->load('template', 'report/index');
		return;
	}

	function balancesheet($period = NULL)
	{
		$this->template->set('page_title', 'Balance Sheet');
		$this->template->load('template', 'report/balancesheet');
		return;
	}

	function profitandloss($period = NULL)
	{
		$this->template->set('page_title', 'Profit And Loss Statement');
		$this->template->load('template', 'report/profitandloss');
		return;
	}

	function trialbalance($period = NULL)
	{
		$this->template->set('page_title', 'Trial Balance');
		$this->template->set('nav_links', array('report/download/trialbalance' => 'Download CSV'));

		$this->load->library('accountlist');
		$this->template->load('template', 'report/trialbalance');
		return;
	}

	function ledgerst($ledger_id = 0)
	{
		/* Pagination setup */
		$this->load->library('pagination');

		$this->template->set('page_title', 'Ledger Statement');
		if ($_POST)
		{
			$ledger_id = $this->input->post('ledger_id', TRUE);
			redirect('report/ledgerst/' . $ledger_id);
		}
		$data['ledger_id'] = $ledger_id;
		$this->template->load('template', 'report/ledgerst', $data);
		return;
	}

	function download($statement, $id = NULL)
	{
		if ($statement == "trialbalance")
		{
			$this->load->model('Ledger_model');
			$all_ledgers = $this->Ledger_model->get_all_ledgers();
			$counter = 0;
			$trialbalance = array();
			$temp_dr_total = 0;
			$temp_cr_total = 0;

			$trialbalance[$counter][0]= "Ledger";
			$trialbalance[$counter][1]= "";
			$trialbalance[$counter][2]= "Opening";
			$trialbalance[$counter][3]= "";
			$trialbalance[$counter][4]= "Closing";
			$trialbalance[$counter][5]= "";
			$trialbalance[$counter][6]= "Dr Total";
			$trialbalance[$counter][7]= "";
			$trialbalance[$counter][8]= "Cr Total";
			$counter++;

			foreach ($all_ledgers as $ledger_id => $ledger_name)
			{
				if ($ledger_id == 0) continue;

				$trialbalance[$counter][0] = $ledger_name;

				list ($opbal_amount, $opbal_type) = $this->Ledger_model->get_op_balance($ledger_id);
				if ($opbal_amount == 0)
				{
					$trialbalance[$counter][1] = "";
					$trialbalance[$counter][2] = 0;
				} else {
					$trialbalance[$counter][1] = convert_dc($opbal_type);
					$trialbalance[$counter][2] = $opbal_amount;
				}

				$clbal_amount = $this->Ledger_model->get_ledger_balance($ledger_id);

				if ($clbal_amount == 0)
				{
					$trialbalance[$counter][3] = "";
					$trialbalance[$counter][4] = 0;
				} else if ($clbal_amount < 0) {
					$trialbalance[$counter][3] = "Cr";
					$trialbalance[$counter][4] = convert_cur(-$clbal_amount);
				} else {
					$trialbalance[$counter][3] = "Dr";
					$trialbalance[$counter][4] = convert_cur($clbal_amount);
				}

				$dr_total = $this->Ledger_model->get_dr_total($ledger_id);
				if ($dr_total)
				{
					$trialbalance[$counter][5] = "Dr";
					$trialbalance[$counter][6] = convert_cur($dr_total);
					$temp_dr_total += $dr_total;
				} else {
					$trialbalance[$counter][5] = "";
					$trialbalance[$counter][6] = 0;
				}

				$cr_total = $this->Ledger_model->get_cr_total($ledger_id);
				if ($cr_total)
				{
					$trialbalance[$counter][7] = "Cr";
					$trialbalance[$counter][8] = convert_cur($cr_total);
					$temp_cr_total += $cr_total;
				} else {
					$trialbalance[$counter][7] = "";
					$trialbalance[$counter][8] = 0;
				}
				$counter++;
			}

			$trialbalance[$counter][0]= "";
			$trialbalance[$counter][1]= "";
			$trialbalance[$counter][2]= "";
			$trialbalance[$counter][3]= "";
			$trialbalance[$counter][4]= "";
			$trialbalance[$counter][5]= "";
			$trialbalance[$counter][6]= "";
			$trialbalance[$counter][7]= "";
			$trialbalance[$counter][8]= "";
			$counter++;

			$trialbalance[$counter][0]= "Total";
			$trialbalance[$counter][1]= "";
			$trialbalance[$counter][2]= "";
			$trialbalance[$counter][3]= "";
			$trialbalance[$counter][4]= "";
			$trialbalance[$counter][5]= "Dr";
			$trialbalance[$counter][6]= convert_cur($temp_dr_total);
			$trialbalance[$counter][7]= "Cr";
			$trialbalance[$counter][8]= convert_cur($temp_cr_total);

			$this->load->helper('csv');
			echo array_to_csv($trialbalance, "trialbalance.csv");
			return;
		}
		return;
	}
}

/* End of file report.php */
/* Location: ./system/application/controllers/report.php */
