<div id="tag-sidebar">
	<?php $this->load->view('sidebar/tag', $tag_id); ?>
</div>

<?php echo $voucher_table ?>
<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>

