
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	$get = $this->uri->uri_to_assoc();
	if (!isset($get['type'])) {
		exit('No proper type parameter specified');
	}
	
	function buildSetPerTag($get, $entryT, $maxRows=10) {
		if (isset($get['callback'])) {
			echo($get['callback']);
		} else {
			echo('?');
		}
		printf(" ([");
		
		// Expense
		$entry_type = $entryT;			
		$result = mysql_query("select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = " . $entry_type . " and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) DESC LIMIT " . $maxRows) 
		or die("Invalid Query: " . mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			printf("['%s', %s],", $row[0], $row[1]); 
		}
		
		$result = mysql_query("select 'Others', sub-main AS others from (select sum(total) as sub from ( select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = " . $entry_type . " and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) ) AS t1) AS Tsub, (select sum(total) as main from ( select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = " . $entry_type . " and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) DESC LIMIT " . $maxRows . " ) AS t0) AS Tmain") 
		or die("Invalid Query: " . mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			printf("['%s', %s]", $row[0], $row[1]); 
		}
		
		printf("]);");
	}
	
	function buildSetPerMonth($get, $entryT) {
		if (isset($get['callback'])) {
			echo($get['callback']);
		} else {
			echo('?');
		}
		printf(" ([");
		$entry_type = $entryT;
		$result = mysql_query("select CONCAT(MONTHNAME(STR_TO_DATE(DATE_FORMAT(e.date,  '%m'), '%m')), ' ' , DATE_FORMAT(e.date,  '%Y')), sum(e.dr_total) from entries e where entry_type = " . $entry_type . " group by MONTH(e.date) order by e.date")
		or die("Invalid Query: " . mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			printf("['%s', %s],", $row[0], $row[1]); 
		}
		printf("]);");
	}

	switch ($get['type']) {	
		case 'outTag':
			/**
			 * EXPENSES PER TAG.
			 */
			buildSetPerTag($get, 2);
		break;
		
		case 'inTag':
			/**
			 * ENTRIES PER TAG.
			 */
			buildSetPerTag($get, 1);
		break;
		
		case 'outMonth':
			/**
			 * EXPENSES PER MONTH.
			 */
			buildSetPerMonth($get, 2);
		break;
		
		case 'inMonth':
			/**
			 * ENTRIES PER MONTH.
			 */
			buildSetPerMonth($get, 1);
		break;
	}
?>