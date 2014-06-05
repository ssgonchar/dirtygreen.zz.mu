<?php
	require 'connect.php';
	require 'add_city.php';
	
	switch($_REQUEST['method'])
	{
		case 'addCity':
			addCity();
			break;
	}