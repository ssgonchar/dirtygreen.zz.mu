<?php
	$dbhost = 'localhost';
	$dbname = 'mam_www';
	$dbuser	= 'mam';
	$dbpass	= 'vNovom30Vete=';
	$charset= 'utf8 ';
	
	$link = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	
/* Посылаем запрос серверу */ 
/*
if ($result = mysqli_query($link, 'SELECT Name, Population FROM City ORDER BY Population DESC LIMIT 5')) { 

    print("Очень крупные города:\n"); 

    /* Выборка результатов запроса */ 
	/*
    while( $row = mysqli_fetch_assoc($result) ){ 
        printf("%s (%s)\n", $row['Name'], $row['Population']); 
    } 

    /* Освобождаем используемую память */ 
	/*
    mysqli_free_result($result); 
} 

/* Закрываем соединение */ 
/*
mysqli_close($link); 