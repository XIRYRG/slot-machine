<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
          require_once 'appconfig.php';
          function dump_it($var){
            echo '<pre>';
            var_dump($_COOKIE);
            var_dump($_SESSION);
            echo '</pre>';
          }
          session_start();
          if (SetCookie("Test","Value", $NOW_PLUS_ONE_YEAR, '/'))
            echo "<h3>Cookies set successfully!</h3>";
        dump_it($NOW_PLUS_ONE_YEAR);
        ?>
    </body>
</html>
