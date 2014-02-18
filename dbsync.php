#!/usr/bin/php
<?php
$projects_dir = 'projects';
$uid1_name = 'admin';
$uid1_mail = 'york@york.l';

function detect_localarea() {
  global $projects_dir;

  $settings_php = FALSE;
  $project_name = FALSE;

  $dirs = explode('/', $_ENV['PWD']);

  $next = TRUE;
  while (count($dirs) && !empty($dirs) && $next) {
    $dir = implode('/', $dirs);


    if ($dir == ($_ENV['HOME'] . "/" . $projects_dir)) {
      $next = FALSE;
    }
    else {
      if (is_file($dir . '/sites/default/local.settings.php') && empty($settings_php)) {
        $settings_php = $dir . '/sites/default/local.settings.php';
      }

      if (is_file($dir . '/sites/default/settings.local.php') && empty($settings_php)) {
        $settings_php = $dir . '/sites/default/settings.local.php';
      }

      if (is_file($dir . '/sites/default/settings.php') && empty($settings_php)) {
        $settings_php = $dir . '/sites/default/settings.php';
      }

      if (is_dir($dir . '/.git') && empty($project_name)) {
        $project_name = array_pop(explode('/', $dir));
      }
      $_ENV['HOME'] . "/" . $projects_dir;
    }

    if (!empty($settings_php) && !empty($project_name)) {
      $next = FALSE;
    }
    else {
      array_pop($dirs);
    }
  }

  return array($project_name, $settings_php);
}

list($project_name, $settings_php) = detect_localarea();

if (is_file($settings_php)) {
  include_once($settings_php);
  $db_dir = $_ENV['HOME'] . "/" . $projects_dir . "/db/" . $project_name;
  if (is_dir($db_dir)) {
    if ($dh = opendir($db_dir)) {
      $db_file = array();
      while (($file = readdir($dh)) !== FALSE) {
        if ($file != '..' && $file != '.') {
          if ($argc > 1) {
            if (strpos($file, $argv[1]) !== 0) {
              continue;
            }
          }
          $file_stat = stat($db_dir . '/' . $file);

          if (empty($db_file) || ($db_file['ctime'] < $file_stat['ctime'])) {
            $db_file['name'] = $file;
            $db_file['ctime'] = $file_stat['ctime'];
            $db_file['size'] = $file_stat['size'];
          }
//          echo $file . "\n" . $file_stat['atime'] . "\n" . $file_stat['ctime'] . "\n" . $file_stat['mtime'] . "\n";
        }
      }
      closedir($dh);

      if (!empty($db_file)) {
        $ext = pathinfo($db_file['name'], PATHINFO_EXTENSION);

        if (isset($db_url)) {
          $db = parse_url($db_url);
          $drupal_db['database'] =  trim($db['path'], '/');
          $drupal_db['username'] = $db['user'];
          $drupal_db['password'] = $db['pass'];
        }
        else {
          $drupal_db['database'] = $databases['default']['default']['database'];
          $drupal_db['username'] = $databases['default']['default']['username'];
          $drupal_db['password'] = $databases['default']['default']['password'];
        }

        $cmd = 'echo "DROP DATABASE ' . $drupal_db['database']
          . '; CREATE DATABASE ' . $drupal_db['database']
          . ';"|mysql -u' . $drupal_db['username']
          . ' -p' . $drupal_db['password'] . '"';

        $cmd = '';
        switch ($ext) {
          case 'gz':
              $cmd .= 'gzcat ';
            break;

          case 'bz2':
            $cmd .= 'bzcat ';

            break;

          case 'zip':
            $cmd .= 'unzip -p ';

            break;
          case 'Z':
            $cmd .= 'zcat ';

            break;
          default:
            $cmd .= 'cat ';
        }

        $link = mysql_connect('localhost', $drupal_db['username'], $drupal_db['password']);
        if (!$link) {
          die('Not connected : ' . mysql_error());
        }

        // make foo the current db
        $db_selected = mysql_select_db($drupal_db['database'], $link);
        if (!$db_selected) {
          mysql_query('CREATE DATABASE ' . $drupal_db['database']);
        }

        $result = mysql_query('SHOW TABLES');
        $tables = array();
        while ($row = mysql_fetch_array($result)) {
          $tables[] = $row[0];
        }

        if (count($tables)) {
          echo 'DROP ALL TABLES:';
          mysql_query('DROP TABLE ' . implode(', ', $tables));
          echo " done.\n";
        }

        $cmd .= $db_dir . '/' . $db_file['name'] . '| mysql -u '
          . $drupal_db['username'] . ' -p'
          . $drupal_db['password']
          . ' ' . $drupal_db['database'];

        echo 'IMPORT DB ' . $db_file['name'];
        echo ' ' . $db_file['size'];

#        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
#        echo $size ? round($db_file['size']/pow(1024, ($i = floor(log($db_file['size'], 1024)))), 2) . $filesizename[$i] : '0 Bytes';

        exec($cmd);
        echo " done.\n";

        echo 'PASSWORD RESET ';
        mysql_query("UPDATE users SET `name`='" . $uid1_name . "', mail='" . $uid1_mail . "', init='" . $uid1_mail . "' WHERE uid=1");
        system('drush upwd admin --password=admin');
        echo " done.\n";
      }
    }
  }
  else {
    die("DB directory not found! ($db_dir)");
  }

//  var_dump($_ENV);
//  var_dump($argv);
//  $_ENV['PWD'];
//  $_ENV['HOME'];

}
else {
  die("The settings.php not found!");
}
