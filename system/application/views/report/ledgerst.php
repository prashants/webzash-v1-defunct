<?php
$this->load->model('Ledger_model');
echo form_open('report/ledgerst/' . $ledger_id);
echo "<p>";
echo form_input_ledger('ledger_id', $ledger_id);
echo " ";
echo form_submit('submit', 'Show');
echo "</p>";
echo form_close();
?>
