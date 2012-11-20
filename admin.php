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
            console.log('slot on');
            power_on_off = 'on';
          }
          else{
            console.log('slot off');
            power_on_off = 'off';
          }
          if (paying_out == 'checked'){
            console.log('paying out on');
            paying_out = 'on';
          }
          else{
            console.log('paying out off');
            paying_out = 'off';
          }
          //save the values by click
          $.post("AjaxRequestsProcessing.php", { slot: "power", power: power_on_off, paying_out: paying_out})
            .success(function(options) {
              console.log(options);
              options = eval( "("+options+")");
              checkSlotPowerStatus();
              //syncOptionsWithServer(slotOptions.options);
            })
            .error(function(){
              console.log('Client error. Error in ajax admin-->power request, bad response');
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
            console.log('Client error. Error in ajax admin-->checkSlotPowerStatus request, bad response');
          });
        };
        function syncOptionsWithServer(options){
          console.log(options);
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
        /*
        function checkSlotPowerStatus(){
            $.post("AjaxRequestsProcessing.php", { slot: "power", power: 'check_options'})
            .success(function(options) {
              options = eval( "("+options+")");
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
              
              console.log(options);
              
            })
            .error(function(){
              console.log('Client error. Error in ajax admin-->power request, bad response');
            });
        };
        */
        function show(){
          var fromDate = $('div#calendar > input#from').val();
          var toDate = $('div#calendar > input#to').val();
          if (!fromDate || !toDate){
            console.log('from or to date not specified');
            fromDate = '2012-11-01';
            toDate = '2052-11-01';
            //return false;
          }
          $.post("AjaxRequestsProcessing.php", { slot: "transactions", 'fromDate': fromDate, 'toDate': toDate, 'page':'admin'})
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
  Paying out on: <input id="paying_out" checked="checked" type="checkbox" name="paying_out" />
  <br />
  <button id="save_slot_state">Save</button>
  <br />
  <br />
  <div id="calendar">
    <label for="from">From (e.g.: 2012-12-31)</label>
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
    group_by_user
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

//todo: total cach in out
//$total_cached_out = Transaction::get_total_cached_out_money();
//$total_cached_in = Transaction::get_total_cached_in_money();
//
//Transaction::show_transactions('transactions', 0,20, '2012-11-19', '2012-11-23');
//$output_start = "
//    <br />
//    <table border=\"1px\" style=\"border-collapse: collapse;\">
//      <tr>
//        <td>Cash in</td>
//        <td>Cash out</td>
//        <td>Profit</td>
//        <td>Payback %</td>
//         
//        ";
//$payback = ($total_cached_out/$total_cached_in)*100;
//$profit = $total_cached_in - $total_cached_out;
//$output_end = "
//      </tr>
//      <tr>
//          <td>$total_cached_in</td>
//          <td>$total_cached_out</td>
//          <td>$profit</td>
//          <td>$payback</td>
//        </tr>
//    </table>
//  ";
//echo $output_start.$output_end;
?>
