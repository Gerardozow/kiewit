<?php
//require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table)
{
  global $db;
  if (tableExists($table)) {
    return find_by_sql("SELECT * FROM " . $db->escape($table));
  }
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
  return $result_set;
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table, $id)
{
  global $db;
  $id = (int)$id;
  if (tableExists($table)) {
    $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
    if ($result = $db->fetch_assoc($sql))
      return $result;
    else
      return null;
  }
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_email($email)
{
  global $db;
  $sql = $db->query("SELECT id FROM users WHERE email='{$db->escape($email)}' LIMIT 1");
  if ($result = $db->fetch_assoc($sql))
    return $result;
  else
    return null;
}
/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table, $id)
{
  global $db;
  if (tableExists($table)) {
    $sql = "DELETE FROM " . $db->escape($table);
    $sql .= " WHERE id=" . $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
  }
}
/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/

function count_by_id($table)
{
  global $db;
  if (tableExists($table)) {
    $sql    = "SELECT COUNT(id) AS total FROM " . $db->escape($table);
    $result = $db->query($sql);
    return ($db->fetch_assoc($result));
  }
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table)
{
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $db->escape($table) . '"');
  if ($table_exit) {
    if ($db->num_rows($table_exit) > 0)
      return true;
    else
      return false;
  }
}
/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
function authenticate($username = '', $password = '')
{
  global $db;
  $username = $db->escape($username);
  $password = $db->escape($password);
  $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
  $result = $db->query($sql);
  if ($db->num_rows($result)) {
    $user = $db->fetch_assoc($result);
    $password_request = sha1($password);
    if ($password_request === $user['password']) {
      return $user['id'];
    }
  }
  return false;
}
/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
function authenticate_v2($username = '', $password = '')
{
  global $db;
  $username = $db->escape($username);
  $password = $db->escape($password);
  $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
  $result = $db->query($sql);
  if ($db->num_rows($result)) {
    $user = $db->fetch_assoc($result);
    $password_request = sha1($password);
    if ($password_request === $user['password']) {
      return $user;
    }
  }
  return false;
}


/*--------------------------------------------------------------*/
/* Find current log in user by session id
  /*--------------------------------------------------------------*/
function current_user()
{
  static $current_user;
  global $db;
  if (!$current_user) {
    if (isset($_SESSION['user_id'])) :
      $user_id = intval($_SESSION['user_id']);
      $current_user = find_by_id('users', $user_id);
    endif;
  }
  return $current_user;
}
/*--------------------------------------------------------------*/
/* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
function find_all_user()
{
  global $db;
  $results = array();
  $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
  $sql .= "g.group_name ";
  $sql .= "FROM users u ";
  $sql .= "LEFT JOIN user_groups g ";
  $sql .= "ON g.group_level=u.user_level ORDER BY u.name ASC";
  $result = find_by_sql($sql);
  return $result;
}
/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

function updateLastLogIn($user_id)
{
  global $db;
  $date = make_date();
  $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
  $result = $db->query($sql);
  return ($result && $db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Find all Group name
  /*--------------------------------------------------------------*/
function find_by_groupName($val)
{
  global $db;
  $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
  $result = $db->query($sql);
  return ($db->num_rows($result) === 0 ? true : false);
}
/*--------------------------------------------------------------*/
/* Find group level
  /*--------------------------------------------------------------*/
function find_by_groupLevel($level)
{
  global $db;
  $response = array();

  $sql = "SELECT group_status, group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
  $result = $db->query($sql);

  $response['num_rows'] = $db->num_rows($result);

  if ($response['num_rows'] > 0) {
    $groupData = $db->fetch_assoc($result);
    $response['group_status'] = $groupData['group_status'];
    $response['group_level'] = $groupData['group_level'];
  } else {
    $response['group_status'] = null;
    $response['group_level'] = null;
  }

  return $response;
}
/*--------------------------------------------------------------*/
/* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
function page_require_level($require_level)
{
  global $session;
  $current_user = current_user();
  $login_level = find_by_groupLevel($current_user['user_level']);
  //if user not login
  if (!$session->isUserLoggedIn(true)) :
    $session->msg('d', 'Please login...');
    redirect('index.php', false);
  //if Group status Deactive
  elseif ($login_level['group_status'] === '0') :
    $session->msg('d', 'Este nivel de usuario ha sido desactivado');
    redirect('home.php', false);
  //cheackin log in User level and Require level is Less than or equal to
  elseif ($current_user['user_level'] <= (int)$require_level) :
    return true;
  else :
    $session->msg("d", "Lo sentimos, no tiene permiso para ver la página.");
    redirect('home.php', false);
  endif;
}

/*--------------------------------------------------------------*/
/* Function para saber si el user name existe en la tabla users
/*--------------------------------------------------------------*/


function find_username($val)
{
  global $db;
  $sql = "SELECT username FROM users WHERE username = '{$db->escape($val)}' LIMIT 1 ";
  $result = $db->query($sql);
  return ($db->num_rows($result) === 0 ? true : false);
}


/*--------------------------------------------------------------*/
/* Function para saber si existe el email en la tabla users
/*--------------------------------------------------------------*/
function find_email($val)
{
  global $db;
  $sql = "SELECT email FROM users WHERE email = '{$db->escape($val)}' LIMIT 1 ";
  $result = $db->query($sql);
  return ($db->num_rows($result) === 0 ? true : false);
}


/*--------------------------------------------------------------*/
/* Function buscar los usuarios por paginacion
/*--------------------------------------------------------------*/
function find_users_with_pagination($limit, $offset)
{
  global $db;
  $sql = $db->query("SELECT * FROM users ORDER BY id ASC LIMIT $limit OFFSET $offset");

  // Cambia fetch_assoc a fetch_all para obtener todos los resultados
  $result = $sql->fetch_all(MYSQLI_ASSOC);

  return $result;
}

/*--------------------------------------------------------------*/
/* Function remplazar imagen de perfil
/*--------------------------------------------------------------*/

function replace_image_profile($consulta)
{
  global $db;
  $result = $db->query($consulta);
  return ($result && $db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function crear token para recuperar contraseña
/*--------------------------------------------------------------*/
function recover_password($id, $token, $expiration)
{
  global $db;

  // Insertar un nuevo registro en la tabla reset_pass
  $query = "INSERT INTO reset_pass (user_id, token, expiration) VALUES ('{$id}', '{$token}', '{$expiration}')";
  if ($db->query($query)) {
  }
}

/*--------------------------------------------------------------*/
/* Function buscar a que id esta asociado un token
/*--------------------------------------------------------------*/

function find_id_by_token($token)
{
  global $db;
  $sql = $db->query("SELECT user_id FROM reset_pass WHERE token = '{$db->escape($token)}' LIMIT 1");

  // Cambia fetch_assoc a fetch_all para obtener todos los resultados
  $result = $sql->fetch_all(MYSQLI_ASSOC);

  return $result;
}

/*--------------------------------------------------------------*/
/* Function buscar que el token exista en la base de datos.
/*--------------------------------------------------------------*/
function find_token($token)
{
  global $db;
  $sql = $db->query("SELECT token, user_id, expiration FROM reset_pass WHERE token = '{$db->escape($token)}' LIMIT 1");

  if ($result = $db->fetch_assoc($sql))
      return $result;
    else
      return null;

  return $result;
}
/*--------------------------------------------------------------*/
/* Function para elimiar los tokens usados y restablecer la contraseña
/*--------------------------------------------------------------*/
function change_password_with_token($id,$password)
{
  global $db;
  $id = (int)$id;
  $pass = sha1($password);
  // Insertar un nuevo registro en la tabla reset_pass
  $query1 = "UPDATE users SET password = '{$pass}' WHERE id = '{$id}'";
  $db->query($query1);
  $query2 = "DELETE FROM reset_pass WHERE user_id = '{$id}'";
  $db->query($query2);
}


/*--------------------------------------------------------------*/
/* Function para determianr los departamentos
/*--------------------------------------------------------------*/

function find_departaments_with_pagination($limit, $offset)
{
  global $db;
  $sql = $db->query("SELECT * FROM departamentos ORDER BY departamento ASC LIMIT $limit OFFSET $offset");

  // Cambia fetch_assoc a fetch_all para obtener todos los resultados
  $result = $sql->fetch_all(MYSQLI_ASSOC);

  return $result;
}

/*--------------------------------------------------------------*/
/* Function para determianr las evaluaciones
/*--------------------------------------------------------------*/

function find_evaluaciones_with_pagination($limit, $offset)
{
  global $db;
  $sql = $db->query("SELECT e.*, d.departamento AS descripcion_departamento FROM evaluaciones e JOIN departamentos d ON e.id_departamento = d.id ORDER BY id_departamento ASC LIMIT $limit OFFSET $offset");
  // Cambia fetch_assoc a fetch_all para obtener todos los resultados
  $result = $sql->fetch_all(MYSQLI_ASSOC);

  return $result;
}

/*--------------------------------------------------------------*/
/* Function para determianr las evaluaciones
/*--------------------------------------------------------------*/

function find_evaluacion($id)
{
  global $db;
  $sql = $db->query("SELECT e.*, d.departamento AS descripcion_departamento FROM evaluaciones e JOIN departamentos d ON e.id_departamento = d.id WHERE e.id = $id");
  // Cambia fetch_assoc a fetch_all para obtener todos los resultados
  $result = $sql->fetch_assoc();

  return $result;
}



/*--------------------------------------------------------------*/
/* Function ordenar de ASC
/*--------------------------------------------------------------*/
function find_all_az($table, $order)
{
  global $db;
  if (tableExists($table)) {
    return find_by_sql("SELECT * FROM " . $db->escape($table) . " ORDER BY ".  $db->escape($order). " ASC");
  }
}

/*--------------------------------------------------------------*/
/* Function ordenar de DEC
/*--------------------------------------------------------------*/
function find_all_za($table, $order)
{
  global $db;
  if (tableExists($table)) {
    return find_by_sql("SELECT * FROM " . $db->escape($table) . " ORDER BY ".  $db->escape($order). " ASC");
  }
}

/*--------------------------------------------------------------*/
/* Actualiar la funcioon
/*--------------------------------------------------------------*/
function update_evaluacion($id,$password)
{
  global $db;
  $id = (int)$id;
  $pass = sha1($password);
  // Insertar un nuevo registro en la tabla reset_pass
  $query1 = "UPDATE users SET password = '{$pass}' WHERE id = '{$id}'";
  $db->query($query1);
  $query2 = "DELETE FROM reset_pass WHERE user_id = '{$id}'";
  $db->query($query2);
}

?>