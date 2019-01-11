<?php

class Session {

  const ONE_MONTH_IN_SECONDS = 30 * 86400;
  const ONE_YEAR_IN_SECONDS = 365 * 86400;

  static function init() {
    if (isset($_COOKIE[session_name()])) {
      session_start();
    }
    if (Request::isWeb()) {
      self::setActiveUser();
    }
    // Otherwise we're being called by a local script, not a web-based one.
  }

  static function login($user, $remember = false) {
    self::set('userId', $user->id);
    if ($remember) {
      $cookie = Cookie::create($user->id);
      setcookie('login', $cookie->cookieString, time() + self::ONE_YEAR_IN_SECONDS, '/');
    }

    User::setActive($user->id); // for logging purposes only
    Log::info('Logged in, IP=' . $_SERVER['REMOTE_ADDR']);
    Util::redirect(Core::getWwwRoot());
  }

  static function logout() {
    Log::info('Logged out, IP=' . $_SERVER['REMOTE_ADDR']);
    $cookieString = $_COOKIE['login'] ?? '';
    $cookie = Cookie::get_by_cookieString($cookieString);
    if ($cookie) {
      $cookie->delete();
    }
    self::unsetCookie('login');
    unset($_COOKIE['login']);
    self::kill();
    Util::redirect(Core::getWwwRoot());
  }

  // Try to load logging information from the long-lived cookie
  static function loadUserFromCookie() {
    $lll = $_COOKIE['login'] ?? null;
    if ($lll) {
      $cookie = Cookie::get_by_cookieString($lll);
      $user = $cookie ? User::get_by_id($cookie->userId) : null;
      if ($user) {
        self::set('userId', $user->id);
        User::setActive($user->id);
      } else {
        // The cookie is invalid.
        self::unsetCookie('login');
        unset($_COOKIE['login']);
        if ($cookie) {
          $cookie->delete();
        }
      }
    }
  }

  static function setActiveUser() {
    if ($userId = self::get('userId')) {
      User::setActive($userId);
    } else {
      self::loadUserFromCookie();
    }
  }

  static function get($name, $default = null) {
    if (isset($_SESSION)){
      if (array_key_exists($name, $_SESSION)) {
        return $_SESSION[$name];
      }
    }
    return $default;
  }

  static function set($var, $value) {
    // Lazy start of the session so we don't send a PHPSESSID cookie unless we have to
    if (!isset($_SESSION)) {
      session_start();
    }
    $_SESSION[$var] = $value;
  }

  static function unsetVar($var) {
    if (isset($_SESSION)) {
      unset($_SESSION[$var]);
      if (!count($_SESSION)) {
        // Note that this will prevent us from creating another session this same request.
        // This does not seem to cause a problem at the moment.
        self::kill();
      }
    }
  }

  static function unsetCookie($name) {
    unset($_COOKIE[$name]);
    setcookie($name, '', time() - 3600, '/');
  }

  static function has($var) {
    return isset($_SESSION) && isset($_SESSION[$var]);
  }

  static function kill() {
    if (!isset($_SESSION)) {
      session_start(); // It has to have been started in order to be destroyed.
    }
    session_unset();
    @session_destroy();
    if (ini_get("session.use_cookies")) {
      self::unsetCookie(session_name());
    }
  }

}
