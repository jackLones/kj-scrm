<?php
	/**
	 * Created by PhpStorm.
	 * User: Dove Chen
	 * Date: 19-09-07
	 * Time: 上午09:04
	 */

	namespace app\util;

	use app\components\InvalidParameterException;
	use DateTime;
	use DateTimeZone;

	class DateUtil
	{

		const YEAR_DATE_FORMAT = 'Y';
		const MONTH_DATE_FORMAT = 'm';
		const DAY_DATE_FORMAT = 'd';
		const HOUR_DATE_FORMAT = 'H';
		const MINUTE_DATE_FORMAT = 'i';
		const SECOND_DATE_FORMAT = 's';
		const SHORTEST_DATE_FORMAT = 'Ymd';
		const SHORT_DATE_FORMAT = 'Y-m-d';
		const FULL_DATE_FORMAT = 'Y-m-d H:i:s';
		const SHORT_DATE_TIME_FORMAT = 'YmdHis';

		public static function calculateAge ($birthDay)
		{
			if (empty($birthDay)) {
				return 0;
			}

			return date_diff(date_create($birthDay), date_create('now'))->y;
		}

		public static function getYear ($time = NULL)
		{
			if (empty($time)) {
				return date(static::YEAR_DATE_FORMAT);
			} else {
				return date(static::YEAR_DATE_FORMAT, $time);
			}
		}

		public static function getMonth ($time = NULL)
		{
			if (empty($time)) {
				return date(static::MONTH_DATE_FORMAT);
			} else {
				return date(static::MONTH_DATE_FORMAT, $time);
			}
		}

		public static function getDay ($time = NULL)
		{
			if (empty($time)) {
				return date(static::DAY_DATE_FORMAT);
			} else {
				return date(static::DAY_DATE_FORMAT, $time);
			}
		}

		public static function getHour ($time = NULL)
		{
			if (empty($time)) {
				return date(static::HOUR_DATE_FORMAT);
			} else {
				return date(static::HOUR_DATE_FORMAT, $time);
			}
		}

		public static function getMinute ($time = NULL)
		{
			if (empty($time)) {
				return date(static::MINUTE_DATE_FORMAT);
			} else {
				return date(static::MINUTE_DATE_FORMAT, $time);
			}
		}

		public static function getSecond ($time = NULL)
		{
			if (empty($time)) {
				return date(static::SECOND_DATE_FORMAT);
			} else {
				return date(static::SECOND_DATE_FORMAT, $time);
			}
		}

		public static function getCurrentShortYMD ()
		{
			return date(static::SHORTEST_DATE_FORMAT);
		}

		public static function getCurrentYMD ()
		{
			return date(static::SHORT_DATE_FORMAT);
		}

		public static function getCurrentTime ()
		{
			return date(static::FULL_DATE_FORMAT);
		}

		public static function getCurrentShortTime ()
		{
			return date(static::SHORT_DATE_TIME_FORMAT);
		}

		public static function getPreviousSecondsTime ($seconds = 1, $ymd = NULL)
		{
			if (is_null($ymd)) {
				$ymd = static::getCurrentTime();
			}

			$dateObj = date_create($ymd);
			date_sub($dateObj, date_interval_create_from_date_string($seconds . " seconds"));

			return date_format($dateObj, static::FULL_DATE_FORMAT);
		}

		public static function getNextSecondsTime ($seconds = 1, $ymd = NULL)
		{
			if (is_null($ymd)) {
				$ymd = static::getCurrentTime();
			}

			$dateObj = date_create($ymd);
			date_add($dateObj, date_interval_create_from_date_string($seconds . " seconds"));

			return date_format($dateObj, static::FULL_DATE_FORMAT);
		}

		public static function isToday ($date)
		{
			$tmp = date_create($date);

			return static::getCurrentYMD() == date_format($tmp, static::SHORT_DATE_FORMAT);
		}

		public static function isSameDay ($d1, $d2)
		{
			$tmp1 = date_create($d1);
			$tmp2 = date_create($d2);

			return date_format($tmp1, static::SHORT_DATE_FORMAT) == date_format($tmp2, static::SHORT_DATE_FORMAT);
		}

		/*
		 * @param date $ymd
		 */
		public static function getPreviousYMD ($day = 1, $ymd = NULL)
		{
			if (is_null($ymd)) {
				$ymd = static::getCurrentYMD();
			}
			$date = date_create($ymd);
			date_sub($date, date_interval_create_from_date_string($day . " days"));

			return date_format($date, static::SHORT_DATE_FORMAT);
		}

		public static function getNextYMD ($ymd = NULL, $day = 1)
		{
			if (is_null($ymd)) {
				$ymd = static::getCurrentYMD();
			}

			$date = date_create($ymd);
			date_add($date, date_interval_create_from_date_string($day . " days"));

			return date_format($date, static::SHORT_DATE_FORMAT);
		}

		public static function dateAddSeconds ($date, $interval)
		{
			$obj = date_create($date);
			date_add($obj, date_interval_create_from_date_string($interval . " seconds"));

			return date_format($obj, static::FULL_DATE_FORMAT);
		}

		public static function dateDiffSeconds ($d1, $d2)
		{
			if (static::isTimestamp($d1)) {
				$d1 = static::getFormattedTime($d1);
			}

			if (static::isTimestamp($d2)) {
				$d2 = static::getFormattedTime($d2);
			}

			return strtotime($d2) - strtotime($d1);
		}

		public static function isTimestamp ($timestamp)
		{
			if (count(explode('-', $timestamp)) > 1) {
				return false;
			} else {
				return strtotime(date(static::FULL_DATE_FORMAT, $timestamp)) == $timestamp;
			}
		}

		public static function getFormattedYMD ($ymd)
		{
			if (static::isTimestamp($ymd)) {
				$unixTime = $ymd;
			} else {
				$unixTime = strtotime($ymd);
			}
			if (!$unixTime)
				return false;

			return date(static::SHORT_DATE_FORMAT, $unixTime);
		}

		public static function getFormattedTime ($ymd)
		{
			if (static::isTimestamp($ymd)) {
				$unixTime = $ymd;
			} else {
				$unixTime = strtotime($ymd);
			}
			if (!$unixTime)
				return false;

			return date(static::FULL_DATE_FORMAT, $unixTime);
		}

		public static function getNoDashFormattedYMD ($ymd = NULL)
		{
			if (is_null($ymd)) {
				$ymd = static::getCurrentYMD();
			}

			$unixTime = strtotime($ymd);
			if (!$unixTime)
				return false;

			return date(static::SHORTEST_DATE_FORMAT, $unixTime);
		}

		public static function getMaxTimeInDay ($ymd)
		{
			return date(DateUtil::FULL_DATE_FORMAT, strtotime($ymd . ' 23:59:59'));
		}

		public static function getMinTimeInDay ($ymd)
		{
			return date(DateUtil::FULL_DATE_FORMAT, strtotime($ymd . ' 00:00:00'));
		}

		public static function getTimezoneOffset ($strTimezone)
		{
			$timeZone    = new DateTimeZone($strTimezone);
			$datetimeNow = new DateTime('now', $timeZone);

			return $datetimeNow->format('P');
		}

		/**
		 * @param      $date1
		 * @param null $date2
		 *
		 * @return string
		 * @throws InvalidParameterException
		 */
		public static function getDiffText ($date1, $date2 = NULL)
		{
			if (is_null($date2)) {
				$date2 = static::getCurrentTime();
			}

			$diffSeconds = static::dateDiffSeconds($date1, $date2);

			if ($diffSeconds < 0) {
				throw new InvalidParameterException("date two must abort date one");
			} elseif ($diffSeconds == 0) {
				return '刚刚';
			} elseif ($diffSeconds < 1 * 60) {
				return $diffSeconds . '秒前';
			} else {
				$minute = floor($diffSeconds / 60);

				if ($minute < 60) {
					return $minute . "分钟前";
				} else {
					$hour = floor($minute / 60);

					if ($hour < 24) {
						return $hour . '小时前';
					} else {
						$day = floor($hour / 24);

						if ($day < 365) {
							return $day . '天前';
						} else {
							$year = floor($day / 365);

							return $year . '年前';
						}
					}
				}
			}
		}

		/**
		 * 获取差别天数
		 *
		 * @param      $date1
		 * @param null $date2
		 *
		 * @return float
		 */
		public static function getDiffDay ($date1, $date2 = NULL)
		{
			if (is_null($date2)) {
				$date2 = static::getCurrentTime();
			}

			$diffSeconds = static::dateDiffSeconds($date1, $date2);

			$day = floor($diffSeconds / 60 / 60 / 24);

			return $day;
		}

		/**
		 * @param $timeString
		 *
		 * @return string
		 */
		public static function getTextFromSeconds ($timeString)
		{
			$hour = $minute = $seconds = 0;
			if (floor($timeString / 60 / 60) > 0) {
				$hour    = floor($timeString / 60 / 60);
				$minute  = floor($timeString / 60 - $hour * 60);
				$seconds = $timeString - $hour * 60 * 60 - $minute * 60;
			} elseif (floor($timeString / 60) > 0) {
				$minute  = floor($timeString / 60);
				$seconds = $timeString - $minute * 60;
			} else {
				$seconds = $timeString;
			}

			if ($hour == 0) {
				if ($minute == 0) {
					$result = $seconds . '秒';
				} elseif ($seconds == 0) {
					$result = $minute . '分钟';
				} else {
					$result = $minute . '分钟' . $seconds . '秒';
				}
			} elseif ($minute == 0) {
				if ($seconds == 0) {
					$result = $hour . '小时';
				} else {
					$result = $hour . '小时' . $seconds . '秒';
				}
			} elseif ($seconds == 0) {
				$result = $hour . '小时' . $minute . '分钟';
			} else {
				$result = $hour . '小时' . $minute . '分钟' . $seconds . '秒';
			}

			return $result;
		}

		/**
		 * 根据日期返回每一天
		 *
		 * @param $startdate 开始日期
		 * @param $enddate   结束日期
		 *
		 * @return string
		 */
		public static function getDateFromRange ($startdate, $enddate)
		{

			$stimestamp = strtotime($startdate);
			$etimestamp = strtotime($enddate);

			// 计算日期段内有多少天
			$days = ($etimestamp - $stimestamp) / 86400 + 1;

			// 保存每天日期
			$date = [];
			for ($i = 0; $i < $days; $i++) {
				$date[] = date('Y-m-d', $stimestamp + (86400 * $i));
			}

			return $date;
		}

		/**
		 * 根据日期返回每周的开始时间和结束时间
		 *
		 * @param $data 时间数组 例如 array(2019-10-01,2019-10-02);
		 *
		 * @return array
		 */
		public static function getWeekFromRange ($data)
		{
			$cc    = count($data);
			$data1 = [];
			$data2 = [];
			if (count($data) <= 7) {
				array_push($data1, $data[0]);
				array_push($data2, $data[count($data) - 1]);
			} else {
				for ($i = 0; $i < $cc; $i++) {
					if ($i % 7 == 0) {
						array_push($data1, $data[$i]);
					}
					if (($i + 1) % 7 == 0) {
						array_push($data2, $data[$i]);
					}

					if (($i + 1) == $cc && ($i + 1) % 7 != 0) {
						array_push($data2, $data[$i]);
					}

				}
			}
			$result['s_date'] = $data1;
			$result['e_date'] = $data2;

			return $result;
		}

		/**
		 * 获取最近的12个月和每月的第一天和最后一天
		 *
		 * @return array
		 */
		public static function getLastMonth ()
		{
			$result      = [];
			$currentTime = time();
			$cyear       = floor(date("Y", $currentTime));
			$cMonth      = floor(date("m", $currentTime));
			for ($i = 0; $i < 12; $i++) {
				$nMonth   = $cMonth - $i;
				$cyear    = $nMonth == 0 ? ($cyear - 1) : $cyear;
				$nMonth   = $nMonth <= 0 ? 12 + $nMonth : $nMonth;
				$date     = $cyear . "-" . $nMonth . "-1";
				$firstday = date('Y-m-01', strtotime($date));
				$lastday  = date('Y-m-t', strtotime($date));
				if ($nMonth < 10) {
					$nMonth = '0' . $nMonth;
				}
				$result[$i]['time']     = $cyear . "/" . $nMonth;
				$result[$i]['firstday'] = $firstday;
				$result[$i]['lastday']  = $lastday;
				$result[$i]['id']       = $i;
			}
			$last_names = array_column($result, 'id');
			array_multisort($last_names, SORT_DESC, $result);

			return $result;
		}

		/**
		 * 获取最近的12个月不包含当月
		 *
		 */
		public static function getMoreMonth ()
		{
			$time        = [];
			$currentTime = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
			$cyear       = floor(date("Y", $currentTime));
			$cMonth      = floor(date("m", $currentTime));
			for ($i = 0; $i < 12; $i++) {
				$nMonth         = $cMonth - $i;
				$cyear          = $nMonth == 0 ? ($cyear - 1) : $cyear;
				$nMonth         = $nMonth <= 0 ? 12 + $nMonth : $nMonth;
				$time[]['time'] = $cyear . '/' . $nMonth;
			}

			return $time;
		}

		/**
		 * 获取最近7天的每一天数据
		 *
		 * @return array
		 */
		public static function get_weeks ($time = '', $format = 'Y-m-d', $num = 7)
		{
			$time = $time != '' ? $time : time();
			//组合数据
			$date = [];
			for ($i = 1; $i <= $num; $i++) {
				$date[$i] = date($format, strtotime('+' . $i - $num . ' days', $time));
			}

			return $date;
		}

		//获取百分比
		public static function getPer ($count1, $count2, $type = 0)
		{
			$num = '0.0%';
			if ($type == 1) {
				$num = '0.00%';
			}
			if ($count2 > 0) {
				$num = round($count1 / $count2, 3);
				if ($type == 1) {
					$num = sprintf("%.2f", $num * 100);
				} else {
					$num = sprintf("%.1f", $num * 100);
				}
				$num = $num . '%';
			}

			return $num;
		}

		//将秒转换为友好的方式
        public static function getHumanFormatBySecond($second){
		    $format = '';
		    if ($second) {

		        $hour = floor($second/3600);

		        $second = $second%3600;
		        $min = floor($second/60);

                $second = $second%60;

		        $format = ($hour ? $hour . "小时" : '') . ($min ? $min . '分钟' : '') . ($second ? $second . '秒' : '');
            }else{
                $format = '0秒';
            }
		    return $format;
        }
	}
