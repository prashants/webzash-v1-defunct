<?php
echo "<div class=\"tag-title\">Tags<span class=\"float-right\">" . anchor("tag", "Edit") . "</span></div>";
	echo "<div class=\"tag-content\">";
	$tags = $this->Tag_model->get_all_tags($allow_none = FALSE);
	echo "<ul id=\"tag-list\">";
	if ($tags)
	{
		foreach ($tags as $id => $title)
		{

			echo "<li>" . $this->Tag_model->show_entry_tag_link($id) . "</li>";
		}
	} else {
		echo "<li>No tags defined</li>";
		echo "<li>" . anchor("tag/add", "Add one", array('title' => 'Add tag', 'class' => 'anchor-link-a')) . "</li>";
	}
	echo "</ul>";
	echo "</div>";
