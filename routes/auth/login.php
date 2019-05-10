<?php

Util::assertNotLoggedIn();

$email = Request::get('email');
$password = Request::get('password');
$remember = Request::has('remember');
$referer = Request::get('referer', $_SERVER['HTTP_REFERER'] ?? null);
$submitButton = Request::has('submitButton');

$fakeEmail = Request::get('fakeEmail');

if ($fakeEmail) {
  if (!Config::DEVELOPMENT_MODE) {
    FlashMessage::add('Conectarea cu utilizatori de test este permisă doar în development.');
    Util::redirect('login');
  }
  $user = User::get_by_email($fakeEmail);
  if (!$user) {
    $user = Model::factory('User')->create();
  }
  $user->email = $fakeEmail;
  $user->save();
  Session::login($user, true, $referer);
}

if ($submitButton) {
  $user = validate($email, $password, $errors);

  if ($user) {
    Session::login($user, $remember, $referer);
  } else {
    Smart::assign(['errors' => $errors]);
  }
}

if (Config::DEVELOPMENT_MODE) {
  Smart::assign(['allowFakeLogins' => true]);
}

Smart::assign([
  'email' => $email,
  'remember' => $remember,
  'referer' => $referer,
]);
Smart::display('auth/login.tpl');

/*************************************************************************/

// returns a user upon successful credentials, null otherwise
function validate($email, $password, &$errors) {
  $errors = [];

  if (!$email) {
    $errors['email'][] = _('Please enter an email address.');
  }

  if (!$password) {
    $errors['password'][] = _('Please enter a password.');
  }

  $user = null;
  if ($email && $password) {
    $user = User::get_by_email_password($email, md5($password));
    if (!$user) {
      $errors['password'][] = _('Incorrect email address or password.');
    }
  }

  return $user;
}
