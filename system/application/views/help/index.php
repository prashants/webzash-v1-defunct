<ul id="qa-list">
<li><span class="qa-heading">General</span>
	<ul>
	<li><a href="#general-1" class="anchor-link-a">How do I enable / disable logging ?</a></li>
	</ul>
</li>
<li><span class="qa-heading">Printing</span>
	<ul>
	<li><a href="#print-1" class="anchor-link-a">How do I modify the voucher print format ?</a></li>
	<li><a href="#print-2" class="anchor-link-a">How do I modify the reports print format (balance sheet, profit and loss statement, trial balance, ledger statement) ?</a></li>
	</ul>
</li>
<li><span class="qa-heading">Email</span>
	<ul>
	<li><a href="#email-1" class="anchor-link-a">How do I modify the voucher email format ?</a></li>
	</ul>
</li>
</ul>

<br /><br />

<div class="qa-section" id="general-1">
	<a name="print-1"></a>
	<div class="qa-question">Q. How do I enable / disable logging ?</div>
	<div class="qa-answer">You need "administrator" permissions to do this. After logging with "administrator" permissions click on "Administer" link on the top of the page. Then go to "General Settings" and check / uncheck the "Log Messages" option and click on "Update".<br /><br />Note: You can do this manually by opening the "config/settings/general.ini" in a text editor and changing the value of log = "1" or log = "0" to enable or disable logging respectively.</div>
</div>

<div class="qa-section" id="print-1">
	<a name="print-1"></a>
	<div class="qa-question">Q. How do I modify the voucher print format ?</div>
	<div class="qa-answer">Voucher print template is located at "system/application/views/voucher/printpreview.php". Modify this file to change the voucher print format.</div>
</div>

<div class="qa-section" id="print-2">
	<a name="print-2"></a>
	<div class="qa-question">Q. How do I modify the reports print format (balance sheet, profit and loss statement, trial balance, ledger statement) ?</div>
	<div class="qa-answer">Report print template is located at "system/application/views/report/report_template.php". Modify this file to change the report print format.</div>
</div>

<div class="qa-section" id="email-1">
	<a name="email-1"></a>
	<div class="qa-question">Q. How do I modify the voucher email format ?</div>
	<div class="qa-answer">Voucher email template is located at "system/application/views/voucher/emailpreview.php". Modify this file to change the voucher email format.</div>
</div>

