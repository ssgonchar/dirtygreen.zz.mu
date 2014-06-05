<?php

class Mailbox extends Model
{
    function Mailbox()
    {
        Model::Model('mailboxes');
    }

    /**
     * Заполняет статистику для пользователя
     * 
     * @param mixed $rowset
     * 
     * @version 20120917, zharkov
     */
    function FillUserStat($mailboxes)
    {
        $hash       = 'mailboxes-' . $this->user_id . '-stat';
        $cache_tags = array($hash, 'mailboxes', 'emails');

        $is_admin   = $this->user_role <= ROLE_ADMIN ? 1 : 0;
        
        $rowset = $this->_get_cached_data($hash, 'sp_mailbox_get_userstat', array($this->user_id, $is_admin), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();

        foreach ($mailboxes as $key => $mailbox)
        {
            foreach ($rowset as $stat)
            {
                if ($mailbox['mailbox_id'] == $stat['mailbox_id'])
                {
                    $mailboxes[$key]['mailbox']['stat'] = $stat;
                    break;
                }
            }
        }
        
        return $mailboxes;
    }
    
    /**
     * Возвращает mailbox по значению параметра
     * 
     * @param mixed $param_name
     * @param mixed $param_value
     * 
     * @version 20130318, zharkov
     */
    function _get_by_param($param_name, $param_value)
    {
        foreach ($this->GetList(false) as $mailbox)
        {
            if (isset($mailbox['mailbox']))
            {
                $mailbox = $mailbox['mailbox'];
                
                if (isset($mailbox[$param_name]) && $mailbox[$param_name] == $param_value)
                {
                    return $mailbox;
                }                
            }
        }

        return null;
    }
    
    /**
     * Возвращает mailbox по username
     * 
     * @param mixed $address
     * 
     * @version 20130318, zharkov
     */
    function GetByUserName($username)
    {
        return $this->_get_by_param('username', $username);
    }

    /**
     * Возвращает мэйлбокс по адресу
     * 
     * @param mixed $address
     */
    function GetByAddress($address)
    {
        return $this->_get_by_param('address', $address);
    }
    
    /**
     * Проверяет есть ли среди адреса имеющиеся в системе мэйлбоксы
     * 
     * @param mixed $str список адресов через запятую
     */
    function FindInString($str)
    {
        $addresses  = explode(',', trim(preg_replace('#\s+#i', '', $str), ','));
        $result     = array();
        foreach ($this->GetList(false) as $mailbox)
        {
            if (isset($mailbox['mailbox']))
            {
                $mailbox = $mailbox['mailbox'];
                
                if (isset($mailbox['address']) && in_array($mailbox['address'], $addresses) && !array_key_exists($mailbox['id'], $result))
                {
                    $result[$mailbox['id']] = $mailbox['address'];
                }                
            }
        }
        
        return $result;
    }
    
    /**
     * Возвращает список доступных пользователю ящиков
     * 
     * @param mixed $user_id
     * 
     * @version 20120911, zharkov
     */
    function GetListForUser($user_id)
    {
        $hash       = 'user-' . $user_id . '-mailboxes';
        $cache_tags = array($hash, 'mailboxes');

        $rowset = $this->_get_cached_data($hash, 'sp_mailbox_get_list_for_user', array($user_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillMailboxInfo($rowset[0]) : array();

        return $rowset;        
    }

    /**
     * Возвращает список mailboxes
     * 
     * @version 20120911, zharkov
     */
    function GetList($only_active = true)
    {
        $hash       = 'mailboxes-' . ($only_active ? 'active' : 'all');
        $cache_tags = array($hash, 'mailboxes');

        $rowset = $this->_get_cached_data($hash, 'sp_mailbox_get_list', array($only_active), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillMailboxInfo($rowset[0]) : array();

        return $rowset;        
    }
        
    /**
     * Возвращает mailbox по идентификатору
     *     
     * @param mixed $id
     * 
     * @version 20120911, zharkov
     */
    function GetById($id)
    {
        $dataset = $this->FillMailboxInfo(array(array('mailbox_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['mailbox']) ? $dataset[0] : null;
    }
    
    
    /**
     * Возвращет информацию о mailbox
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * 
     * @version 20120911, zharkov
     */
    function FillMailboxInfo($rowset, $id_fieldname = 'mailbox_id', $entityname = 'mailbox', $cache_prefix = 'mailbox')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_mailbox_get_list_by_ids', array('mailboxes' => '', 'mailbox' => 'id'), array());
    }
    
    /**
     * Сохраняет mailbox
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     * 
     * пока не используется
     */
    function Save()
    {        
        $result = $this->CallStoredProcedure('sp_mailbox_save', array());
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('mailbox-' . $result['id']);
        Cache::ClearTag('mailboxes');
        
        return $result;
    }    
}
