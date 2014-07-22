<?php
class Translit
{
/*
    function Translit()
    {
    }
*/
    static function Encode($input_string)
    {
        // from http://www.transliteration.ru/hand_transliteration.html
        $statements     = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '.\'', 'ы' => 'y', 'ь' => '\'', 'э' => 'e-', 
        'ю' => 'yu', 'я' => 'ya', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 
        'Ё' => 'Jo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 
        'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 
        'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shh', 'Ъ' => '.\'', 'Ы' => 'Y', 
        'Ь' => '\'', 'Э' => 'E-', 'Ю' => 'Yu', 'Я' => 'Ya', '№' => 'No'
        );

        foreach($statements as $key => $value)
        {
            $input_string = str_replace($key, $value, $input_string);
        }

        return $input_string;
    }

    static function ClearServiceCharacters($input_string)
    {
        $statements = array('~', ',', '`', '\'', '"', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', 
                            '-', '+', '\\', '|', '/', '{', '[', '}', ']', ':', ';', '<', '>', '?');

        for ($i = 0; $i < count($statements); $i++)
        {
            $input_string = str_replace($statements[$i], '', $input_string);
        }

        $input_string = str_replace(' ', '_', $input_string);

        return $input_string;
    }

    static function EncodeAndClear($input_string)
    {
        $input_string = Translit::Encode($input_string);
        $input_string = Translit::ClearServiceCharacters($input_string);    

        return $input_string;
    }
}
