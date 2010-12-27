<div id="tag-sidebar">
	<?php $this->load->view('sidebar/tag', $tag_id); ?>
</div>

<?php echo $voucher_table ?>

<?php
	/* Check for Voucher Print, Download, Email */
	if ($this->session->userdata('print_voucher'))
	{
		print "<script type=\"text/javascript\">$(document).ready(function() {window.open('http://localhost/webzash/index.php/voucher/printpreview/" . $this->session->userdata('print_voucher_type') . "/" . $this->session->userdata('print_voucher_id') . "', '_blank', 'width=600,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'); });</script>";
		$this->session->unset_userdata('print_voucher');
		$this->session->unset_userdata('print_voucher_type');
		$this->session->unset_userdata('print_voucher_id');
	}
	if ($this->session->userdata('email_voucher'))
	{
		print "<script type=\"text/javascript\">$(document).ready(function() {window.open('http://localhost/webzash/index.php/voucher/email/" . $this->session->userdata('email_voucher_type') . "/" . $this->session->userdata('email_voucher_id') . "', '_blank', 'width=500,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'); });</script>";
		$this->session->unset_userdata('email_voucher');
		$this->session->unset_userdata('email_voucher_type');
		$this->session->unset_userdata('email_voucher_id');
	}
	if ($this->session->userdata('download_voucher'))
	{
		print "<script type=\"text/javascript\">$(document).ready(function() {window.open('http://localhost/webzash/index.php/voucher/download/" . $this->session->userdata('download_voucher_type') . "/" . $this->session->userdata('download_voucher_id') . "', '_blank', 'width=600,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'); });</script>";
		$this->session->unset_userdata('download_voucher');
		$this->session->unset_userdata('download_voucher_type');
		$this->session->unset_userdata('download_voucher_id');
	}
?>
<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>

