<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Print - <?php echo ucfirst($voucher_type); ?> Voucher Number <?php echo $voucher_number; ?></title>
<?php echo link_tag(asset_url() . 'images/favicon.ico', 'shortcut icon', 'image/ico'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/printvoucher.css">
</head>
<body>
	<div id="print-html-link"><a href="JavaScript:window.print();">Print HTML</a></div>
	<div id="print-voucher-type"><span class="value"><?php echo ucfirst($voucher_type); ?> Voucher</span></div>
	<div id="print-account-name"><span class="value"><?php echo  $this->config->item('account_name'); ?></span></div>
	<div id="print-account-address"><span class="value"><?php echo $this->config->item('account_address'); ?></span></div>
	<div id="print-voucher-number">Voucher Number : <span class="value"><?php echo $voucher_number; ?></span></div>
	<div id="print-voucher-number">Voucher Date : <span class="value"><?php echo $voucher_date; ?></span></div>
	<table id="print-voucher-table">
		<thead>
			<tr><th>Ledger A/C</th><th>Dr Amount</th><th>Cr Amount</th></tr>
		</thead>
		<tbody>
		<?php
			foreach ($ledger_data as $id => $row)
			{
				echo "<tr>";
				echo "<td>" . $row['name'] . "</td>";
				if ($row['dc'] == "D")
				{
					echo "<td>Dr " . $row['amount'] . "</td>";
					echo "<td></td>";
				} else {
					echo "<td></td>";
					echo "<td>Cr " . $row['amount'] . "</td>";
				}
				echo "</tr>";
			}
			echo "<tr><td>Total</td><td>Dr " . $voucher_dr_total . "</td><td>Cr " . $voucher_cr_total . "</td></tr>";
		?>
		</tbody>
	</table>
	<div id="print-voucher-narration">Narration : <span class="value"><?php echo $voucher_narration; ?></span></div>
</body>
</html>
