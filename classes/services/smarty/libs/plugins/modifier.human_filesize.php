<?php

function smarty_modifier_human_filesize($filesize)
{
    $title = array('b', 'K', 'M', 'G', 'T');

    for ($i = 0; $i < count($title); $i++)
    {
        if ($filesize > 1000)
        {
            $filesize = $filesize / 1024;
        }
        else
        {
            break;
        }
    }
    
    $parts = explode('.', $filesize);

    if (count($parts) > 1 && $parts[1] > 0)
    {
        return sprintf("%1.1f", $filesize) . $title[$i];
    }
    else
    {
        return $filesize . $title[$i];
    }
}

?>
