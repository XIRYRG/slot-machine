<?php

try{
  require_once 'Appconfig.php';
  
  //auth/register new user
  //$u1 = new User();
  $u1 = User::get_instance();
  //$u1->auth();
}
catch (Exception $e){
  dump_it($e->getTraceAsString());
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bitcoin Slot Machine</title>
  <link href="css/style.css" rel="stylesheet">
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
  <script src="js/jquery.min.js"></script>
  <script src="js/slot.js"></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    function get_cookie ( cookie_name ){
      var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
      if ( results )
        return ( unescape ( results[2] ) );
      else
        return null;
    }
    
    $(document).ready(function(){
      var uid = '<?php echo $u1->uid;?>';
      //var uid = get_cookie('uid');
      slot = new Slot(uid);
      slot.linesFilling();
      slot.syncWithServer();
      slot.initAudio();
      $('button#slots-spin').on('click', function(){
        if (slot.options.playing == 'off'){
          return false;
        }
        if (slot.spin()){
//          slot.audio.spinbutton.play();
          //slot.audio.spin5sec.play();//into spin()
        }
      });
      
      $('button#slots-maxbet').on('click', function(){
        slot.audio.buttons.play();
        slot.makeMaxBet();
      });
      $('button#slots-autoplay').on('click', function(){
        slot.audio.buttons.play();
        clearInterval(slot.intervalID);
        if (!slot.autoplay && slot.currentBet != 0){
          slot.autoplay = true;
          slot.spin();
          //slot.audio.spin5sec.play();
          //setInterval(function(){
          slot.intervalID = setInterval(function(){
            if (window.console) console.log('autoplay');
            if (slot.currentUserBalance + slot.currentBet <= 0){
              slot.autoplay = false;
            }
            if (slot.autoplay){
              slot.spin();
            }
            else{
              clearInterval(slot.intervalID);
              slot.autoplay = false;
            }
          }
          , 1000/*(slot.maxSpinTime+500)/2*/)
        }
        else{
          slot.autoplay = false;
        }
      });
      $('button#slots-lastbet').on('click', function(){
        slot.audio.buttons.play();
        slot.setBetTo(slot.getLastBet());
      });
      //plus
      $('button.slots-plus').on('click', function(){
        //slot.audio.buttons.pause();
        //slot.audio.buttons.currentTime = 0;
        slot.audio.buttons.play();
        var buttonPlusId = this.id;
        switch(buttonPlusId){
          case 'slots-plus001':
            slot.incBetTo(0.01);
            break;
          case 'slots-plus01':
            slot.incBetTo(0.1);
            break;
          case 'slots-plus1':
            slot.incBetTo(1);
            break;
        }
      });
      //minus
      $('button.slots-minus').on('click', function(){
        slot.audio.buttons.play();
        var buttonPlusId = this.id;
        switch(buttonPlusId){
          case 'slots-minus001':
            slot.decBetTo(0.01);
            break;
          case 'slots-minus01':
            slot.decBetTo(0.1);
            break;
          case 'slots-minus1':
            slot.decBetTo(1);
            break;
        }
      });
      $('button#slots-cashout').on('click', function(){
        slot.audio.buttons.play();
        slot.checkSlotOptions();
        if (slot.options.paying_out == 'off'){
          return false;
        }
        
      });
      
      
      //uncomment
      //setInterval(slot.updateInterestingFacts, 10000);
      //too many requests to server db
      //setInterval(slot.checkSlotOptions, 1000);
      //setInterval(slot.checkForNewIncommingPayment, 10000);
    });
  </script>  
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body>
  <div id="slots">
    <div id="slots-reel1" class="slots-reel">
      <div class="slots-line"></div>
    </div>
    <div id="slots-reel2" class="slots-reel">
      <div class="slots-line"></div>
    </div>
    <div id="slots-reel3" class="slots-reel">
      <div class="slots-line"></div>
    </div>
    <div id="slots-status">
      <div id="slots-status-display"></div>
    </div>
    <div id="slots-body">
      <img id="slots-logo" src="images/bsm-logo.png" alt="SatoshiSlots.com">
      <img id="slots-paytable" src="images/bsm-paytable.png" alt="Paytable">
      <div id="slots-address" class="slots-display"> <?php echo $u1->bitcoin_recieve_address; ?> </div>
      <div id="slots-balance" class="slots-display">0</div>
      <div id="slots-bet" class="slots-display">0</div>
      <button id="slots-minus001" class="slots-button slots-minus"></button>
      <button id="slots-minus01" class="slots-button slots-minus"></button>
      <button id="slots-minus1" class="slots-button slots-minus"></button>
      <button id="slots-plus001" class="slots-button slots-plus"></button>
      <button id="slots-plus01" class="slots-button slots-plus"></button>
      <button id="slots-plus1" class="slots-button slots-plus"></button>
      <button id="slots-spin" class="slots-button"></button>
      <button id="slots-lastbet" class="slots-button slots-bottombutton"></button>
      <button id="slots-maxbet" class="slots-button slots-bottombutton"></button>
      <button id="slots-autoplay" class="slots-button slots-bottombutton"></button>
      <button id="slots-cashout" class="slots-button slots-bottombutton"></button>
    </div>
  </div>
  <div id="statistic">
    <?php
      echo 'Last 20 transactions: <br />';
      Transaction::show_transactions($option = 'last');
      echo '<a href="fullList.php?option=last">Full list</a><br /><br />';
      echo '20 biggest winners: <br />';
      Transaction::show_transactions($option = 'biggestwinners');
      echo '<a href="fullList.php?option=biggestwinners">Full list</a><br />';
    ?>
  </div>
  <div id="interesting_facts">
    Interesting facts
    <?php
      show_interesting_facts();
    ?>
  </div>
  <div id="audio">
    <audio id="spin5sec" preload="auto">
      <source src="sounds/mp3/Spin_5sec.mp3" type="audio/mpeg" >
      <source src="sounds/wav/Spin_5sec.wav" type="audio/wav" >
    </audio>
    <audio id="win" preload="auto">
      <source src="sounds/mp3/Win.mp3" type="audio/mpeg" >
      <source src="sounds/wav/Win.wav" type="audio/wav" >
    </audio>
    <audio id="loose" preload="auto">
      <source src="sounds/mp3/Loose.mp3" type="audio/mpeg" >
      <source src="sounds/wav/Loose.wav" type="audio/wav" >
    </audio>
    <audio id="spinbutton" preload="auto">
      <source src="sounds/mp3/Button_SPIN_only.mp3" type="audio/mpeg" >
      <source src="sounds/wav/Button_SPIN_only.wav" type="audio/wav" >
    </audio>
    <audio id="buttons" preload="auto">
      <source src="sounds/mp3/Button_all_the_rest.mp3" type="audio/mpeg" >
      <source src="sounds/wav/Button_All_the_rest.wav" type="audio/wav" >
    </audio>
  </div>
</body>
</html>