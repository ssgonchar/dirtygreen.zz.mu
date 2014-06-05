-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.97.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 02/08/2013 08:12:54
-- Версия сервера: 5.5.27
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_cmr_get_list$$
CREATE PROCEDURE sp_cmr_get_list(
	IN param_ra_id INT,
	IN param_from INT,
	IN param_count INT
)
sp:
BEGIN

    PREPARE stmt FROM
    "SELECT 
        cmr.id AS cmr_id 
    FROM cmr
	LEFT JOIN ra ON ra.id = cmr.ra_id
	WHERE
			ra.is_deleted = 0
		AND (cmr.ra_id = ? OR ? = -1)
    ORDER BY cmr.number DESC 
    LIMIT ?, ?;";

    SET
		@stmt_param_ra_id = param_ra_id,

		@stmt_from  = param_from,
		@stmt_count	= param_count
	;
    
    EXECUTE stmt
	USING
		@stmt_param_ra_id,
		@stmt_param_ra_id,

		@stmt_from,
		@stmt_count
	;

   
    SELECT
		COUNT(cmr.id) AS rows_count
	FROM cmr
	LEFT JOIN ra ON ra.id = cmr.ra_id
	WHERE
			ra.is_deleted = 0
		AND (cmr.ra_id = param_ra_id OR param_ra_id = -1)
	;

END
$$

DROP PROCEDURE IF EXISTS sp_ddt_get_list$$
CREATE PROCEDURE sp_ddt_get_list(
	IN param_ra_id INT,
	IN param_from INT,
	IN param_count INT
)
sp:
BEGIN

    PREPARE stmt FROM
    "SELECT 
        ddt.id AS ddt_id 
    FROM ddt
	LEFT JOIN ra ON ra.id = ddt.ra_id
	WHERE
			ra.is_deleted = 0
		AND (ddt.ra_id = ? OR ? = -1)
    ORDER BY ddt.number DESC
    LIMIT ?, ?;";

    SET
		@stmt_param_ra_id = param_ra_id,

		@stmt_from  = param_from,
		@stmt_count	= param_count
	;
    
    EXECUTE stmt
	USING
		@stmt_param_ra_id,
		@stmt_param_ra_id,

		@stmt_from,
		@stmt_count
	;

   
    SELECT
		COUNT(ddt.id) AS rows_count
	FROM ddt
	LEFT JOIN ra ON ra.id = ddt.ra_id
	WHERE
			ra.is_deleted = 0
		AND (ddt.ra_id = param_ra_id OR param_ra_id = -1)
	;

END
$$

DROP PROCEDURE IF EXISTS sp_ra_get_list$$
CREATE PROCEDURE sp_ra_get_list(
	IN param_from INT,
	IN param_count INT
)
sp:
BEGIN

    PREPARE stmt FROM
    "SELECT 
        id AS ra_id 
    FROM ra
	WHERE is_deleted = 0
    ORDER BY number DESC 
    LIMIT ?, ?;";

    SET
		@stmt_from  = param_from,
		@stmt_count	= param_count
	;
    
    EXECUTE stmt
	USING
		@stmt_from,
		@stmt_count
	;

   
    SELECT
        COUNT(*) AS rows_count
    FROM ra;

END
$$

DELIMITER ;
