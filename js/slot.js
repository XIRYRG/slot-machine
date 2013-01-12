function Slot(uid){
      var slot = this;
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
      this.bitcoinCheckConnectionInterval = 30000;//in ms == 30sec
      this.isBitcoinConnected = function(){
        isBitcoinConnected = false;
        /*
        $.post("AjaxRequestsProcessing.php", { slot: "bitcoin_connect"})
        .success(function(isConnected) {
          //slot.statusDisplay.clear();
          if (window.console) console.log('Bitcoind isConnected: '+isConnected);
          if (isConnected == 0){
            slot.statusDisplay.show('No connection with bitcoin server. Please, try later.', 14);
            isBitcoinConnected = false;
            return false;
          }
          if (isConnected == 1){
            slot.statusDisplay.clear();
            isBitcoinConnected = true;
            if (window.console) console.log('good '+isBitcoinConnected);
            return true;
          }
        })
        .error(function(){
          if (window.console) console.log('Bad request in isBitcoinConnected function');
        })
        .complete(function() { 
          alert("complete");
          if (window.console) console.log('Bad '+isBitcoinConnected);
        });
        */
        //return isBitcoinConnected;
        //todo: remake it
        return true;
      }
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
      //by default set the last bet before every new spin
      this.setLastBetByDefault = function(){
        if (slot.currentBet == 0){
          slot.setBetTo(slot.getLastBet());
        }
      }
      //just warm-up if have no deposit 
      this.warmup = {
        isWarmup : true,
        count : 10,
        dec : function(){
          if (this.count > 0)
            this.count -= 1;
          //if (this.count < 0)
          else
            this.isWarmup = false;
        },
        couldWin : function(){
          return "YOU could have WON "+slot.currentPayline.multiplier+" times your bet.\n Please feed me some bitcoins! :)";
        },
        couldntWin : function(){
          return "You lost nothing. Let's try for real now? :)";
        }
      }
      this.initAudio = function(){
        //var slot = this;
        slot.audio.spin5sec = document.getElementById('spin5sec');
        slot.audio.win = document.getElementById('win');
        slot.audio.loose = document.getElementById('loose');
        slot.audio.spinbutton = document.getElementById('spinbutton');
        slot.audio.buttons.element = document.getElementById('buttons');
      }
      this.getClientSeed = function(){
        //var seed_entered_by_client = parseInt($('div#verify input#client_seed').val());
        var seed_entered_by_client = $('div#verify input#client_seed').val();
        //var hashOfClientSeed = CryptoJS.SHA1(seed_entered_by_client.toString());
        var client_seed;
        //if (!seed_entered_by_client || seed_entered_by_client < 0 || seed_entered_by_client > 2147483647 ){// PHP_INT_MAX == 2147483647
        if (typeof(seed_entered_by_client) != 'string' || seed_entered_by_client.length == 0){
          client_seed = Math.round(Math.random()*2000000000);//0--2 000 000
        }
        else{
          client_seed = seed_entered_by_client;
        }
        //var hashOfClientSeed = CryptoJS.SHA1(client_seed.toString());
        //return hashOfClientSeed;
        $('div#verify input#client_seed').val(client_seed)
        return client_seed;
      }
      function isWin(){
        if (slot.currentPayline.multiplier > 0){
          return true;
        }
        else{
          return false;
        }
      }
      this.winChecker = function(){
        //in case of warmup - just first 10 times
        if (slot.warmup.isWarmup){
          //if win
          if (isWin()){
            slot.statusDisplay.show(slot.warmup.couldWin(), 13);
          }
          //if lose
          if (!isWin()){
            slot.statusDisplay.show(slot.warmup.couldntWin());
          }
        }
        //normal mode
        else {
          if (isWin()){
            var won_money = Math.round((slot.currentPayline.multiplier*slot.currentPayline.bet_from_client)*100)/100;
            slot.statusDisplay.show('You WON '+ won_money +' (Bet '+slot.currentPayline.bet_from_client+'x'+slot.currentPayline.multiplier+')');
            //$('div#slots-status-display').text('You WON '+ slot.currentPayline.multiplier*slot.currentPayline.bet_from_client +' (Bet '+slot.currentPayline.bet_from_client+'x'+slot.currentPayline.multiplier+')');
            slot.audio.win.play();
          }
          if(!isWin() && slot.currentPayline.bet_from_client){//slot.currentPayline.bet_from_client != 'undefined'
            slot.statusDisplay.show('You lost '+ slot.currentPayline.bet_from_client +'. Better luck on the next spin!');
            //$('div#slots-status-display').text('You lost '+ slot.currentPayline.bet_from_client +'. Better luck on the next spin!');
            slot.audio.loose.play();
          }
        }
      }
      this.statusDisplay = {
        //slot : this,
        get : function(){
          return $('div#slots-status-display').text();
        },
        show : function(str){
          //default font size
          var fontSize = 17;
          //if other font size given
          if (arguments.length == 2){
            //if given as string
            if (typeof arguments[1] == 'string'){
              fontSize = parseInt(arguments[1]);
            }
            if (typeof arguments[1] == 'number'){
              fontSize = arguments[1];
            }
          }
          else{
            fontSize = 17;
          }
          $('div#slots-status-display').css('font-size', fontSize);
          $('div#slots-status-display').text(str);
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
          if (window.console) console.log(options);
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
          if (window.console) console.log('Client error. Error in ajax admin-->power request, bad response');
          slot.statusDisplay.show('Connection error.');
        });
      };
      this.updateInterestingFacts = function(){
        $.post("AjaxRequestsProcessing.php", { slot: "getInterestingFacts"})
        .success(function(interestingFacts) {
          var interestingFacts = eval( "("+interestingFacts+")");
          $('td#total_cached_out').text('Cashed out money: '+ interestingFacts.cashed_out_money +' BTC');
          $('td#total_spin_number').text('Games played: '+ interestingFacts.games_played);
          //if (window.console) console.log(interestingFacts);
        })
        .error(function(){
          if (window.console) console.log('Client error. Error in ajax updateInterestingFacts request, bad response');
          slot.statusDisplay.show('Connection error.');
        });
      }
      this.checkForNewIncommingPayment = function(){
        $.post("AjaxRequestsProcessing.php", { slot: "checkForIncommingPayment"})
          //todo: try/catch and in case bad request 
        .success(function(balance) {
          if (window.console) console.log(balance);
          slot.syncWithServer();
          //slot.currentPayline = eval( "("+balance+")");
        })
        .error(function(){
          if (window.console) console.log('Client error. Error in ajax checkForIncommingPayment request, bad response');
          slot.statusDisplay.show('Connection error.');
        });
      }
      
      this.getHashedServerSeeds = function(){
        var getHashedServerSeeds;
        $.post("AjaxRequestsProcessing.php", { slot: "getHashedServerSeeds"})
        .success(function(getHashedServerSeeds){
          getHashedServerSeeds = eval( "("+getHashedServerSeeds+")");
          $('div#verify input#hashed_server_seed').val(getHashedServerSeeds);
          //if (window.console) console.log('getHashedServerSeeds = '+getHashedServerSeeds);
        })
        .error(function(){
          if (window.console) console.log('Client error. Error in ajax result server seed request, bad response');
        });
      }
      
      this.sendToServerThatSpinPressed = function(){
        //var slot = this;
        //get random client seed or entered by user
        var clientSeed = slot.getClientSeed();
        //var lastHashedServerSeeds = slot.getHashedServerSeeds();
        $.post("AjaxRequestsProcessing.php", { slot: "spinPressed", currentBet: slot.currentBet, clientSeed: clientSeed })//, function(paylineReturnedByServerSpin){
          //todo: try/catch and in case bad request 
        //})
        .success(function(arrayReturnedByServerSpin) {///*paylineReturnedByServerSpin*/
          arrayReturnedByServerSpin = eval( "("+arrayReturnedByServerSpin+")");
          //here will be other (like slot.js:238) hashed server seeds already!
          slot.getHashedServerSeeds();
          if (window.console) console.log('clientSeed = '+clientSeed);
          //when result (new payline, user win/lose) was received, client sync with server
          slot.syncWithServer();
          //if (paylineReturnedByServerSpin == 'Slot-machine powered off'){
          if (arrayReturnedByServerSpin == -1){
            slot.options.playing = 'off';
            //if (window.console) console.log('Slot-machine powered off');
            slot.statusDisplay.show('Slot machine is off');
            return false;
          }
          //if (paylineReturnedByServerSpin == '[Bet <= 0 or Bet not number.]'){
          if (arrayReturnedByServerSpin == -2){
            slot.statusDisplay.show('Bad bet');
            return false;
          }
          slot.currentPayline = arrayReturnedByServerSpin['new_payline'];
          //slot.currentResultHashedServerSeeds = arrayReturnedByServerSpin['result_hashed_seeds'];
          //slot.currentPayline = eval( "("+paylineReturnedByServerSpin+")");
          slot.fillPayLine();
          slot.rememberLastShowedSymbols();
          //if client have made bet than he stars to play with real money
          if (slot.currentPayline.bet_from_client > 0){
            slot.warmup.isWarmup = false;
          }
          
          var hashOfClientSeed = CryptoJS.SHA1(clientSeed.toString());
          hashOfClientSeed = hashOfClientSeed.toString(CryptoJS.enc.Hex);
          console.log('Hashed client seed: '+hashOfClientSeed);
          var hash1 = hashOfClientSeed+arrayReturnedByServerSpin['server_seeds'][0];
          var hash2 = hashOfClientSeed+arrayReturnedByServerSpin['server_seeds'][1];
          var hash3 = hashOfClientSeed+arrayReturnedByServerSpin['server_seeds'][2];
          hash1 = CryptoJS.SHA1(hash1.toString());
          hash2 = CryptoJS.SHA1(hash2.toString());
          hash3 = CryptoJS.SHA1(hash3.toString());
          var arr_hashes = [hash1.toString(CryptoJS.enc.Hex), hash2.toString(CryptoJS.enc.Hex), hash3.toString(CryptoJS.enc.Hex)]
          //$('div#verify input[name="hashed_server_seed"]').val(arr_hashes);
          
          if (window.console){
            console.log('-----Got New Payline------');
            console.log(arrayReturnedByServerSpin['new_payline']/*==slot.currentPayline*/);
            console.log('-----Raw server seeds------');
            console.log(arrayReturnedByServerSpin['server_seeds']);
//            console.log('-----lastHashedServerSeeds------');
//            console.log(lastHashedServerSeeds);
            console.log('-----result hash(client + server seeds)------');
            console.log(arr_hashes);
          }
        })
        .error(function(){
          if (window.console) console.log('Client error. Error in ajax spin request, bad response');
          slot.statusDisplay.show('Connection error.');
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
        //var slot = this;
        user = null;
        $.post("AjaxRequestsProcessing.php", { slot: "sync" })
        .success(function(slotValues) {
          user = eval( "("+slotValues+")");
          slot.uid = user.uid;
          slot.currentUserBalance = new Number(user.money_balance - slot.currentBet);
          setTimeout('slot.updateBalanceAndBet();', slot.maxSpinTime-100);
          //if (window.console) console.log(user);
        })
        .error(function(){
          if (window.console) console.log('Error in syncWithServer');
        });
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
        //var slot = this;
        /*
        if (!(typeof(reelNum) == 'number') || reelNum < 1 || reelNum > 3 ){
          return false;
        }
        */
        $('div.slots-line').append('<div class="slots-symbol'+ symbol +'"></div>');
      }
      //There is no spoon
      this.restoreLastShowedSymbols = function(){
        //var slot = this;
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
        //var slot = this;
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
        //var slot = this;
        
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
      //todo: rotate until result spin response got
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
        //var slot = this;
        //already started
        if (slot.state == 'started'){
          if (window.console) console.log('[Slot started already. Wait while it have stoped! ]');
          return false;
        }
        //if real money was deposited
        if (slot.currentUserBalance > 0){
          //then warmup is over
          slot.warmup.isWarmup = false;
        }
        //no bet
        if (slot.currentBet <= 0 && (slot.warmup.isWarmup == false || slot.warmup.count == 0)){
          //slot.statusDisplay.show('Please, make a bet');
          slot.statusDisplay.clear();
          if (window.console) console.log('[Current bet = 0. Not worth the trouble]');
          return false;
        }
        // -1 bet
        slot.warmup.dec();
        //restore last showed symbols if there is last show exists
        slot.linesFilling();
        if (slot.onceStarted){
            slot.restoreLastShowedSymbols();
        }
        
        slot.audio.spinbutton.play();
        slot.audio.spin5sec.play();
        slot.statusDisplay.clear();
        
        
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
        //setTimeout('slot.syncWithServer()', 0/*slot.maxSpinTime-1000*/);
        //set last bet as current bet after spin
        setTimeout('slot.setLastBetByDefault()', slot.maxSpinTime+500);
        setTimeout('slot.statusDisplay.show()', slot.maxSpinTime+100);
        //check for win
        setTimeout('slot.winChecker()', slot.maxSpinTime+100);
        slot.onceStarted = true;
        return true;
      }
      this.animateSlot = function(){
        //var slot = this;
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
        //if (window.console) console.log(val);
        //can't inc bet
        if (this.currentUserBalance - val < 0){
          if (window.console) console.log("[can't inc bet]");
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
          if (window.console) console.log("[can't dec bet]");
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
          if (window.console) console.log("[Not enough money. Max bet had been made.]");
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
        //var slot = this;
        this.currentUserBalance = Math.round(this.currentUserBalance * 100) / 100;
        this.currentBet = Math.round(this.currentBet * 100) / 100;
        $('div#slots-balance').text(slot.currentUserBalance);
        $('div#slots-bet').text(slot.currentBet);
      }

      //return max bet and fill current bet field
      this.getMaxBet = function(){
        //var slot = this;
        return slot.currentUserBalance + slot.currentBet;
      }
      this.makeMaxBet = function(){
        //var slot = this;
        var maxBet = slot.getMaxBet();
        this.setBetTo(maxBet);
      }
      this.getLastBet = function(){
        if (this.lastBet == 0){
          if (window.console) console.log('[There is no last bet]');
          return 0;
        }
        //var slot = this;
        return slot.lastBet;
      }
      
    }
    
//    onsole.log('some msg');
//    console.info('information');
//    console.warn('some warning');
//    console.error('some error');
//    console.assert(false, 'YOU FAIL');
    