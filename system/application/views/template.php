<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Webzash<?php if (isset($page_title)) echo ' | ' . $page_title; ?></title>

<?php echo link_tag(asset_url() . 'images/favicon.ico', 'shortcut icon', 'image/ico'); ?>

<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/tables.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/custom.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/menu.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/jquery.datepick.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/thickbox.css">

<?php
/* Dynamically adding css files from controllers */
if (isset($add_css))
{
	foreach ($add_css as $id => $row)
	{
		echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . asset_url() . $row ."\">";
	}
}
?>

<script type="text/javascript" src="<?php echo asset_url(); ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/jquery.datepick.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/custom.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/superfish.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/supersubs.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/thickbox-compressed.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/ezpz_tooltip.min.js"></script>

<?php
/* Dynamically adding javascript files from controllers */
if (isset($add_javascript))
{
	foreach ($add_javascript as $id => $row)
	{
		echo "<script type=\"text/javascript\" src=\"" . asset_url() . $row ."\"></script>";
	}
}
?>

<script type="text/javascript">
/* Loading JQuery Superfish menu */
$(document).ready(function() {
	$("ul.sf-menu").supersubs({ 
		minWidth:12,
		maxWidth:27,
		extraWidth: 1
	}).superfish(); // call supersubs first, then superfish, so that subs are 
	$('.datepicker').datepick({dateFormat: '<?php echo $this->config->item('account_date_format'); ?>'});
	$('.datepicker-restrict').datepick({
		dateFormat: '<?php echo $this->config->item('account_date_format'); ?>',
		minDate: '<?php echo date_mysql_to_php($this->config->item('account_fy_start')); ?>',
		maxDate: '<?php echo date_mysql_to_php($this->config->item('account_fy_end')); ?>',
	});
});
</script>

</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			Webzash <span id="beta-area">(beta)</span>
		</div>
		<div id="admin">
			<?php echo anchor('admin', 'Administer', array('title' => "Administer", 'class' => 'anchor-link-b')); ?>
		</div>
		<div id="info">
			<?php
				echo $this->config->item('account_name');
				echo " (";
				echo "<a href=\"#TB_inline?height=300&width=300&inlineId=changeActiveAccount\" class=\"thickbox anchor-link-a\" title=\"Change Active Account\">change</a>";
				echo ")<br />";
				echo "FY : ";
				echo date_mysql_to_php_display($this->config->item('account_fy_start'));
				echo " - ";
				echo date_mysql_to_php_display($this->config->item('account_fy_end'));
			?>
		</div>
	</div>
	<div id="menu">
		<ul class="sf-menu">
			<li class="current">
				<a href="<?php print base_url(); ?>" title="Dashboard">Dashboard</a>
			</li>
			<li>
				<?php echo anchor('account', 'Accounts', array('title' => 'Chart of accounts')); ?>
			</li>
			<li>
				<?php echo anchor('voucher', 'Vouchers', array('title' => 'Vouchers')); ?>
				<ul>
					<li><?php echo anchor('voucher/show/all', 'All', array('title' => 'All Vouchers')); ?></li>
					<li><?php echo anchor('voucher/show/receipt', 'Receipt', array('title' => 'Receipt Vouchers')); ?></li>
					<li><?php echo anchor('voucher/show/payment', 'Payment', array('title' => 'Payment Vouchers')); ?></li>
					<li><?php echo anchor('voucher/show/contra', 'Contra', array('title' => 'Contra Vouchers')); ?></li>
					<li><?php echo anchor('voucher/show/journal', 'Journal', array('title' => 'Journal Vouchers')); ?></li>
					<li><?php echo anchor('voucher/show/draft', 'Draft', array('title' => 'Draft Vouchers')); ?></li>
				</ul>
			</li>
			<li>
				<?php echo anchor('report', 'Reports', array('title' => 'Reports')); ?>
				<ul>
					<li><?php echo anchor('report/balancesheet', 'Balance Sheet', array('title' => 'Balance Sheet')); ?></li>
					<li><?php echo anchor('report/profitandloss', 'Profit & Loss', array('title' => 'Profit & Loss')); ?></li>
					<li><?php echo anchor('report/trialbalance', 'Trial Balance', array('title' => 'Trial Balance')); ?></li>
					<li><?php echo anchor('report/ledgerst', 'Ledger Statement', array('title' => 'Ledger Statement')); ?></li>
				</ul>
			</li>
			<li>
				<?php echo anchor('setting', 'Settings', array('title' => 'Settings')); ?>
			</li>
			<li>
				<?php echo anchor('help', 'Help', array('title' => 'Help', 'class' => 'last')); ?>
			</li>
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
				<?php if (isset($nav_links)) {
					echo "<ul id=\"main-links-nav\">";
					foreach ($nav_links as $link => $title) {
						if ($title == "Print Preview")
							echo "<li>" . anchor_popup($link, $title, array('title' => $title, 'class' => 'nav-links-item', 'style' => 'background-image:url(\'' . asset_url() . 'images/buttons/navlink.png\');', 'width' => '1024')) . "</li>";
						else
							echo "<li>" . anchor($link, $title, array('title' => $title, 'class' => 'nav-links-item', 'style' => 'background-image:url(\'' . asset_url() . 'images/buttons/navlink.png\');')) . "</li>";
					}
					echo "</ul>";
				} ?>
			</div>
			<div class="clear">
			</div>
			<div id="main-content">
				<?php
				$messages = $this->messages->get();
				if (is_array($messages))
				{
					if (count($messages['success']) > 0)
					{
						echo "<div id=\"success-box\">";
						echo "<ul>";
						foreach ($messages['success'] as $message) {
							echo ('<li>' . $message . '</li>');
						}
						echo "</ul>";
						echo "</div>";
					}
					if (count($messages['error']) > 0)
					{
						echo "<div id=\"error-box\">";
						echo "<ul>";
						foreach ($messages['error'] as $message) {
							if (substr($message, 0, 4) == "<li>")
								echo ($message);
							else
								echo ('<li>' . $message . '</li>');
						}
						echo "</ul>";
						echo "</div>";
					}
					if (count($messages['message']) > 0)
					{
						echo "<div id=\"message-box\">";
						echo "<ul>";
						foreach ($messages['message'] as $message) {
							echo ('<li>' . $message . '</li>');
						}
						echo "</ul>";
						echo "</div>";
					}
				}
				?>
				<?php echo $contents; ?>
			</div>
		</div>
	</div>
</div>
<div id="footer">
	<?php if (isset($page_footer)) echo $page_footer ?>
	<a href="http://webzash.wordpress.com" target="_blank">Webzash<a/> is licensed under <a href="http://www.gnu.org/licenses/agpl-3.0.txt" target="_blank">GNU Affero General Public License, version 3</a> as published by the Free Software Foundation.
</div>
<div id="account_change" class="hidden-element">
	<div id="changeActiveAccount">
	<?php $this->load->view("setting/change"); ?>
	</div>
</div>
</body>
</html>
