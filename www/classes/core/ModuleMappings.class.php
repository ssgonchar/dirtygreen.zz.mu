<?
class ModuleMappings
{
    var $rules = array();

    function ModuleMappingsBase()
    {   

    }

    function CheckRules($query_string)
    {
        return $this->apply_module_rules($query_string);
    }

    function apply_module_rules($query_string)
    {
        foreach ($this->rules as $rule => $result)
        {
            if (preg_match_all($rule, $query_string, $matches))
            {
                $params = array();

                foreach ($result as $param => $value)
                {
                    if (preg_match('/\$[0-9]/', $value))
                    {
                        $params[$param] = $matches[intval($value[1])][0];
                    }
                    else
                    {
                        if ($value == '$current_user_id' && !empty($_SESSION['user'])) 
                        {
                            $value = $_SESSION['user']['id'];
                        }

                        if ($value == '$current_user_login' && !empty($_SESSION['user'])) 
                        {
                            $value = $_SESSION['user']['login'];
                        }

                        $params[$param] = $value;
                    }
                }

                return $params;
            }
        }

        return false;
    }
}
?>