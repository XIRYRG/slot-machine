<?php
require_once 'Appconfig.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Slot admin page</title>
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <!--<link rel="stylesheet" href="/resources/demos/style.css" />-->
    <!--<link href="css/style.css" rel="stylesheet">-->
    <!--<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
    <!--<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">-->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
    <script src="js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    
</head>
<body>

<?php
//admin auth
  function auth(){
    if (empty($_SESSION['admin']) || $_SESSION['admin'] != 'true'){
      if (empty($_POST['login']) || empty($_POST['password'])){
        //echo 'Bad parameters was recieved. No auth';
        exit('Not authorized');
      }
      $login = $_POST['login'];
      $password_md5 = md5($_POST['password']);
      $db = DBconfig::get_instance();
      $admin = $db->mysql_fetch_array('SELECT * FROM admin');
      //auth == false
      if (($admin['login'] != $login) || ($admin['password'] != $password_md5)){
        exit('Wrong login or password');
        return false;
      }
      //auth == true
      $_SESSION['admin'] = true;
      //refresh this page
      echo '<META HTTP-EQUIV="Refresh" Content="0; ">';
      
    }
    else{
      update_login_pass_in_db();
    }
  }
  //for changing login and pass in db
  function update_login_pass_in_db(){
    if (empty($_POST['login']) || empty($_POST['password']) || empty($_POST['Save'])){
        //echo 'Bad parameters was recieved. No auth';
        //exit('Not authorized'.__LINE__);
      //echo 'login and password not given.';
      return false;
    }
    if (empty($_SESSION['admin']) || $_SESSION['admin'] != 'true' || empty($_POST['Save'])){
      echo 'Can\'t change login and password.';
      return false;
    }
    $login = mysql_real_escape_string($_POST['login']);
    $password_md5 = md5(mysql_real_escape_string($_POST['password']));
    $db = DBconfig::get_instance();
    $res = $db->query("UPDATE admin SET login = '$login', password = '$password_md5' WHERE ID = 1");
    if ($res){
      echo 'Login and password updated succesfully<br />';
      echo 'Login: '.$login.'<br>';
      echo 'md5(password): '.$password_md5;
    }
  }


//  if (empty($_SESSION['admin']) || $_SESSION['admin'] != 'true'){
?>
  
  <div id="auth_form" style="    width: 200px;">
    <form method="POST" action="admin.php" onSubmit="window.location.reload()">
      Login:<br />
      <input type="text" id="login" name="login" />
      <br />
      Password:<br />
      <input type="password" id="password" name="password" />
      <br />
      <?php
      if (empty($_SESSION['admin']) || $_SESSION['admin'] != 'true'){
      ?>
        <input type="submit" id="enter" name="enter" value="Sign in" />
      <?php
      }
      else{
      ?>
        <input type="submit" id="Save" name="Save" value="Save" />  
      <?php
      }
//      dump_it($_POST);
      ?>
    </form>
  </div>
  
<?php
//  if (empty($_SESSION['admin']) || $_SESSION['admin'] != 'true'){
    auth();
//  }
  
  
//}

?>
  
  
  <div id="slot_power_state">Playing status: on</div>
  Slot on: <input id="power" checked="checked" type="checkbox" name="power" />
  
  <div id="slot_paying_out_state">Paying out status: on</div>
  Paying out on: <input id="paying_out" checked="checked" type="checkbox" name="paying_out" />
  <br />
  <button id="save_slot_state">Save</button>
  <br />
  <br />
  <div id="calendar">
    <label for="from">From (e.g.: 2012-11-31)</label>
    <input type="text" id="from" name="from" />
    <label for="to">to</label>
    <input type="text" id="to" name="to" />
    <!--<br />-->
    <button id="show_transactions">Show</button>
  </div>
  <div id="transactions">
    <!--
    tables are loaded via ajax and placed in this div
    -->
  </div>
  
  <div id="group_by_user">
  </div>
  
  <script type="text/javascript">
      function slotOptions(){
        this.options = {
          'playing': 'on',
          'paying_out': 'on'
        };
      }
      $(document).ready(function(){
        show();
        slotOptions = new slotOptions();
        $(function() {
            $( "#from" ).datepicker({
              dateFormat: 'yy-mm-dd',
              defaultDate: "+1w",
              changeMonth: true,
              numberOfMonths: 1,
              onClose: function( selectedDate ) {
                  $( "#to" ).datepicker( "option", "minDate", selectedDate );
              }
            });
            $( "#to" ).datepicker({
              dateFormat: 'yy-mm-dd',
              defaultDate: "+1w",
              changeMonth: true,
              numberOfMonths: 1,
              onClose: function( selectedDate ) {
                  $( "#from" ).datepicker( "option", "maxDate", selectedDate );
              }
            });
        });
        checkSlotPowerStatus();
        //syncOptionsWithServer(slotOptions.options);
        
        
        $('button#save_slot_state').on('click',function(){
          var power_check = null;
          var power_on_off = 'on';
          var paying_out = 'on';
          power_check = $('input#power').attr('checked');
          paying_out = $('input#paying_out').attr('checked');
          if (power_check == 'checked'){
            if (window.console) console.log('slot on');
            power_on_off = 'on';
          }
          else{
            if (window.console) console.log('slot off');
            power_on_off = 'off';
          }
          if (paying_out == 'checked'){
            if (window.console) console.log('paying out on');
            paying_out = 'on';
          }
          else{
            if (window.console) console.log('paying out off');
            paying_out = 'off';
          }
          //save the values by click
          $.post("AjaxRequestsProcessing.php", { slot: "power", power: power_on_off, paying_out: paying_out})
            .success(function(options) {
              if (window.console) console.log(options);
              options = eval( "("+options+")");
              checkSlotPowerStatus();
              //syncOptionsWithServer(slotOptions.options);
            })
            .error(function(){
              if (window.console) console.log('Client error. Error in ajax admin-->power request, bad response');
            });
        });
        $('button#show_transactions').on('click',function(){
          show();
        });
        function checkSlotPowerStatus(){
          $.post("AjaxRequestsProcessing.php", { slot: "power", power: 'check_options'})
          .success(function(options) {
            options = eval( "("+options+")");
            slotOptions.options.playing = options.playing;
            slotOptions.options.paying_out = options.paying_out;
            syncOptionsWithServer(slotOptions.options);
          })
          .error(function(){
            if (window.console) console.log('Client error. Error in ajax admin-->checkSlotPowerStatus request, bad response');
          });
        };
        function syncOptionsWithServer(options){
          if (window.console) console.log(options);
          $('div#slot_power_state').text('Playing status: '+options.playing);
          $('div#slot_paying_out_state').text('Paying out status: '+options.paying_out);
          if (options.playing == 'on'){
            $('input#power').attr('checked', 'checked');
          }
          else{
            $('input#power').removeAttr('checked', 'checked');
          }
          if (options.paying_out == 'on'){
            $('input#paying_out').attr('checked', 'checked');
          }
          else{
            $('input#paying_out').removeAttr('checked', 'checked');
          }
        }
        
        function show(){
          var fromDate = $('div#calendar > input#from').val();
          var toDate = $('div#calendar > input#to').val();
          if (!fromDate || !toDate){
            if (window.console) console.log('from or to date not specified');
            fromDate = '2012-11-01';
            toDate = '2052-11-01';
            //return false;
          }
          $.post("AjaxRequestsProcessing.php", { slot: "transactions", 'fromDate': fromDate, 'toDate': toDate, 'page':'admin'})
            .success(function(transactionsTable) {
              $('div#transactions').html(transactionsTable);
              //if (window.console) console.log(transactionsTable);
              
            })
            .error(function(){
              if (window.console) console.log('Client error. Error in ajax admin-->show request, bad response');
            });
        }
        //not used
        function auth(){
          $('div#auth_form > input#enter').on('click',function(){
            login = '';
            password = '';
            $.post("admin.php", { admin: "auth", 'login': login, 'password': password})
              .success(function(authResponse) {
                if (window.console) console.log(authResponse);
              })
              .error(function(){
                if (window.console) console.log('Client error. Error in ajax admin-->auth_form request, bad response');
              });

          });
        }
      });
    </script>
</body>
</html>
