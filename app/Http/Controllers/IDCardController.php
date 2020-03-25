<?php

namespace App\Http\Controllers;

class IDCardController extends Controller
{
    /**
     * 根据身份证号码获取性别
     *
     * @param $IDCard
     *
     * @return string|int
     */
    public static function getSex($IDCard)
    {
        if (empty($IDCard)) {
            return '-';
        }

        $sexInt = (int) $IDCard[16];

        return $sexInt % 2 === 0 ? '女' : '男';
    }

    /**
     * 根据身份证号码获取生日
     *
     * @param $IDCard
     *
     * @return string
     */
    public static function getBirthday($IDCard): string
    {
        if (empty($IDCard)) {
            return '-';
        }

        $bir = substr($IDCard, 6, 8);

        $year = (int) substr($bir, 0, 4);

        $month = (int) substr($bir, 4, 2);

        $day = (int) substr($bir, 6, 2);

        return $year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $day);
    }

    /**
     * 根据身份证号码计算年龄
     *
     * @param  string  $IDCard
     *
     * @return false|float|int|string
     */
    public static function getAge($IDCard)
    {
        if (empty($IDCard)) {
            return '-';
        }

        // 获得出生年月日的时间戳
        $date = strtotime(substr($IDCard, 6, 8));
        // 获得今日的时间戳
        $today = strtotime('today');
        // 得到两个日期相差的大体年数
        $diff = floor(($today - $date) / 86400 / 365);

        // strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        return strtotime(substr($IDCard, 6, 8).' +'.$diff.'years') > $today ? ($diff + 1) : $diff;
    }

    /**
     * 判断字符串是否是身份证号
     *
     * @param  string  $IDCard
     *
     * @return bool
     */
    public static function isIdCard(string $IDCard): bool
    {
        // 转化为大写，如出现x
        $IDCard = strtoupper($IDCard);
        // 加权因子
        $wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $ai = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        // 按顺序循环处理前17位
        $sigma = 0;
        // 提取前17位的其中一位，并将变量类型转为实数
        for ($i = 0; $i < 17; $i++) {
            $b = (int) $IDCard{$i};
            // 提取相应的加权因子
            $w = $wi[$i];
            // 把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }

        // 计算序号
        $sidCard = $sigma % 11;
        // 按照序号从校验码串中提取相应的字符。
        $checkIdCard = $ai[$sidCard];

        return $IDCard{17} === $checkIdCard;
    }

    /**
     * 根据身份证号，返回对应的生肖
     *
     * @param  string  $IDCard
     *
     * @return string
     */
    public static function getZodiac(string $IDCard): string
    {
        if (empty($IDCard)) {
            return '-';
        }

        $start = 1901;
        $end = (int) substr($IDCard, 6, 4);
        $x = ($start - $end) % 12;

        $val = '';

        if ($x === 1 || $x === -11) {
            $val = '鼠';
        }

        if ($x === 0) {
            $val = '牛';
        }

        if ($x === 11 || $x === -1) {
            $val = '虎';
        }

        if ($x === 10 || $x === -2) {
            $val = '兔';
        }

        if ($x === 9 || $x === -3) {
            $val = '龙';
        }

        if ($x === 8 || $x === -4) {
            $val = '蛇';
        }

        if ($x === 7 || $x === -5) {
            $val = '马';
        }

        if ($x === 6 || $x === -6) {
            $val = '羊';
        }

        if ($x === 5 || $x === -7) {
            $val = '猴';
        }

        if ($x === 4 || $x === -8) {
            $val = '鸡';
        }

        if ($x === 3 || $x === -9) {
            $val = '狗';
        }

        if ($x === 2 || $x === -10) {
            $val = '猪';
        }

        return $val;
    }

    /**
     * 根据身份证号，返回对应的星座
     *
     * @param  string  $IDCard
     *
     * @return string
     */
    public static function getConstellation(string $IDCard): string
    {
        if (empty($IDCard)) {
            return '-';
        }

        $b = substr($IDCard, 10, 4);
        $m = (int) substr($b, 0, 2);
        $d = (int) substr($b, 2);

        $val = '';
        if (($m === 1 && $d <= 21) || ($m === 2 && $d <= 19)) {
            $val = '水瓶座';
        } elseif (($m === 2 && $d > 20) || ($m === 3 && $d <= 20)) {
            $val = '双鱼座';
        } elseif (($m === 3 && $d > 20) || ($m === 4 && $d <= 20)) {
            $val = '白羊座';
        } elseif (($m === 4 && $d > 20) || ($m === 5 && $d <= 21)) {
            $val = '金牛座';
        } elseif (($m === 5 && $d > 21) || ($m === 6 && $d <= 21)) {
            $val = '双子座';
        } elseif (($m === 6 && $d > 21) || ($m === 7 && $d <= 22)) {
            $val = '巨蟹座';
        } elseif (($m === 7 && $d > 22) || ($m === 8 && $d <= 23)) {
            $val = '狮子座';
        } elseif (($m === 8 && $d > 23) || ($m === 9 && $d <= 23)) {
            $val = '处女座';
        } elseif (($m === 9 && $d > 23) || ($m === 10 && $d <= 23)) {
            $val = '天秤座';
        } elseif (($m === 10 && $d > 23) || ($m === 11 && $d <= 22)) {
            $val = '天蝎座';
        } elseif (($m === 11 && $d > 22) || ($m === 12 && $d <= 21)) {
            $val = '射手座';
        } elseif (($m === 12 && $d > 21) || ($m === 1 && $d <= 20)) {
            $val = '魔羯座';
        }

        return $val;
    }
}
