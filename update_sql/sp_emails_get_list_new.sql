DELIMITER //
DROP PROCEDURE IF EXISTS`sp_email_get_list_new`//
CREATE PROCEDURE `sp_email_get_list_new`(
    IN param_user_id INT,
    IN param_is_admin TINYINT,
    IN param_object_alias CHAR(20),
    IN param_object_id TEXT,
    IN param_mailbox_id TEXT,
    IN param_type_id TEXT,
    IN param_is_deleted TINYINT,
    IN param_approve_by INT,
    IN param_from INT,
    IN param_count INT)
sp:
BEGIN

    DECLARE var_user_mailboxes VARCHAR(1000) DEFAULT '';

    DROP TEMPORARY TABLE IF EXISTS t_email_ids;
    CREATE TEMPORARY TABLE t_email_ids(email_id INT);

    SET @var_param_user_id  = param_user_id;
    SET @var_object_alias   = param_object_alias;
    SET @var_object_id      = param_object_id;
    SET @var_mailbox_id     = param_mailbox_id;
    SET @var_type_id        = param_type_id;
    SET @var_is_deleted     = param_is_deleted;
    SET @var_approve_by     = param_approve_by;
    SET @var_from           = param_from;
    SET @var_count          = param_count;
    
    IF param_is_admin = 1
    THEN

        IF TRIM(param_object_alias) != '' AND TRIM(param_object_id) != ''
        THEN

            SET @var_stmt = CONCAT("   
                INSERT INTO t_email_ids(email_id)
                SELECT DISTINCT 
                    email_id
                FROM email_objects
                JOIN emails ON email_objects.email_id = emails.id
                WHERE email_objects.object_alias = ?
                AND email_objects.object_id IN (?)", 
                IF(TRIM(param_type_id) != '', CONCAT(" AND emails.type_id IN (", param_type_id), ") AND emails.type_id IN (1,2,3)"), 
                IF(param_is_deleted IN (1, 0), CONCAT(" AND emails.is_deleted = ", param_is_deleted), " AND emails.is_deleted = 0"),
                CASE 
                    WHEN param_approve_by > 0 THEN CONCAT(" AND emails.approve_by = ", param_approve_by)
                    WHEN param_approve_by = 0 THEN " AND (emails.approve_by <= 0 OR emails.approve_by IS NULL)"
                    ELSE ""
                END,
                " GROUP BY email_id ORDER BY emails.date_mail DESC                
            ;");

            PREPARE stmt FROM @var_stmt;
            EXECUTE stmt USING @var_object_alias, @var_object_id;
        
        ELSEIF TRIM(param_mailbox_id) != ''
        THEN

            SET @var_stmt = CONCAT("   
                INSERT INTO t_email_ids(email_id)
                SELECT DISTINCT 
                    email_id
                FROM email_mailboxes
                JOIN emails ON email_mailboxes.email_id = emails.id
                WHERE mailbox_id IN (?)", 
                IF(TRIM(param_type_id) != '', CONCAT(" AND emails.type_id IN (", param_type_id), ") AND emails.type_id IN (1,2,3)"), 
                IF(param_is_deleted IN (1, 0), CONCAT(" AND emails.is_deleted = ", param_is_deleted), " AND emails.is_deleted = 0"),
                CASE 
                    WHEN param_approve_by > 0 THEN CONCAT(" AND emails.approve_by = ", param_approve_by)
                    WHEN param_approve_by = 0 THEN " AND (emails.approve_by <= 0 OR emails.approve_by IS NULL)"
                    ELSE ""
                END,
                
                " GROUP BY email_id ORDER BY emails.date_mail DESC                
            ;");
        
            PREPARE stmt FROM @var_stmt;
            EXECUTE stmt USING @var_mailbox_id;

        ELSE

            SET @var_stmt = CONCAT("   
                INSERT INTO t_email_ids(email_id)
                SELECT DISTINCT 
                    emails.id AS email_id
                FROM emails
                WHERE 1 = 1",
                IF(TRIM(param_type_id) != '', CONCAT(" AND type_id IN (", param_type_id), ") AND emails.type_id IN (1,2,3)"), 
                IF(param_is_deleted IN (1, 0), CONCAT(" AND emails.is_deleted = ", param_is_deleted), " AND emails.is_deleted = 0"),
                CASE 
                    WHEN param_approve_by > 0 THEN CONCAT(" AND emails.approve_by = ", param_approve_by)
                    WHEN param_approve_by = 0 THEN " AND (emails.approve_by <= 0 OR emails.approve_by IS NULL)"
                    ELSE ""
                END,

                " ORDER BY emails.date_mail DESC
            ;");
SELECT stmt;
        /*
            PREPARE stmt FROM @var_stmt;
            EXECUTE stmt;
            */
        END IF;

    ELSE

        SET var_user_mailboxes = IFNULL((SELECT GROUP_CONCAT(mailbox_id SEPARATOR ",") FROM user_mailboxes WHERE user_id = param_user_id), '0');

        IF TRIM(param_object_alias) != '' AND  TRIM(param_object_id) != ''
        THEN

            SET @var_stmt = CONCAT("   
                INSERT INTO t_email_ids(email_id)
                SELECT DISTINCT 
                    email_objects.email_id
                FROM email_objects
                JOIN emails ON email_objects.email_id = emails.id
                JOIN email_mailboxes ON email_mailboxes.email_id = emails.id
                WHERE email_objects.object_alias = ?
                AND email_objects.object_id IN (?)
                AND email_mailboxes.mailbox_id IN (", var_user_mailboxes, ")", 
                IF(TRIM(param_type_id) != '', CONCAT(" AND emails.type_id IN (", param_type_id), ") AND emails.type_id IN (1,2,3)"),
                IF(param_is_deleted IN (1, 0), CONCAT(" AND emails.is_deleted = ", param_is_deleted), " AND emails.is_deleted = 0"),
                CASE 
                    WHEN param_approve_by > 0 THEN CONCAT(" AND emails.approve_by = ", param_approve_by)
                    WHEN param_approve_by = 0 THEN " AND (emails.approve_by <= 0 OR emails.approve_by IS NULL)"
                    ELSE ""
                END,
                
                " GROUP BY email_objects.email_id ORDER BY emails.date_mail DESC                 
            ;");
        
            PREPARE stmt FROM @var_stmt;
            EXECUTE stmt USING @var_object_alias, @var_object_id;

        ELSEIF TRIM(param_mailbox_id) != ''
        THEN

            SET @var_stmt = CONCAT("   
                INSERT INTO t_email_ids(email_id)
                SELECT DISTINCT 
                    email_id
                FROM email_mailboxes
                JOIN emails ON email_mailboxes.email_id = emails.id
                WHERE mailbox_id IN (?)
                AND mailbox_id IN (", var_user_mailboxes, ")", 
                IF(TRIM(param_type_id) != '', CONCAT(" AND emails.type_id IN (", param_type_id), ") AND emails.type_id IN (1,2,3)"), 
                IF(param_is_deleted IN (1, 0), CONCAT(" AND emails.is_deleted = ", param_is_deleted), " AND emails.is_deleted = 0"),
                CASE 
                    WHEN param_approve_by > 0 THEN CONCAT(" AND emails.approve_by = ", param_approve_by)
                    WHEN param_approve_by = 0 THEN " AND (emails.approve_by <= 0 OR emails.approve_by IS NULL)"
                    ELSE ""
                END,

                " GROUP BY email_id ORDER BY emails.date_mail DESC                
            ;");
        
            PREPARE stmt FROM @var_stmt;
            EXECUTE stmt USING @var_mailbox_id;

        ELSE

            SET @var_stmt = CONCAT("   
                INSERT INTO t_email_ids(email_id)
                SELECT DISTINCT 
                    emails.id AS email_id
                FROM emails
                JOIN email_mailboxes ON email_mailboxes.email_id = emails.id
                WHERE mailbox_id IN (", var_user_mailboxes, ")",
                IF(TRIM(param_type_id) != '', CONCAT(" AND emails.type_id IN (", param_type_id), ") AND emails.type_id IN (1,2,3)"), 
                IF(param_is_deleted IN (1, 0), CONCAT(" AND emails.is_deleted = ", param_is_deleted), " AND emails.is_deleted = 0"),
                CASE 
                    WHEN param_approve_by > 0 THEN CONCAT(" AND emails.approve_by = ", param_approve_by)
                    WHEN param_approve_by = 0 THEN " AND (emails.approve_by <= 0 OR emails.approve_by IS NULL)"
                    ELSE ""
                END,

                " GROUP BY emails.id ORDER BY emails.date_mail DESC                
            ;");
        
            PREPARE stmt FROM @var_stmt;
            EXECUTE stmt;

        END IF;

    END IF;

    
    SET @var_stmt = "
    SELECT e.*
    FROM t_email_ids AS e
    WHERE e.email_id NOT IN (SELECT ed.email_id
                            FROM email_delivered AS ed
                            WHERE
                                    ed.email_id = e.email_id
                                AND ed.user_id = ?
                                AND (ed.deleted_at > 0 OR ed.deleted_at IS NOT NULL))
    LIMIT ?, ?;";

    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt USING @var_param_user_id, @var_from, @var_count;

    SELECT COUNT(*) AS `rows`
    FROM t_email_ids AS e
    INNER JOIN emails AS em ON em.id = e.email_id AND em.is_sent = 0
    WHERE e.email_id NOT IN (   SELECT ed.email_id
                                FROM email_delivered AS ed
                                WHERE
                                    ed.email_id = e.email_id
                                AND ed.user_id = param_user_id
                                AND (ed.deleted_at > 0 OR ed.deleted_at IS NOT NULL));

    DROP TEMPORARY TABLE IF EXISTS t_email_ids;

END//


