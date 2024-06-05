<?php

namespace twcli;

class cli
{
    private $colors = [
        'bg' => [
            'black' => 40,
            'white' => 48,
            'gray'  => 100,
            'red'   => 41,
            'green' => 42,
            'yellow' => 43,
            'blue' => 44,
            'purple' => 45,
            'cyan' => 46,
        ],
        'text' => [
            'white' => 29,
            'gray'  => 30,
            'red'   => 31,
            'green' => 32,
            'yellow' => 33,
            'blue' => 34,
            'purple' => 35,
            'cyan' => 36,


            'i' => 3,
            'u' => 4,
            't' => 28,
        ],
    ];


    static function print(string $text)
    {
        return (new CLI)->pri($text);
    }


    static function table(array $table)
    {
        return (new CLI)->tbl($table);
    }






    function all()
    {
        for ($i = 0; $i < 500; $i++) {
            echo "\e[{$i}m Hello world {$i}\n";
        }
    }


    private function getColor(string $alias, $bg = false)
    {
        $index = isset($this->colors[$bg ? 'bg' : 'text'][$alias]) ? $this->colors[$bg ? 'bg' : 'text'][$alias] : 0;
        return "\e[{$index}m";
    }


    private function getStyle(string $tag): string|false
    {
        $bg = false;
        $close = false;
        if (str_starts_with($tag, '/')) {
            return false;
        }

        if (str_starts_with($tag, 'bg-')) {
            $tag = substr($tag, 3);
            $bg = true;
        }

        return $this->getColor($tag, $bg);
    }


    function pri(string $text)
    {
        $tags = [];

        $text = preg_replace_callback(
            '/<(.*?)>/m',
            function ($matches) use (&$tags) {
                [$_, $tag] = $matches;

                if ($style = $this->getStyle($tag)) {
                    $tags[] = $tag;
                    return $style;
                }

                $tags = array_slice($tags, 0, -1);
                $tag = end($tags);
                return $this->getStyle($tag);
            },
            $text
        );

        echo $text;
    }


    function tbl(array $array)
    {
        $columns = array_keys($array[0]);
        $columnWidths = array_map(function ($col) use ($array) {
            return max(array_map('strlen', array_column($array, $col))) + 2;
        }, $columns);

        $rowFormat = '';
        foreach ($columnWidths as $width) {
            $rowFormat .= "%-{$width}s";
        }
        $rowFormat .= PHP_EOL;

        $header = vsprintf($rowFormat, $columns);

        $separator = '';
        foreach ($columnWidths as $width) {
            $separator .= str_repeat('-', $width);
        }
        $separator .= PHP_EOL;

        $rows = '';
        foreach ($array as $row) {
            $rows .= vsprintf($rowFormat, $row);
        }

        $table = $header . $separator . $rows;

        echo $table;
    }
}
