<?php
	switch ($voucher_type)
	{
	case 'receipt' :
		echo "<div class=\"voucher-add-links\">";
		echo anchor('voucher/add/receipt', 'Add Receipt Voucher', array('title' => 'Add Receipt Voucher'));
		echo "</div>";
		break;
	case 'payment' :
		echo "<div class=\"voucher-add-links\">";
		echo anchor('voucher/add/payment', 'Add Payment Voucher', array('title' => 'Add Payment Voucher'));
		echo "</div>";
		break;
	case 'contra' :
		echo "<div class=\"voucher-add-links\">";
		echo anchor('voucher/add/contra', 'Add Contra Voucher', array('title' => 'Add Contra Voucher'));
		echo "</div>";
		break;
	case 'journal' :
		echo "<div class=\"voucher-add-links\">";
		echo anchor('voucher/add/journal', 'Add Journal Voucher', array('title' => 'Add Journal Voucher'));
		echo "</div>";
		break;
	default :
		break;
	}
?>
<?php echo $voucher_table ?>

<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>

