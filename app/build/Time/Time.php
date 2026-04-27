<?php

namespace Bruder\Time;

use DateTime;

class Time
{

  /**
   * Returns a human-readable time difference from a timestamp to now.
   *
   * @param int|string $timestamp
   * @param bool $full
   * @return string
   */
  public static function ago(int|string $timestamp, bool $full = false)
  {
    /**
     * Convert unix timestamps to readable ones.
     */
    if (is_numeric($timestamp))
      $timestamp = date("Y-m-d H:i:s", $timestamp);

    $now = new DateTime;
    $ago = new DateTime($timestamp);
    $diff = $now->diff($ago);

    $diffString = [
      'y' => 'year',
      'm' => 'month',
      'd' => 'day',
      'h' => 'hour',
      'i' => 'minute',
      's' => 'second',
    ];

    foreach ($diffString as $key => &$value)
      if ($diff->$key)
        return $diff->$key . ($key == "i" ? "min" : ($key == "s" ? "sec" : $key)) . ($full ? " her" : "");

    return 'just now';
  }

  /**
   * Checks if a timestamp is older than a given number of days.
   *
   * @param string $timestamp
   * @param int $days
   * @return bool
   */
  public static function older_than(string $timestamp, int $days)
  {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($date);

    return $interval->days >= $days;
  }

  /**
   * @param int|string $timestamp
   * @param bool $exact_hours
   * @param bool $full
   * @return ?string
   */
  public static function left(int|string $timestamp, bool $exact_hours = false, bool $full = true)
  {
    /**
     * Evaluate type of timestamp
     */
    if (is_numeric($timestamp)) {
      $unix_timestamp = $timestamp;
    } else {
      $dateTime = new DateTime($timestamp);
      $unix_timestamp = $dateTime->getTimestamp();
    }

    $now = time();
    $difference = $unix_timestamp - $now;

    /**
     * Return null, if time is over.
     */
    if ($difference <= 0)
      return null;

    $intervals = array(
      'year' => 31536000,
      'month' => 2592000,
      'week' => 604800,
      'day' => 86400,
      'hour' => 3600,
      'minute' => 60
    );

    $futureString = '';

    foreach ($intervals as $interval => $seconds) {
      $count = floor($difference / $seconds);

      if ($count > 0) {
        if ($interval === 'hour')
          $futureString .= ($count == 1) ? "$count $interval" : "$count {$interval}s";
        else
          $futureString .= ($count == 1) ? "$count $interval" : "$count {$interval}s";

        $difference %= $seconds;

        // To show only one level of granularity (e.g., "1 day 2 hours" instead of "1 day 2 hours 30 minutes")
        break;
      }
    }

    if ($exact_hours) {
      $hours = floor($difference / 3600);
      if ($hours > 0) {
        $hour_s = $hours > 1 ? "hours" : "hour";
        $futureString .= ($futureString === '') ? "$hours $hour_s" : ", $hours $hour_s";
        $difference %= 3600;
      }
    }

    return ($futureString === "") ? "Less than a minute" : $futureString . ($full ? " left" : "");
  }

  /**
   * Determine if a timestamp is older than x days
   *
   * @param string $timestamp The timestamp to compare with.
   * @param int $days The amount of days to compare with.
   * @param bool $consider_seconds Whether or not to consider seconds.
   * @return bool True or false.
   */
  public static function has_passed_since(string|int $timestamp, int $days = 30, bool $consider_seconds = false)
  {
    /**
     * Convert unix timestamp
     */
    if (is_numeric($timestamp))
      $timestamp = date("Y-m-d H:i:s", $timestamp);

    $currentDate = new DateTime();
    $inputDate = DateTime::createFromFormat("Y-m-d H:i:s", $timestamp);

    $interval = $inputDate->diff($currentDate);
    $daysDifference = $interval->days;

    $return = $daysDifference >= $days;

    if ($consider_seconds)
      if (!$return && $interval->f > 0)
        $return = false;

    return $return;
  }


  /**
   * Function to check, if a timestamp is older than the passed
   * time to have passed.
   *
   * @param string $timestamp
   * @param string $time_to_have_passed | e-g. `+30 minutes`
   * @return bool
   */
  public static function has_passed($timestamp, $time_to_have_passed)
  {

    /**
     * Convert the timestamp passed, whether it's unix or string
     * to a DateTime object.
     */
    $timestamp = is_numeric($timestamp) ? new DateTime("@$timestamp") : new DateTime($timestamp);

    /**
     * Add the time to pass onto the the passed timestamp
     */
    $timestamp->modify($time_to_have_passed);

    return $timestamp < new DateTime('now');
  }


  public static function has_passed_since_return_time($timestamp1, $timestamp2, $daysToPass)
  {
    // Convert timestamps to DateTime objects
    $dateTime1 = new DateTime($timestamp1);
    $dateTime2 = new DateTime($timestamp2);

    // Calculate the time difference
    $timeDifference = $dateTime2->getTimestamp() - $dateTime1->getTimestamp();

    // Calculate the remaining time in seconds
    $remainingTime = $daysToPass * 24 * 60 * 60 - $timeDifference;

    // Calculate the remaining timestamp
    $remainingTimestamp = $dateTime2->getTimestamp() + $remainingTime;

    // Format the remaining timestamp
    $remainingDateTime = new DateTime();
    $remainingDateTime->setTimestamp($remainingTimestamp);
    $formattedRemainingTimestamp = $remainingDateTime->format('Y-m-d H:i:s');

    return $formattedRemainingTimestamp;
  }
}
