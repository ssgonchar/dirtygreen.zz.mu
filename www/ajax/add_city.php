<?php
	function addCity()
	{
		global $link;
		
		$q_region = "	INSERT INTO regions (country_id, title)
						VALUES ('{$_REQUEST['country']}', '{$_REQUEST['region']}')";
		echo $q_region;
		//mysqli_query($link, $q_region);
		//$id_region = mysqli_insert_id($link);
		
		$q_city = "	INSERT INTO regions (country_id, region_id, title, dialcode)
						VALUES ('{$_REQUEST['country']}', '{$id_region}', '{$_REQUEST['city']}', '{$_REQUEST['dialcode']}')";
		echo $q_city;
		//mysqli_query($link, $q_city);				
		//$id_city = mysqli_insert_id($link);
		
		//$arr['region']=$id_region;
		//$arr['city']=$id_city;
		
		//$json = json_encode($arr);
		//echo $json;
	}