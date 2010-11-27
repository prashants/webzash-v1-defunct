<?php

echo "<p>";
echo form_dropdown('accounts', $accounts);
echo "</p>";

echo "<p>";
echo form_submit('submit', 'Change');
echo " ";
echo anchor('admin', 'Back', array('title' => 'Back to admin'));
echo form_close();
echo "</p>";
?>
