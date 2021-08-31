<html>
  <head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://blockchain.info//Resources/wallet/pay-now-button.js"></script>
  </head>
  <body>
    <div style="font-size:16px;margin:10px;width:300px" class="blockchain-btn"
       data-address="3B3zps3udeGNtHVqb6FehTfejo56Psy3TZ
       data-anonymous="false"
       data-callback="https://mydomain.com/callback_url">
      <div class="blockchain stage-begin">
          <img src="http://static.blockchain.info//Resources/buttons/pay_now_64.png">
      </div>
      <div class="blockchain stage-loading" style="text-align:center">
          <img src="http://static.blockchain.info//Resources/loading-large.gif">
      </div>
      <div class="blockchain stage-ready">
          Please send payment to bitcoin address <b>[[address]]</b>
      </div>
      <div class="blockchain stage-paid">
          Payment Received [[value]] BTC. Thank You.
      </div>
      <div class="blockchain stage-error">
          <font color="red">[[error]]</font>
      </div>
    </div>
  </body>
</html>
