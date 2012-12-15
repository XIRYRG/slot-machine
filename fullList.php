<?php
require_once 'Appconfig.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bitcoin Slot Machine</title>
  <link href="css/style.css" rel="stylesheet">
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
  <script src="js/jquery.min.js"></script>
  <!--<script src="bootstrap/js/bootstrap.min.js"></script>-->
  <script type="text/javascript">
    $(document).ready(function(){
      currentPage = 1;
      paginationStart = 0;
      paginationPer = 100;
      /*
      $.post("AjaxRequestsProcessing.php", { slot: "power", power: power_on_off, paying_out: paying_out})
      .success(function(options) {
        if (window.console) console.log(options);
        options = eval( "("+options+")");
        checkSlotPowerStatus();
      })
      .error(function(){
        if (window.console) console.log('Client error. Error in ajax admin-->power request, bad response');
      });
      */
      function getParams(){
        var tmp = new Array();		
        var tmp2 = new Array();		
        GET = new Array();
        var get = location.search;
        if(get != '') {
          tmp = (get.substr(1)).split('&');
          for(var i=0; i < tmp.length; i++) {
            tmp2 = tmp[i].split('=');
            GET[tmp2[0]] = tmp2[1];
          }
        }
        return GET;
      }
      
     
      function show(){
        option = 'last';
        GET = getParams();
        option = GET['option'];
        
        $.post("AjaxRequestsProcessing.php", { slot: "transactions", 'option': option, 'from': (currentPage*100-100), 'to': (currentPage*100)})
          .success(function(transactionsTable) {
            $('div#transactions').html(transactionsTable);
            //if (window.console) console.log(transactionsTable);

          })
          .error(function(){
            if (window.console) console.log('Client error. Error in ajax admin-->show request, bad response');
          });
      }
      $('div#left').on('click',function(){
        if (currentPage == 1)
          return false;
        currentPage -= 1;
        $('div#left > a').attr('href','#page='+currentPage);
        $('div#right > a').attr('href','#page='+(currentPage+1));
        show();
      });
      $('div#right').on('click',function(){
        currentPage += 1;
        $('div#left > a').attr('href','#page='+(currentPage-1));
        $('div#right > a').attr('href','#page='+currentPage);
        show()
      });
    });
  </script>
</head>
<body>
<?php
if (!empty($_GET['option'])){
  $option = $_GET['option'];
}
else{
  $option = 'last';
}
switch ($option) {
  case 'last':
    Transaction::show_transactions('last', 0, 100);
    break;
  case 'biggestwinners':
    Transaction::show_transactions('biggestwinners', 0, 100);
    break;

  default:
    Transaction::show_transactions('last', 0, 100);
    break;
}
?>
  <div id="prev_and_next">
    <div id="left" style="float: left;"><a href="#">prev</a></div>
    <div id="right" style="float: right;"><a href="#">next</a></div>
  </div>
</body>
</html>