 <?php
$link = mysql_connect( 'localhost', 'mam', 'vNovom30Vete='); 
 mysql_select_db('mam_www', $link); 
 //$q="SELECT id FROM steelitems WHERE is_virtual=1 AND status_id = 0 AND is_deleted=0 AND is_available = 0";
 $q="SELECT DISTINCT
        si.id, si.status_id, si.is_available
    FROM steelitems_history AS si
    JOIN steelpositions_history AS sp ON sp.steelposition_id = si.steelposition_id
    WHERE si.location_id > 0 
    AND sp.qtty > 0 
    AND sp.is_from_order = 0 
    AND sp.is_deleted = 0 
    AND si.is_deleted = 0
	AND si.order_id = 0
	AND si.status_id > 4";
 
 $result = mysql_query($q, $link) or die(mysql_error());
 while(($row=mysql_fetch_array($result))!==false)
 {
	print_r($row['id'].'__'.$row['status_id'].'__'.$row['is_available'].'<br/>');
	/*$q_upd="UPDATE steelitems s
			SET s.status_id = (SELECT sh.status_id FROM steelitems_history sh WHERE
                      sh.steelitem_id = '{$row['id']}' AND sh.status_id<7 AND sh.status_id<>0 ORDER BY sh.id DESC LIMIT 1),
			s.is_available = 1
			WHERE s.id = '{$row['id']}'";
  mysql_query($q_upd, $link);*/    
 }