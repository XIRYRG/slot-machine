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
    <script>
      $(document).ready(function(){
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
        $('button#save_slot_state').on('click',function(){
          var powerCheck = null;
          var powerOnOff = 'on';
          powerCheck = $('input#power').attr('checked');
          if (powerCheck == 'checked'){
            console.log('slot on');
            powerOnOff = 'on';
          }
          else{
            console.log('slot off');
            powerOnOff = 'off';
          }
          //make request
          $.post("AjaxRequestsProcessing.php", { slot: "power", power: powerOnOff})
            .success(function(power) {
              console.log(power);
              checkSlotPowerStatus();
              if (power == 'Slot on'){
                $('div#slot_power_state').text('Playing status: '+powerOnOff);
              }
              else{
                $('div#slot_power_state').text('Playing status: '+powerOnOff);
              }
            })
            .error(function(){
              console.log('Client error. Error in ajax admin-->power request, bad response');
            });
        });
        $('button#show_transactions').on('click',function(){
          show();
        });
        function checkSlotPowerStatus(){
          $.post("AjaxRequestsProcessing.php", { slot: "power", power: 'checkPower'})
            .success(function(power) {
              console.log(power);
              
            })
            .error(function(){
              console.log('Client error. Error in ajax admin-->power request, bad response');
            });
        };
        function show(){
          var fromDate = $('div#calendar > input#from').val();
          var toDate = $('div#calendar > input#to').val();
          if (!fromDate || !toDate){
            console.log('from or to date not specified');
            return false;
          }
          $.post("AjaxRequestsProcessing.php", { slot: "transactions", 'fromDate': fromDate, 'toDate': toDate})
            .success(function(transactionsTable) {
              $('div#transactions').html(transactionsTable);
              //console.log(transactionsTable);
              
            })
            .error(function(){
              console.log('Client error. Error in ajax admin-->show request, bad response');
            });
        }
      });
//      $('input#power').on('change',function(){
//        alert('changed');
//      });
      
    </script>
</head>
<body>
  <div id="slot_power_state">Playing status: on</div>
  Slot on: <input id="power" checked="checked" type="checkbox" name="power" />
  
  <div id="slot_paying_out_state">Paying out status: on</div>
  Paying out on: <input id="power" checked="checked" type="checkbox" name="power" />
  <br />
  <button id="save_slot_state">Save</button>

  <br />
  <div id="calendar">
    <label for="from">From (e.g.: 2012-12-31)</label>
    <input type="text" id="from" name="from" />
    <label for="to">to</label>
    <input type="text" id="to" name="to" />
    <!--<br />-->
    <button id="show_transactions">Show</button>
  </div>
</body>
</html>
<?php
/*
 * 5. ADMIN 
- Minimal backend where cash in, cash out and profit can be outputted 
 * by selectable time frame and breakdown of per user winnings/losse
Also number for the current payback % and option to turn off the playing and paying out
 * 
 *
 */
  //$user = User::get_instance();



$_SESSION['admin'] = true;
//dump_it($_SESSION);

//echo $total_cached_out = Transaction::get_total_cached_out_money();
//echo $total_cached_in = Transaction::get_total_cached_in_money();

Transaction::show_transactions('admin', 10, '2012-11-19', '2012-11-23');
$output_start = "
    <br />
    <table border=\"1px\" style=\"border-collapse: collapse;\">
      <tr>
        <td>Cash in</td>
        <td>Cash out</td>
         
        ";

$output_end = "
  
        
        
      </tr>
      <tr>
          <td>Total cash in</td>
          <td>Total cash out</td>
        </tr>
    </table>
  ";
echo $output_start.$output_end;
?>
