<div>
	<div id="current-active-account">
	<?php echo $current_account; ?>
	</div>

	<div id="left-col">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('admin/create', 'Create Account', array('title' => 'Create a new account')); ?>
			</div>
			<div class="settings-desc">
				Create a new webzash account
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('admin/active', 'Change Active Account', array('title' => 'Change active account')); ?>
			</div>
			<div class="settings-desc">
				Change existing active webzash account
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('admin/manage', 'Manage Accounts', array('title' => 'Manage existing accounts')); ?>
			</div>
			<div class="settings-desc">
				Manage existing webzash accounts
			</div>
		</div>
	</div>
	<div id="right-col">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('admin/gensetting', 'General Settings', array('title' => 'General Application Settings')); ?>
			</div>
			<div class="settings-desc">
				General application settings
			</div>
		</div>
	</div>
</div>
<div class="clear">
</div>
