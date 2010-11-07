<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Webzash<?php if (isset($page_title)) echo ' | ' . $page_title; ?></title>

<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/tables.css">

<script type="text/javascript" src="<?php echo asset_url(); ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/custom.js"></script>

</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			Webzash
		</div>
		<div id="info">
			<?php if (isset($page_account_info)) echo $page_account_info ?>
		</div>
	</div>
	<div id="menu">
		<ul id="nav">
			<li><a href="<?php print base_url(); ?>" title="Dashboard">Dashboard</a></li>
			<li><?php echo anchor('account', 'Accounts', array('title' => 'Chart of accounts')); ?></li>
			<li><?php echo anchor('voucher', 'Vouchers', array('title' => 'Vouchers')); ?></li>
			<li><a href="#">Reports</a></li>
			<li><a href="#">Settings</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="sidebar">
			<?php if (isset($page_sidebar)) echo $page_sidebar; ?>
		</div>
		<div id="main">
			<div id="main-title">
				<?php if (isset($page_title)) echo $page_title; ?>
			</div>
			<div id="main-links">
				<?php if (isset($page_links)) {
					echo "<ul id=\"main-links-nav\">";
					foreach ($page_links as $link => $title) {
						echo "<li>" . anchor($link, $title, $title) . "</li>";
					}
					echo "</ul>";
				} ?>
			</div>
			<div class="clear">
			</div>
			<div id="main-content">
				<?php if ($this->session->flashdata('message')) { ?>
					<div id="message-box">
						<p><?php echo $this->session->flashdata('message'); ?></p>
					</div>
				<?php } ?>
				<?php if ($this->session->flashdata('error')) { ?>
					<div id="error-box">
						<p><?php echo $this->session->flashdata('error'); ?></p>
					</div>
				<?php } ?>
				<?php if (validation_errors()) { ?>
					<div id="validation-error-box">
						<?php echo validation_errors(); ?>
					</div>
				<?php } ?>
