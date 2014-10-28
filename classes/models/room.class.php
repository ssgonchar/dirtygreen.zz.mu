<?php
class Room extends Model
{
    function Room()
    {
        Model::Model('rooms');
    }
    
    public function create($title, $biz_id) {
        $query = "INSERT INTO `rooms`(`title`,`biz_id`) VALUES ('".$title."','".$biz_id."')";

        $resource = $this->table->_exec_raw_query($query);
        if ($resource) {
            Cache::ClearTag('rooms');
            return true;
        } else {
            return 'You have some problem with connection.';
        }
    }
}
