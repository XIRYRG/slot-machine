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
  <script src="bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    function Slot(uid){
      //todo: get config for slot (on client side) via Ajax Or use default config described here
      //default slot config
      this.uid = uid;
      this.currentBet = new Number(0);
      this.lastPayline = '';
      this.currentPayline = '';
      this.autoplay = false;
      this.currentUserBalance = new Number(0);
      this.lastBet = new Number(0);
      this.symbolsPerReel = 64;
      this.arrayOfSymbolsId = new Array();
      this.onceFilledLine = false;
      this.onceStarted = false;
      this.musicOn = true;
      //this.audio = new Audio();
      //
      this.audio = {
        spin5sec: null,
        win: null,
        loose: null,
        spinbutton: null,
        //in case if buttons pressed oftenly
        buttons: {
          element: null,
          play: function(){
            //stop emulation
            slot.audio.buttons.element.pause();
            slot.audio.buttons.element.currentTime = 0;
            slot.audio.buttons.element.play();
          }
        }
      }
      this.initAudio = function(){
        var slot = this;
        slot.audio.spin5sec = document.getElementById('spin5sec');
        slot.audio.win = document.getElementById('win');
        slot.audio.loose = document.getElementById('loose');
        slot.audio.spinbutton = document.getElementById('spinbutton');
        slot.audio.buttons.element = document.getElementById('buttons');
      }
      this.isWin = function(){
        if (slot.currentPayline.multiplier > 0){
          return true;
        }
        else{
          return false;
        }
      }
      this.statusDisplay = {
        slot : this,
        show : function(str){
          //if (slot.currentPayline.multiplier > 0){
          if (slot.isWin()){
            $('div#slots-status-display').text('You have WON! ('+slot.currentPayline.multiplier+'x'+slot.currentPayline.bet_from_client+')');
            slot.audio.win.play();
          }
          else if(!slot.isWin){
            $('div#slots-status-display').text('');
            slot.audio.loose.play();
          }
          else{
            $('div#slots-status-display').text(str);
          }
          
        },
        clear : function(){
          $('div#slots-status-display').text('');
        }
      };
      this.options = {
          'playing': 'on',
          'paying_out': 'on'
        };
      this.checkSlotOptions = function(){
        $.post("AjaxRequestsProcessing.php", { slot: "power", power: 'check_options'})
        .success(function(options) {
          console.log(options);
          options = eval( "("+options+")");
          slot.options.playing = options.playing;
          slot.options.paying_out = options.paying_out;
          if ((slot.options.playing == 'off') && (slot.options.paying_out == 'off')){
            slot.statusDisplay.show('Playing and paying out are off');
          }
          else if (slot.options.playing == 'off'){
            slot.statusDisplay.show('Slot machine playing is off');
          }
          else if (slot.options.paying_out == 'off'){
            slot.statusDisplay.show('Slot machine paying out is off');
          }
          else{
            slot.statusDisplay.show('');
          }
        })
        .error(function(){
          console.log('Client error. Error in ajax admin-->power request, bad response');
        });
      };
      this.updateInterestingFacts = function(){
        $.post("AjaxRequestsProcessing.php", { slot: "getInterestingFacts"})
        .success(function(interestingFacts) {
          var interestingFacts = eval( "("+interestingFacts+")");
          $('td#total_cached_out').text('Cashed out money: '+ interestingFacts.cashed_out_money +' BTC');
          $('td#total_spin_number').text('Games played: '+ interestingFacts.games_played);
          //console.log(interestingFacts);
        })
        .error(function(){
          console.log('Client error. Error in ajax updateInterestingFacts request, bad response');
        });
      }
      this.checkForNewIncommingPayment = function(){
        $.post("AjaxRequestsProcessing.php", { slot: "checkForIncommingPayment"})
          //todo: try/catch and in case bad request 
        .success(function(balance) {
          console.log(balance);
          slot.syncWithServer();
          //slot.currentPayline = eval( "("+balance+")");
        })
        .error(function(){
          console.log('Client error. Error in ajax checkForIncommingPayment request, bad response');
        })
        ;
      }
      this.sendToServerThatSpinPressed = function(){
        var slot = this;
        $.post("AjaxRequestsProcessing.php", { slot: "spinPressed", currentBet: slot.currentBet })//, function(paylineReturnedByServerSpin){
          //todo: try/catch and in case bad request 
        //})
        .success(function(paylineReturnedByServerSpin) {
          //if (paylineReturnedByServerSpin == 'Slot-machine powered off'){
          if (paylineReturnedByServerSpin == -1){
            slot.options.playing = 'off';
            //console.log('Slot-machine powered off');
            slot.statusDisplay.show('Slot machine is off');
            return false;
          }
          //if (paylineReturnedByServerSpin == '[Bet <= 0 or Bet not number.]'){
          if (paylineReturnedByServerSpin == -2){
            slot.statusDisplay.show('Bad bet');
            return false;
          }
          slot.currentPayline = eval( "("+paylineReturnedByServerSpin+")");
          slot.fillPayLine();
          slot.rememberLastShowedSymbols();
          console.log(paylineReturnedByServerSpin);
        })
        .error(function(){
          console.log('Client error. Error in ajax spin request, bad response');
        });
      }
      this.symbols = {
        'pyramid': 0,
        'bitcoin': 1,
        'anonymous': 2,
        'onion': 3,
        'anarchy': 4,
        'peace': 5,
        'blank': 6
      }
      this.syncWithServer = function(){
        var slot = this;
        user = null;
        $.post("AjaxRequestsProcessing.php", { slot: "sync" }, function(slotValues){
          user = eval( "("+slotValues+")");
          slot.uid = user.uid;
          slot.currentUserBalance = new Number(user.money_balance - slot.currentBet);
          slot.updateBalanceAndBet();
          //todo: try/catch and in case bad request 
          //slot.currentUserBalance = user_balance;
          //slotValues
        });
        console.log(user);
        
        
        //todo: sync all values 
        //this.getUserBalanceFromServer();
      }
      this.lastShowedSymbols = {
        //fill it by blank symbols
        reel1: {top: 6, center: 6, bottom: 6},
        reel2: {top: 6, center: 6, bottom: 6},
        reel3: {top: 6, center: 6, bottom: 6}
      }
      
      this.getValidSymbolByName = function(symbol){
        if (typeof(symbol) == 'number' && symbol >= 0 && symbol <= 6){
          return symbol;
        }
        if ( typeof(this.symbols[symbol]) == 'undefined'){
          return false;
        }
        else{
          return this.symbols[symbol];
        }
      }
      //fill payline
      this.fillPayLine = function(){
        $('div#slots-reel1 > div.slots-line > div.payline').attr('class', 'slots-symbol'+slot.getValidSymbolByName(slot.currentPayline.sym1)+' payline');
        $('div#slots-reel2 > div.slots-line > div.payline').attr('class', 'slots-symbol'+slot.getValidSymbolByName(slot.currentPayline.sym2)+' payline');
        $('div#slots-reel3 > div.slots-line > div.payline').attr('class', 'slots-symbol'+slot.getValidSymbolByName(slot.currentPayline.sym3)+' payline');
      }
      
      //fill line (top/center/bottom) in slot
      this.fillLine = function(symbol){
        var slot = this;
        /*
        if (!(typeof(reelNum) == 'number') || reelNum < 1 || reelNum > 3 ){
          return false;
        }
        */
        $('div.slots-line').append('<div class="slots-symbol'+ symbol +'"></div>');
      }
      //There is no spoon
      this.restoreLastShowedSymbols = function(){
        var slot = this;
        $('div#slots-reel1 > div.slots-line > div.oldpayline').prev().attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel1.top);
        $('div#slots-reel1 > div.slots-line > div.oldpayline').attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel1.center+' oldpayline');
        $('div#slots-reel1 > div.slots-line > div.oldpayline').next().attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel1.bottom);
        
        $('div#slots-reel2 > div.slots-line > div.oldpayline').prev().attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel2.top);
        $('div#slots-reel2 > div.slots-line > div.oldpayline').attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel2.center+' oldpayline');
        $('div#slots-reel2 > div.slots-line > div.oldpayline').next().attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel2.bottom);
        
        $('div#slots-reel3 > div.slots-line > div.oldpayline').prev().attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel3.top);
        $('div#slots-reel3 > div.slots-line > div.oldpayline').attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel3.center+' oldpayline');
        $('div#slots-reel3 > div.slots-line > div.oldpayline').next().attr('class', 'slots-symbol'+slot.lastShowedSymbols.reel3.bottom);
      }
      
      //remember who you are
      this.rememberLastShowedSymbols = function(){
        var slot = this;
        slot.lastShowedSymbols.reel1.top = slot.arrayOfSymbolsId[0][0];
        slot.lastShowedSymbols.reel1.center = slot.getValidSymbolByName(slot.currentPayline.sym1);
        slot.lastShowedSymbols.reel1.bottom = slot.arrayOfSymbolsId[0][2];
        
        slot.lastShowedSymbols.reel2.top = slot.arrayOfSymbolsId[1][0];
        slot.lastShowedSymbols.reel2.center = slot.getValidSymbolByName(slot.currentPayline.sym2);
        slot.lastShowedSymbols.reel2.bottom = slot.arrayOfSymbolsId[1][2];
        
        slot.lastShowedSymbols.reel3.top = slot.arrayOfSymbolsId[2][0];
        slot.lastShowedSymbols.reel3.center = slot.getValidSymbolByName(slot.currentPayline.sym3);
        slot.lastShowedSymbols.reel3.bottom = slot.arrayOfSymbolsId[2][2];
      }
      this.regenerateLinesRandomly = function(){
        
      }
      //fills lines of symbols
      this.linesFilling = function(){
        var slot = this;
        
        //linesFilling should be called after filling slot.currentPayline (== Ajax-request for spin| == on the server spin completed)
        /*if (!slot.currentPayline){
          return false;
        }*/
        //slot.fillLine(slot.symbols['pyramid']);
        //$('div.slots-line').css({marginTop: -(slot.symbolsPerReel-3)*125 + 'px'});  
        //slot.arrayOfSymbolsId = new Array();
        var reelNumber = 0;
        $('div.slots-line').each(function(){
          slot.arrayOfSymbolsId[reelNumber] = new Array();
          for (var i = 0; i < slot.symbolsPerReel; i++) {
            var id = Math.round(Math.random()*(slot.totalSymbolsNumber-1));
            //reelLineSymbols keep wrong id for payline!
            //because payline gets new id after request to server
            slot.arrayOfSymbolsId[reelNumber][i] = id;
            if (!slot.onceFilledLine){
              $(this).append('<div class="slots-symbol'+ id +'"></div>');
            }
            else{
              //toooooo slow. todo: rerandom only prev and next lines
              $(this).children('div :nth-child('+(i+1)+')').attr('class','slots-symbol'+ id);
            }
          }
          
          $(this).css({marginTop: -(slot.symbolsPerReel-3)*125 + 'px'});
          reelNumber++;
        });
        
/*
        $('div#slots-reel1 > div.slots-line :nth-child(2)').attr('class');
        $('div#slots-reel2 > div.slots-line :nth-child(2)').attr('class');
        $('div#slots-reel3 > div.slots-line :nth-child(2)').attr('class');
*/
        //add classes oldpayline and payline
        $('div.slots-line :nth-child(2)').addClass('payline');
        $('div.slots-line :nth-child(63)').addClass('oldpayline');
        if (!slot.onceFilledLine){
          slot.onceFilledLine = true;
          slot.rememberLastShowedSymbols();
        }
        //slot.onceFilledLine = true;
        //slot.rememberLastShowedSymbols();
      }
      this.rotationTime = {
        //rotation time for every reel in ms
        reel1: 2000,
        reel2: 3000,
        reel3: 5000
      };
      //max spin time is the max time of every reel rotates
      this.maxSpinTime = Math.max(this.rotationTime.reel1,this.rotationTime.reel2,this.rotationTime.reel3);
      this.totalSymbolsNumber = 7;
      this.state = 'stop';
      //make slot state 'stop'
      this.getStateStop = function(){
        this.state = 'stop';
      }
      //make slot state 'started'
      this.getStateStarted = function(){
        this.state = 'started';
      }
      //make 1 spin
      this.spin = function(){
        var slot = this;
        //already started
        if (slot.state == 'started'){
          console.log('[Slot started already. Wait while it have stoped! ]');
          return false;
        }
        //no bet
        if (slot.currentBet <= 0){
          console.log('[Current bet = 0. Not worth the trouble]');
          return false;
        }

        slot.audio.spin5sec.play();
        slot.statusDisplay.clear();
        //restore last showed symbols if there is last show exists
        slot.linesFilling();
        if (slot.onceStarted){
            slot.restoreLastShowedSymbols();
        }
        //save last payline
        slot.lastPayline = this.currentPayline;
        slot.sendToServerThatSpinPressed();
        //todo: syncronize the client slot values with the server slot values
        //slot started
        
        
        
        slot.getStateStarted();
        setTimeout('slot.getStateStop()', slot.maxSpinTime);
        //bet was 
        slot.lastBet = slot.currentBet;
        slot.currentBet = 0;
        slot.updateBalanceAndBet();
        slot.animateSlot();
        
        //sync the result with the server
        setTimeout('slot.syncWithServer()', slot.maxSpinTime-200);
        //set last bet as current bet after spin
        //if (slot.currentBet == 0){
        setTimeout('slot.setBetTo(slot.getLastBet())', slot.maxSpinTime+200);
        //}
        setTimeout('slot.statusDisplay.show()', slot.maxSpinTime+100);
        //check for win
        //setTimeout('slot.isWin()',slot.maxSpinTime+100);
        slot.onceStarted = true;
        return true;
      }
      this.animateSlot = function(){
        var slot = this;
        $('div.slots-line').each(function(){
          $(this).css({marginTop: -(slot.symbolsPerReel-3)*125 + 'px'});
        });
        $('div#slots-reel1 > div.slots-line').animate({marginTop: 0}, slot.rotationTime.reel1);
        $('div#slots-reel2 > div.slots-line').animate({marginTop: 0}, slot.rotationTime.reel2);
        $('div#slots-reel3 > div.slots-line').animate({marginTop: 0}, slot.rotationTime.reel3);
      }

      this.incBetTo = function(val){
        val = Number(val);
        val = Math.round(val*100) / 100;
        //console.log(val);
        //can't inc bet
        if (this.currentUserBalance - val < 0){
          console.log("[can't inc bet]");
          return false;
        }
        
        this.currentUserBalance -= val;
        this.currentBet += val;
        this.currentUserBalance = Math.round(this.currentUserBalance * 100) / 100;
        this.currentBet = Math.round(this.currentBet * 100) / 100;
        this.updateBalanceAndBet();
      }
      this.decBetTo = function(val){
        val = Number(val);
        val = Math.round(val*100) / 100;
        if (this.currentBet - val < 0){
          console.log("[can't dec bet]");
          return false;
        }
        this.currentUserBalance += val;
        this.currentBet -= val;
        this.currentUserBalance = Math.round(this.currentUserBalance * 100) / 100;
        this.currentBet = Math.round(this.currentBet * 100) / 100;
        this.updateBalanceAndBet();
      }
      this.setBetTo = function(val){
        //not number
        if (typeof(val) != 'number'){
          return 0;
        }
        val = Number(val);
        val = Math.round(val*100) / 100;
        //val should be >= 0
        if (val < 0){
          return 0;
        }
        //val should be < currentUserBalance
        if (this.currentUserBalance + this.currentBet - val < 0){
          console.log("[Not enough money. Max bet had been made.]");
          val = this.getMaxBet();
          //return 0;
        }
        //bet back to balance
        this.currentUserBalance = this.currentBet + this.currentUserBalance;
        //make bet
        this.currentBet = val;
        //balance minus bet
        this.currentUserBalance -= this.currentBet;
        //round all
        this.currentUserBalance = Math.round(this.currentUserBalance * 100) / 100;
        this.currentBet = Math.round(this.currentBet * 100) / 100;
        this.updateBalanceAndBet();
      }
      //retfresh values on page
      this.updateBalanceAndBet = function(){
        var slot = this;
        this.currentUserBalance = Math.round(this.currentUserBalance * 100) / 100;
        this.currentBet = Math.round(this.currentBet * 100) / 100;
        $('div#slots-balance').text(slot.currentUserBalance);
        $('div#slots-bet').text(slot.currentBet);
      }

      //return max bet and fill current bet field
      this.getMaxBet = function(){
        var slot = this;
        return slot.currentUserBalance + slot.currentBet;
      }
      this.makeMaxBet = function(){
        var slot = this;
        var maxBet = slot.getMaxBet();
        this.setBetTo(maxBet);
      }
      this.getLastBet = function(){
        if (this.lastBet == 0){
          console.log('[There is no last bet]');
          return 0;
        }
        var slot = this;
        return slot.lastBet;
      }
      
    }
    
    function get_cookie ( cookie_name )
    {
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
          slot.audio.spinbutton.play();
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
            console.log('autoplay');
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