<?php
require_once('./includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(1);


if (isset($_POST['add_user'])) {

  $req_fields = array('username','name','last_name','group','email','password','confirm_password');
  validate_fields($req_fields);

  password_validate('password','confirm_password');
  username_validation('username');
  email_validation('email');
  if (empty($errors)) {
    echo 'Sin errores';
    $username = remove_junk($db->escape($_POST['username']));
    $name = remove_junk($db->escape($_POST['name']));
    $last_name =remove_junk($db->escape($_POST['last_name']));
    $group = remove_junk($db->escape($_POST['group']));
    $email = remove_junk($db->escape($_POST['email']));
    $password = remove_junk($db->escape($_POST['password']));
    $password = sha1($password);
    $query = "INSERT INTO users (";
    $query .= "name,last_name,username,password,email,user_level,status";
    $query .= ") VALUES (";
    $query .= " '{$name}','{$last_name}', '{$username}', '{$password}', '{$email}', '{$group}','0'";
    $query .= ")";
    if ($db->query($query)) {
      //sucess
      $session->msg('s', "¡La cuenta de usuario ha sido creada!");
      redirect('usuarios.php', false);
    } else {
      //failed
      $session->msg('d', 'Lo sentimos, ¡no se ha podido crear la cuenta!');
      redirect('usuarios.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('usuarios.php', false);
  } 
}
