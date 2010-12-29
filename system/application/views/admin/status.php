<?php
	if (is_array($error_messages))
	{
		if (count($error_messages) > 0)
		{
			echo "<div id=\"error-box\">";
			echo "<ul>";
			foreach ($error_messages as $message)
			{
				echo ('<li>' . $message . '</li>');
			}
			echo "</ul>";
			echo "</div>";
		} else {
			echo "<div id=\"success-box\">";
			echo "<ul>";
			echo "<li>Everything is configured correctly.</li>";
			echo "</ul>";
			echo "</div>";

		}
	}

	echo "<p>";
	echo anchor('admin', 'Back', 'Back to admin');
	echo "</p>";
