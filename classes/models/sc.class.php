<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/qctype.class.php';

class SC extends Model
{
    function SC()
    {
        Model::Model('sc');
    }


    /**
     * Обновляет данные об атачменте, связанным с документом
     * 
     * @param mixed $sc_id
     * @param mixed $attachment_id
     * 
     * @version 20120816, zharkov
     */
    function UpdateAttachment($sc_id, $attachment_id)
    {
        $this->Update($sc_id, array(
            'attachment_id' => $attachment_id
        ));
    }


    /**
     * Список sc
     * 
     * @version 20120722, zharkov
     */
    function GetList($page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'sc-list' . md5('-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'sc');        
        
        $rowset     = $this->_get_cached_data($hash, 'sp_sc_get_list', array($start, $per_page), $cache_tags);
        $data       = isset($rowset[0]) ? $this->FillSCInfo($rowset[0]) : array();
        
        foreach ($data as $key => $row)
        {
            $data[$key]['order_id'] = $row['sc']['order_id'];
        }
        
        $orders = new Order();
        $data   = $orders->FillOrderInfo($data);

        return array(
            'data'  => $data,
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0
        );        
    }

    /**
     * Список sc для заказа
     * 
     * @param mixed $order_id
     */
    function GetListByOrder($order_id)
    {
        $hash       = 'sc-order-' . $order_id;
        $cache_tags = array($hash, 'sc');        
        
        $rowset     = $this->_get_cached_data($hash, 'sp_sc_get_list_by_order', array($this->user_id, $order_id), $cache_tags);
        $rowset     = isset($rowset[0]) ? $this->FillSCInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает специальные требования
     * 
     * @param mixed $sc_id
     */
    function GetSpecialRequirements($sc_id)
    {
        $sc = $this->GetById($sc_id);
        if (empty($sc)) return array();

        $spec_requirements = array(
            'chemical_composition'      => 'Chemical Composition', 
            'tolerances'                => 'Tolerances on Dimensions', 
            'hydrogen_control'          => 'Hydrogen Control', 
            'surface_quality'           => 'Surface Quality', 
            'surface_condition'         => 'Surface Condition',
            'side_edges'                => 'Side Edges', 
            'front_and_back_ends'       => 'Front & Back Ends', 
            'origin'                    => 'Origin', 
            'marking'                   => 'Marking', 
            'packing'                   => 'Packing', 
            'stamping'                  => 'Stamping', 
            'ust_standard'              => 'UST Standard, class', 
            'dunnaging_requirements'    => 'Dunnaging Requirements', 
            'documents_supplied'        => 'Documents Supplied',
            'inspection'                => 'Inspection', 
            'delivery_form'             => 'Delivery Condition', 
            'reduction_of_area'         => 'Reduction of Area', 
            'testing'                   => 'Testing', 
            'notes'                     => 'Notes',
            'qctype_id'                 => 'Quality Certificate'
        );
        
        $result = array();
        $row    = array();
        foreach($sc['sc'] as $key => $value)
        {
            if (array_key_exists($key, $spec_requirements))
            {
                if (empty($value)) continue;
                
                if (count($row) == 2)
                {
                    $result[] = $row;
                    $row = array();
                }
                
                if ($key == 'qctype_id') $value = $sc['sc']['qctype']['title'];
                $row[count($row) + 1] = array('title' => $spec_requirements[$key], 'value' => $value);
            }
        }
        
        if (!empty($row)) $result[] = $row;

        return $result;
    }
    
    /**
     * Очищает позиции
     * 
     * @param mixed $sc_id
     */
    function ClearPositions($sc_id)
    {
        $this->CallStoredProcedure('sp_sc_clear_positions', array($this->user_id, $sc_id));
    }
    
    /**
     * Добавляет позицию
     * 
     * @param mixed $sc_id
     * @param mixed $position_id
     */
    function SavePosition($sc_id, $position_id)
    {
        $this->CallStoredProcedure('sp_sc_save_position', array($this->user_id, $sc_id, $position_id));

        Cache::ClearTag('sc-' . $sc_id);
        Cache::ClearTag('scquick-' . $sc_id);
        Cache::ClearTag('scpositions-' . $sc_id);        
    }
    
    /**
     * Возвращает те позиции из заказа которые выбраны в SC
     * 
     * @param mixed $sc_id
     */
    function GetPositionsFull($sc_id)
    {
        $sc = $this->GetById($sc_id);
        if (empty($sc)) return null;
        
        $sc_positions = $this->GetPositions($sc_id);
        if (empty($sc_positions)) return null;
        
        $orders     = new Order();
        $positions  = $orders->GetPositions($sc['sc']['order_id']);

        foreach ($positions as $key => $row)
        {
            // удаляет позиции, которых нет в SC
            if (!array_key_exists($row['position_id'], $sc_positions)) 
            {
                unset($positions[$key]);
                continue;   
            }
            
            // для pdf нужно чтобы значимые поля были на одном уровне без вложенностей
            if (isset($row['steelgrade']) && !empty($row['steelgrade'])) $positions[$key]['steelgrade_title'] = $row['steelgrade']['title'];
            
            $positions[$key]['price']   = round($row['price'], 2);
            $positions[$key]['weight']  = round($row['weight'], 2);
            $positions[$key]['value']   = round($row['value'], 2);
        }
        
        return $positions;
    }
    
    /**
     * Возвращает список позиций
     * 
     * @param mixed $id
     */
    function GetPositions($sc_id)
    {
        $hash       = 'scpositions-' . $sc_id;
        $cache_tags = array($hash);        
        $rowset     = $this->_get_cached_data($hash, 'sp_sc_get_positions', array($sc_id), $cache_tags);
        $rowset     = isset($rowset[0]) ? $rowset[0] : array();
        
        $result = array();
        foreach ($rowset as $row) $result[$row['position_id']] = $row['position_id'];
        
        return $result;
    }
    
    /**
     * Возвращает sc по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillSCInfo(array(array('sc_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['sc']) ? $dataset[0] : null;
    }
        
    /**
     * Возвращет информацию о sc
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillSCInfo($rowset, $id_fieldname = 'sc_id', $entityname = 'sc', $cache_prefix = 'sc')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_sc_get_list_by_ids', array('scs' => '', 'sc' => 'id'), array());
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;

            $sc = $row[$entityname];
            
            if (!empty($sc['person_id'])) $rowset[$key]['sc_person_id'] = $sc['person_id'];
            
            $rowset[$key]['sc_author_id']       = $sc['created_by'];
            $rowset[$key]['sc_modifier_id']     = $sc['modified_by'];
            $rowset[$key]['sc_qctype_id']       = $sc['qctype_id'];
            $rowset[$key]['sc_attachment_id']   = $sc['attachment_id'];
        }
        
        $attachments    = new Attachment();
        $rowset         = $attachments->FillAttachmentInfo($rowset, 'sc_attachment_id', 'sc_attachment');
        
        $perssons       = new Person();
        $rowset         = $perssons->FillPersonInfo($rowset, 'sc_person_id', 'sc_person');
        
        $users          = new User();
        $rowset         = $users->FillUserInfo($rowset, 'sc_author_id',   'sc_author');
        $rowset         = $users->FillUserInfo($rowset, 'sc_modifier_id', 'sc_modifier');
        
        $qctypes        = new QCType();
        $rowset         = $qctypes->FillQCTypeInfo($rowset, 'sc_qctype_id', 'sc_qctype');
        
        foreach ($rowset as $key => $row) 
        {
            if (!isset($row[$entityname])) continue;

            if (isset($row['sc_attachment']) && !empty($row['sc_attachment']))
            {
                $rowset[$key][$entityname]['attachment'] = $row['sc_attachment'];
            }            
            unset($rowset[$key]['sc_attachment_id']);
            unset($rowset[$key]['sc_attachment']);


            if (isset($row['sc_person']))
            {
                $rowset[$key][$entityname]['person'] = $row['sc_person'];
            }
            unset($rowset[$key]['sc_person']);
            unset($rowset[$key]['sc_person_id']);            

            
            if (isset($row['sc_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['sc_author'];
            }                
            unset($rowset[$key]['sc_author']);
            unset($rowset[$key]['sc_author_id']);

            
            if (isset($row['sc_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['sc_modifier'];
            }
            unset($rowset[$key]['sc_modifier']);
            unset($rowset[$key]['sc_modifier_id']);
            
            
            if (isset($row['sc_qctype']))
            {
                $rowset[$key][$entityname]['qctype'] = $row['sc_qctype'];
            }
            unset($rowset[$key]['sc_qctype']);
            unset($rowset[$key]['sc_qctype_id']);            
            
            
            $rowset[$key][$entityname]['doc_no']       = 'SC' . substr((10000 + $row[$entityname]['id']), 1) . '/' . substr($row[$entityname]['created_year'], 2);
            $rowset[$key][$entityname]['doc_no_short'] = 'SC' . substr((10000 + $row[$entityname]['id']), 1);
            
        }
        
        
        return $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по SC
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'sc_id', $entityname = 'sc')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'scquick', 'sp_sc_get_quick_by_ids', array('scsquick' => '', 'scs' => '', 'sc' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname . 'quick']) && !empty($row[$entityname . 'quick']['attachment_id']))
            {
                $rowset[$key][$entityname . 'attachment_id'] = $row[$entityname . 'quick']['attachment_id'];
            }
        }

        $attachments    = new Attachment();
        $rowset         = $attachments->FillAttachmentInfo($rowset, $entityname . 'attachment_id', $entityname . 'attachment');
        
        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row[$entityname . 'quick'])) $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];                
                if (isset($row[$entityname . 'attachment'])) $rowset[$key][$entityname]['attachment'] = $row[$entityname . 'attachment'];
            }
            
            unset($rowset[$key][$entityname . 'quick']);
            unset($rowset[$key][$entityname . 'attachment']);
            unset($rowset[$key][$entityname . 'attachment_id']);
        }
        
        return $rowset;
    }
    
    
    /**
     * Сохраняет SC
     * 
     * @param mixed $id
     * @param mixed $order_id
     * @param mixed $chemical_composition
     * @param mixed $tolerances
     * @param mixed $hydrogen_control
     * @param mixed $surface_quality
     * @param mixed $surface_condition
     * @param mixed $side_edges
     * @param mixed $marking
     * @param mixed $packing
     * @param mixed $stamping
     * @param mixed $ust_standard
     * @param mixed $dunnaging_requirements
     * @param mixed $documents_supplied
     * @param mixed $front_and_back_ends
     * @param mixed $origin
     * @param mixed $inspection
     * @param mixed $delivery_form
     * @param mixed $reduction_of_area
     * @param mixed $testing
     * @param mixed $delivery_costs
     * @param mixed $qc_type_id
     * @return resource
     */
    function Save($id, $order_id, $person_id, $delivery_point, $delivery_date, $chemical_composition, $tolerances, $hydrogen_control, $surface_quality, $surface_condition, $side_edges,
                    $marking, $packing, $stamping, $ust_standard, $dunnaging_requirements, $documents_supplied, $front_and_back_ends, 
                    $origin, $inspection, $delivery_form, $reduction_of_area, $testing, $delivery_cost, $qctype_id, $notes, $transport_mode)
    {        
        $result = $this->CallStoredProcedure('sp_sc_save', array($this->user_id, $id, $order_id, $person_id, $delivery_point, $delivery_date, 
                    $chemical_composition, $tolerances, $hydrogen_control, $surface_quality, $surface_condition, $side_edges, $marking, 
                    $packing, $stamping, $ust_standard, $dunnaging_requirements, $documents_supplied, $front_and_back_ends, $origin, 
                    $inspection, $delivery_form, $reduction_of_area, $testing, $delivery_cost, $qctype_id, $notes, $transport_mode));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('sc-' . $result['id']);
        Cache::ClearTag('sc-order-' . $order_id);        
        Cache::ClearTag('sc');
        
        return $result;
    }
}
