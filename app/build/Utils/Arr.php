<?php

namespace Bruder\Utils;

class Arr
{
  /**
   * Sanitized a multi-dimensional array's values.
   *
   * @param array $array
   * @return array
   */
  public static function sanitize_special_chars(array $array)
  {
    $filtered = [];

    foreach ($array as $key => $value)
      if (is_array($value))
        $filtered[$key] = self::sanitize_special_chars($value);
      else
        $filtered[$key] = is_numeric($value)
          ? (int) $value
          : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);;

    return $filtered;
  }

  /**
   * Convert a given array to a JSON string.
   *
   * @param array $array The array to be converted to JSON format.
   * @return string The JSON representation of the input array.
   */
  public static function to_json(array $array)
  {
    $json = "{";
    $isFirst = true;

    foreach ($array as $key => $value) {
      if (!$isFirst) {
        $json .= ",";
      }

      $isFirst = false;
      $json .= '"' . addslashes($key) . '":';

      if (is_array($value)) {
        $json .= self::to_json($value);
      } elseif (is_string($value)) {
        $json .= '"' . addslashes($value) . '"';
      } elseif (is_bool($value)) {
        $json .= $value ? "true" : "false";
      } elseif (is_null($value)) {
        $json .= "null";
      } else {
        $json .= $value;
      }
    }

    $json .= "}";

    return $json;
  }

  /**
   * @param mixed $these
   * @return object
   */
  public static function objectify(mixed $these)
  {
    if (!$these) $these = (object) [];
    return json_decode(json_encode($these));
  }

  /**
   * Recursively search for a key in a multidimensional array.
   *
   * @param mixed $key
   * @param array $array
   * @return ?array The key value pair, if found.
   */
  public static function find_key(mixed $key, array $array)
  {
    foreach ($array as $k => $value) {
      if ($k === $key)
        return [$k => $value];

      if (is_array($value)) {
        $result = self::find_key($key, $value);

        if ($result)
          return $result;
      }
    }

    return null;
  }

  /**
   * Validate that all elements in a multidimensional array are numeric values.
   *
   * @param array $array The multidimensional array to validate.
   * @return bool Returns true if all elements are numeric, false otherwise.
   */
  public static function validate_numeric_values(array $array)
  {
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        if (!self::validate_numeric_values($value))
          return false;
      } else if (!is_numeric($value))
        return false;
    }

    return true;
  }

  /**
   * Recursively validate the structure of a given array of any
   * dimensional depth against a predefined one.
   *
   * @param array $array
   * @param array $expected
   * @return bool
   */
  public static function validate_structure_with_numeric_values(array $array, array $expected)
  {
    if (!is_array($array) || !is_array($expected))
      return false;

    foreach ($expected as $key => $value) {
      // Check if key exists in $array
      $real_key = is_array($value) ? $key : $value;

      if (!array_key_exists($real_key, $array))
        return false;



      // If $value is also an array, recursively check substructure
      if (is_array($value)) {
        // Ensure $array[$key] is also an array
        if (!is_array($array[$real_key]))
          return false;


        // Recursively check substructure
        if (!self::validate_structure_with_numeric_values($array[$real_key], $value))
          return false;
      } else

        // Otherwise, $value is expected to be numeric
        if (!is_numeric($array[$real_key]))
          return false;
    }

    return true;
  }


  /**
   * Compare the keys of a given array with a predefined array.
   *
   * @param array $sendArray The array to be checked.
   * @param array $predefinedArray The predefined array with required keys.
   * @return bool Returns true if $sendArray has all keys present in $predefinedArray, false otherwise.
   */
  public static function compare_keys(array $sendArray, array $predefinedArray): bool
  {
    $predefinedKeys = array_keys($predefinedArray);
    foreach ($predefinedKeys as $key)
      if (!array_key_exists($key, $sendArray))
        return false;

    return true;
  }

  /**
   * Iterate through an array and check, if it is missing any parameters
   * @return bool
   */
  public static function is_missing(array $list, array $arr): bool
  {
    foreach ($list as $key => $l)
      if (!array_key_exists($l, $arr)) return true;

    return false;
  }

  /**
   * Compares one array to another and removes any keys from the compared
   * one that do not exist in the `to` one.
   *
   * @return array|null Either an array with legit values or null.
   */
  public static function compare_and_remove(array $compare, array $to)
  {
    $compare = (array) $compare;

    foreach ($compare as $key => $c) {
      if (!in_array($c, $to))
        unset($compare[$key]);
    }

    return $compare ? $compare : null;
  }

  /**
   * Checking an array against another one, to make sure, it has atleast
   * one of the params in
   * @return bool
   */
  public static function has_any(array $compare, array $against)
  {
    $compare = (array) $compare;

    foreach ($compare as $key => $c)
      if (array_key_exists($c, $against)) return true;

    return false;
  }

  /**
   * Checks for an passed array to have atleast one param with a
   * valid value (!=0/null/'')
   * @return bool
   */
  public static function is_any(array $array)
  {
    $counter = 0;
    foreach ($array as $a)
      if ($a) $counter++;

    return $counter;
  }
}
