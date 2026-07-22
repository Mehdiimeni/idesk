<?php
class DateConverter extends jDateTime
{
    protected $date;
    protected $language;

    public function __construct($date, $language = 'en')
    {
        $this->date = $date;
        $this->language = $language;
    }

    public function convertToShamsi()
    {
        if ($this->language == 'fa') {
            return $this->isPersian() ? $this->date : jDateTime::date('H:i:s  Y/m/d', strtotime($this->date));
        } else {
            return date('Y/m/d H:i:s', strtotime($this->date));
        }
    }

    public function getDayDifference()
    {
        $timestamp = strtotime($this->date);
        $todayTimestamp = strtotime('today');
        $difference = floor(($todayTimestamp - $timestamp) / (60 * 60 * 24));
        return $difference;
    }

    public function getWeekday()
    {
        return date('l', strtotime($this->date));
    }

    public function getMonthName()
    {
        return date('F', strtotime($this->date));
    }

    public function getMonthNameInPersian()
    {
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
            12 => 'اسفند'
        ];

        $monthNumber = $this->convertNumbers(jDateTime::date('m', strtotime($this->date)),'fa');
        return($this->language == 'fa') ? $monthNames[$monthNumber] : date('F', strtotime($this->date));
    }



    public function getWeekdayInPersian()
    {
        $weekdays = [
            'Saturday' => 'شنبه',
            'Sunday' => 'یک‌شنبه',
            'Monday' => 'دوشنبه',
            'Tuesday' => 'سه‌شنبه',
            'Wednesday' => 'چهارشنبه',
            'Thursday' => 'پنج‌شنبه',
            'Friday' => 'جمعه',
        ];

        return($this->language == 'fa') ? $weekdays[date('l', strtotime($this->date))] : date('l', strtotime($this->date));
    }

    protected function isPersian()
    {
        return preg_match('/\p{Arabic}/u', $this->date);
    }


    private static function convertNumbers($matches,$lang)
    {
        $farsi_array = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        // اگر عدد فارسی بود، به انگلیسی تبدیل کن
        if ($lang == 'fa') {
            return str_replace($farsi_array, $english_array, $matches);
        } else {
            // در غیر این صورت، به فارسی تبدیل کن
            return str_replace($english_array, $farsi_array, $matches);
        }
    }
}

?>