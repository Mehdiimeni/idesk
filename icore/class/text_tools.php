<?php

class TextTools
{
<<<<<<< HEAD
    private static ?TextTools $instance = null;
=======
    private static $instance = null;
>>>>>>> 5591029... some change

    private function __construct()
    {
    }

    public static function getInstance(): TextTools
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

<<<<<<< HEAD
    /**
     * Truncate text without cutting words.
     */
    public function truncateText(?string $text, int $length = 150, string $ellipsis = '...'): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }

        $truncateText = mb_substr($text, 0, $length, 'UTF-8');

        $lastSpace = mb_strrpos($truncateText, ' ', 0, 'UTF-8');
=======
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
>>>>>>> 5591029... some change

        if ($lastSpace !== false) {
            $truncateText = mb_substr($truncateText, 0, $lastSpace, 'UTF-8');
        }

        return $truncateText . $ellipsis;
    }

<<<<<<< HEAD
    /**
     * Capitalize first character.
     */
    public function capitalizeFirstLetter(string $word): string
    {
        if ($word === '') {
            return '';
        }

        $first = mb_strtoupper(
            mb_substr($word, 0, 1, 'UTF-8'),
            'UTF-8'
        );

        return $first . mb_substr($word, 1, null, 'UTF-8');
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Cannot unserialize TextTools singleton.');
    }
}
=======

    public function capitalizeFirstLetter($word)
    {
        if (strlen($word) == 0) {
            return $word;
        }

        $firstLetter = strtoupper($word[0]);

        if (strlen($word) == 1) {
            return $firstLetter;
        }
>>>>>>> 5591029... some change

        $restOfWord = substr($word, 1);
        return $firstLetter . $restOfWord;
    }
}
