<?php
class TextTools
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function truncateText($text, $length = 150, $ellipsis = '...')
    {
        if ($text === null) {
            return '';
        }

        if (strlen($text) <= $length) {
            return $text;
        }

        $truncateText = substr($text, 0, $length);
        $lastSpace = strrpos($truncateText, ' ');

        if ($lastSpace !== false) {
            $truncateText = substr($truncateText, 0, $lastSpace);
        }

        return $truncateText . $ellipsis;
    }


    public function capitalizeFirstLetter($word)
    {
        if (strlen($word) == 0) {
            return $word;
        }

        $firstLetter = strtoupper($word[0]);

        if (strlen($word) == 1) {
            return $firstLetter;
        }

        $restOfWord = substr($word, 1);
        return $firstLetter . $restOfWord;
    }
}
