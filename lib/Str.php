<?php

class Str {

  static function endsWith($string, $substring) {
    $lenString = strlen($string);
    $lenSubstring = strlen($substring);
    $endString = substr($string, $lenString - $lenSubstring, $lenSubstring);
    return $endString == $substring;
  }

  static function startsWith($string, $substring) {
    $startString = substr($string, 0, strlen($substring));
    return $startString == $substring;
  }

  static function getCharAt($s, $index) {
    return mb_substr($s, $index, 1);
  }

  // assumes that the string is trimmed
  static function capitalize($s) {
    if (!$s) {
      return $s;
    }
    return mb_strtoupper(self::getCharAt($s, 0)) . mb_substr($s, 1);
  }

  static function randomString($length = 10) {
    $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
    $sigma = strlen($alphabet);

    $result = '';
    while ($length--) {
      $result .= $alphabet[rand(0, $sigma - 1)];
    }
    return $result;
  }

  static function markdown($s) {
    $s = trim($s);

    $fileName = tempnam(Config::TMP_DIR, 'tbd_');
    file_put_contents($fileName, $s);

    OS::execute("marked < {$fileName}", $output);

    @unlink($fileName);

    return $output;
  }

  static function formatNumber($n, int $decimals = 0) {
    $l = localeconv();
    return number_format($n, $decimals, $l['decimal_point'], $l['thousands_sep']);
  }
}
