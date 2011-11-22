<ul id="qa-list">
<li><span class="qa-heading">General</span>
	<ul>
	<li><a href="#general-1" class="anchor-link-a">How do I enable / disable logging ?</a></li>
	<li><a href="#general-2" class="anchor-link-a">What is account lock and how do I enable / disable it ?</a></li>
	</ul>
</li>
<li><span class="qa-heading">Printing</span>
	<ul>
	<li><a href="#print-1" class="anchor-link-a">How do I modify the entry print format ?</a></li>
	<li><a href="#print-2" class="anchor-link-a">How do I modify the reports print format (balance sheet, profit and loss statement, trial balance, ledger statement) ?</a></li>
	</ul>
</li>
<li><span class="qa-heading">Email</span>
	<ul>
	<li><a href="#email-1" class="anchor-link-a">How do I modify the entry email format ?</a></li>
	<li><a href="#email-2" class="anchor-link-a">How do send entries using gmail ?</a></li>
	</ul>
</li>
</ul>

<br /><br />

<div class="qa-section" id="general-1">
	<a name="print-1"></a>
	<div class="qa-question">Q. How do I enable / disable logging ?</div>
	<div class="qa-answer">You need "administrator" permissions to do this. After logging with "administrator" permissions click on "Administer" link on the top of the page. Then go to "General Settings" and check / uncheck the "Log Messages" option and click on "Update".<br /><br />Note: You can do this manually by opening the "config/settings/general.ini" in a text editor and changing the value of log = "1" or log = "0" to enable or disable logging respectively.</div>
</div>

<div class="qa-section" id="general-2">
	<a name="print-1"></a>
	<div class="qa-question">Q. What is account lock and how do I enable / disable it ?</div>
	<div class="qa-answer">Once a account is locked it cannot be modified any further, it becomes read-only. Click on "Settings" in Main Menu and then select "Account Settings". You need to check / uncheck the option called "Account Locked" (in the bottom) to enable or disable the account lock respectively.<br /><br />Note: If account is locked you can see a messages 'Account is currently locked to prevent any further modifications.' in the account dashboard.</div>
</div>

<div class="qa-section" id="print-1">
	<a name="print-1"></a>
	<div class="qa-question">Q. How do I modify the entry print format ?</div>
	<div class="qa-answer">Entry print template is located at "system/application/views/entry/printpreview.php". Modify this file to change the entry print format.</div>
</div>

<div class="qa-section" id="print-2">
	<a name="print-2"></a>
	<div class="qa-question">Q. How do I modify the reports print format (balance sheet, profit and loss statement, trial balance, ledger statement) ?</div>
	<div class="qa-answer">Report print template is located at "system/application/views/report/report_template.php". Modify this file to change the report print format.</div>
</div>

<div class="qa-section" id="email-1">
	<a name="email-1"></a>
	<div class="qa-question">Q. How do I modify the entry email format ?</div>
	<div class="qa-answer">Entry email template is located at "system/application/views/entry/emailpreview.php". Modify this file to change the entry email format.</div>
</div>

<div class="qa-section" id="email-2">
	<a name="email-2"></a>
	<div class="qa-question">Q. How do I send entries using gmail ?</div>
	<div class="qa-answer">You need to use the following gmail settings in Settings > Email Settings<br /><br />
	Email protocol : smtp<br />
	Hostname : ssl://smtp.googlemail.com<br />
	Port : 465<br />
	Email username : your-username@gmail.com<br />
	Email Password : your-password<br />
	</div>
</div>
