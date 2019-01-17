<?php

class LocaleUtil {
  const COOKIE_NAME = 'language';

  static function init() {
    self::set(self::getCurrent());
  }

  static function getAll() {
    return Config::LOCALES;
  }

  // Returns the locale as dictated, in order of priority, by
  // 1. user cookie
  // 2. config file
  static function getCurrent() {
    $locale = Config::DEFAULT_LOCALE;

    $cookie = $_COOKIE[self::COOKIE_NAME] ?? null;
    if ($cookie && isset(Config::LOCALES[$cookie])) { // sanity check
      $locale = $cookie;
    }

    return $locale;
  }

  private static function set($locale) {
    mb_internal_encoding('UTF-8');

    // workaround for Windows lovers
    if (PHP_OS_FAMILY == 'Windows') {
      putenv("LC_ALL=$locale");
    }

    setlocale(LC_ALL, $locale);
    $domain = "messages";
    bindtextdomain($domain, Core::getRootPath() . '/locale');
    bind_textdomain_codeset($domain, 'UTF-8');
    textdomain($domain);
  }

  // changes the locale and stores it in a cookie
  static function change($id) {
    if (!isset(Config::LOCALES[$id])) {
      return;
    }

    // delete the existing cookie if it matches the config value
    if ($id == Config::DEFAULT_LOCALE) {
      Session::unsetCookie(self::COOKIE_NAME);
    } else {
      setcookie(self::COOKIE_NAME, $id, time() + Session::ONE_YEAR_IN_SECONDS, '/');
    }

    self::set($id);
  }

  // formats a number according to the current locale
  static function number($x, $decimals = 0) {
    $locale = localeconv();
    return number_format(
      $x, $decimals, $locale['decimal_point'], $locale['thousands_sep']);
  }

  static function date($timestamp, $format = "%e %b %Y") {
    return strftime($format, $timestamp);
  }

}