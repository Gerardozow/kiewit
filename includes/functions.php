<?php
$errors = array();

/*--------------------------------------------------------------*/
/* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str)
{
  global $con;
  $escape = mysqli_real_escape_string($con, $str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str)
{
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str)
{
  $val = str_replace('-', " ", $str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var)
{
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if (isset($val) && $val == '') {
      $errors = $field . " No puede estar vacio.";
      return $errors;
    }
  }
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg = array())
{
  $output = '';
  if (!empty($msg)) {
    foreach ($msg as $key => $value) {
      $output .= "<div class=\"alert alert-{$key} alert-dismissible fade show\">";
      $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
      $output .= remove_junk(first_character($value));
      $output .= "</div>";
    }
    return $output;
  } else {
    return "";
  }
}

/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
  if (headers_sent() === false) {
    header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
  }

  exit();
}
/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function make_date($timestamp = null)
{
  if ($timestamp === null) {
    $timestamp = time();
  }
  // Establecer el huso horario a México
  date_default_timezone_set('America/Mexico_City');
  return date("Y-m-d H:i:s", $timestamp);
}


/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/

function page_active($separador)
{
  $page = $GLOBALS['page'];
  if ($separador == $page) {
    $mensaje = 'active';
    return $mensaje;
  }
}
/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function menu_open($menu_active)
{
  $menu = $GLOBALS['separador'];
  if ($menu_active == $menu) {
    $mensaje = 'menu-open active';
    return $mensaje;
  }
}


/*--------------------------------------------------------------*/
/* Function password validate
/*--------------------------------------------------------------*/
function password_validate($password, $confirm_password)
{
  global $errors;
  if (remove_junk($_POST[$password]) != remove_junk($_POST[$confirm_password])) {
    $errors = "No coiciden las contraseñas";
    return $errors;
  }
}

/*--------------------------------------------------------------*/
/* Function validar registro de usuario
/*--------------------------------------------------------------*/
function username_validation($username)
{
  global $errors;

  $exist = find_username($_POST[$username]);
  if ($exist === true) {
  } else {
    $errors = 'Usuario ya existe.';
    return $errors;
  }
}
/*--------------------------------------------------------------*/
/* Function validar registro de email
/*--------------------------------------------------------------*/
function email_validation($email)
{
  global $errors;

  $exist = find_email($_POST[$email]);
  if ($exist === true) {
  } else {
    $errors = 'Email ya registrado.';
    return $errors;
  }
}
