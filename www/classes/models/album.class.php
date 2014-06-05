<?php

class Album extends Model
{
    function Album()
    {
        Model::Model('albums');
    }

    /**
     * Возвращает альбом по алиасу
     * 
     * @param mixed $alias
     * 
     * @version 20130305, zharkov
     */
    function GetByAlias($alias)
    {
        $hash       = 'album-' . $alias;
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_album_get_by_alias', array($this->user_id, $alias), $cache_tags);
        return isset($rowset[0]) && isset($rowset[0][0]) && isset($rowset[0][0]['album_id']) ? $this->GetById($rowset[0][0]['album_id']) : array();
    }
    
    /**
     * Возвращет информацию об альбоме
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillAlbumInfo($rowset, $id_fieldname = 'album_id', $entityname = 'album', $cache_prefix = 'album')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_album_get_list_by_ids', array('qctypes' => '', 'qctype' => 'id'), array());
    }
    
    /**
     * Возвращает qctype по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillAlbumInfo(array(array('album_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['album']) ? $dataset[0] : null;
    }
}
