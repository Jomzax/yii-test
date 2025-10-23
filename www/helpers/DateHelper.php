<?php

namespace app\helpers;

class DateHelper
{
    /**
     * แปลงวันที่ให้เป็นรูปแบบไทย (เช่น 20/10/2568 15:45)
     */
    public static function DateThaiTime($date)
    {
        if ($date === null || $date === '') {
            return '-';
        }

        // ✅ รองรับกรณีเป็น MongoDB\BSON\UTCDateTime โดยไม่อ้างอิง type ตรง ๆ
        if (is_object($date)
            && is_a($date, '\MongoDB\BSON\UTCDateTime', false)
            && method_exists($date, 'toDateTime')) {
            $date = $date->toDateTime()->format('Y-m-d H:i:s');
        }

        // ✅ แปลง string/timestamp เป็น timestamp เดียว
        $ts = is_numeric($date) ? (int)$date : strtotime((string)$date);
        if (!$ts) {
            return '-';
        }

        $thaiYear = (int)date('Y', $ts) + 543;
        return date('d/m/', $ts) . $thaiYear . ' ' . date('H:i', $ts);
    }

    /**
     * แปลงวันที่ไทยแบบสั้น (20/10/2568)
     */
    public static function DateThai($date)
    {
        if ($date === null || $date === '') {
            return '-';
        }

        if (is_object($date)
            && is_a($date, '\MongoDB\BSON\UTCDateTime', false)
            && method_exists($date, 'toDateTime')) {
            $date = $date->toDateTime()->format('Y-m-d');
        }

        $ts = is_numeric($date) ? (int)$date : strtotime((string)$date);
        if (!$ts) {
            return '-';
        }

        $thaiYear = (int)date('Y', $ts) + 543;
        return date('d/m/', $ts) . $thaiYear;
    }
}
