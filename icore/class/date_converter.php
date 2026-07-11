<?php
<<<<<<< HEAD

=======
>>>>>>> 5591029... some change
class DateConverter extends jDateTime
{
    protected string $date;
    protected string $language;

    public function __construct(string $date, string $language = 'en')
    {
        $this->date = $date;
        $this->language = $language;
    }

    public function convertToShamsi(): string
    {
<<<<<<< HEAD
        $timestamp = strtotime($this->date);

        if ($timestamp === false) {
            return '';
=======
        if ($this->language == 'fa') {
            return $this->isPersian() ? $this->date : jDateTime::date('H:i:s  Y/m/d', strtotime($this->date));
        } else {
            return date('Y/m/d H:i:s', strtotime($this->date));
>>>>>>> 5591029... some change
        }

        if ($this->language === 'fa') {
            return $this->isPersian()
                ? $this->date
                : jDateTime::date('H:i:s  Y/m/d', $timestamp);
        }

        return date('Y/m/d H:i:s', $timestamp);
    }

    public function getDayDifference(): int
    {
        $timestamp = strtotime($this->date);

        if ($timestamp === false) {
            return 0;
        }

        $todayTimestamp = strtotime('today');

        return (int) floor(($todayTimestamp - $timestamp) / 86400);
    }

    public function getWeekday(): string
    {
        $timestamp = strtotime($this->date);

        return $timestamp === false ? '' : date('l', $timestamp);
    }

    public function getMonthName(): string
    {
        $timestamp = strtotime($this->date);

        return $timestamp === false ? '' : date('F', $timestamp);
    }

    public function getMonthNameInPersian(): string
    {
        $timestamp = strtotime($this->date);

        if ($timestamp === false) {
            return '';
        }

        if ($this->language !== 'fa') {
            return date('F', $timestamp);
        }

        $monthNames = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];

        $monthNumber = (int) jDateTime::date('m', $timestamp);

        return $monthNames[$monthNumber] ?? '';
    }

    public function getWeekdayInPersian(): string
    {
        $timestamp = strtotime($this->date);

        if ($timestamp === false) {
            return '';
        }

        $weekday = date('l', $timestamp);

        if ($this->language !== 'fa') {
            return $weekday;
        }

        $weekdays = [
            'Saturday' => 'شنبه',
            'Sunday' => 'یک‌شنبه',
            'Monday' => 'دوشنبه',
            'Tuesday' => 'سه‌شنبه',
            'Wednesday' => 'چهارشنبه',
            'Thursday' => 'پنج‌شنبه',
            'Friday' => 'جمعه',
        ];

        return $weekdays[$weekday] ?? '';
    }

    protected function isPersian(): bool
    {
        return preg_match('/\p{Arabic}/u', $this->date) === 1;
    }

    public static function convertNumbers(string $value, string $target = 'fa'): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return $target === 'fa'
            ? str_replace($english, $persian, $value)
            : str_replace($persian, $english, $value);
    }
}

?>