/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Homestretch implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * homestretch.js
 *
 * Homestretch user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.homestretch", ebg.core.gamegui, {
        constructor: function(){
            console.log('homestretch constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            
            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
            }
            
            // TODO: Set up your game interface here, according to "gamedatas"
            // dice on table
            for( var i in this.gamedatas.Dice )
            {
                var value = this.gamedatas.Dice[i];
                this.UpdateDice( i, value );
            }

            for( var i in this.gamedatas.Positions )
            {
                var pos = this.gamedatas.Positions[i];
                console.log(pos)
                this.UpdatePositions( parseInt( i ) + 2, parseInt( pos.progress ) );
            }
 
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();
            this.UpdateDiceBtn( gamedatas.DiceLaunch );
            // dojo.query( '.dice' ).connect( 'onclick', this, 'onDice' );
            // dojo.query("#roll_btn").connect(  'onclick', this, 'onDiceRoll' );
            // dojo.query("#move_horse_btn").connect(  'onclick', this, 'onMoveHorse' );
            // dojo.query("#reroll_btn").connect(  'onclick', this, 'onReRoll' );
            console.log( "Ending game setup" );
        },

        onDiceRoll: function( evt )
        {
            console.log("js.onDiceRoll");
            evt.preventDefault();
            dojo.stopEvent( evt );

            if( this.checkAction( 'rollDice' ) )
            {
                var dices = dojo.query( '.dice_select' );
                if( dices.length != 0 )
                {
                    var arg = "";

                    for(var i=0; i<dices.length; i++)
                    {
                        if( i!=0)
                            arg += ";";
                        var subArray = dices[i].id.split('_');
                        arg += subArray[1];
                    }

                    this.ajaxcall( "/homestretch/homestretch/roll.html", {
                        lock: true,
                        dices: arg
                    }, this, function( result ) {} );
                }
                else
                {
                    // Tell player he can't retrive no dice
                    this.showMessage( _('Please select some dice first'), 'info' );
                }
            }
        },

        onMoveHorse: function( evt )
        {
            console.log("js.onMoveHorse");
            evt.preventDefault();
            dojo.stopEvent( evt );

            if( this.checkAction( 'moveHorse' ) )
            {
                this.ajaxcall( "/homestretch/homestretch/moveHorse.html", {
                    lock: true,
                }, this, function( result ) {} );
            }
        },

        onReRoll: function( evt )
        {
            console.log("js.onReRoll");
            evt.preventDefault();
            dojo.stopEvent( evt );

            if( this.checkAction( 'reRoll' ) )
            {
                this.ajaxcall( "/homestretch/homestretch/reRoll.html", {
                    lock: true,
                }, this, function( result ) {} );
            }
        },

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */

            case 'playerTurnBegin':
                this.UpdateDiceBtn( 0 );

                break;
            // case 'playerTurnChoose':
            //     dojo.empty( 'dice_btn_content' );
            //     if( this.isCurrentPlayerActive() )
            //     {
            //         dojo.place(
            //             this.format_block('jstpl_rollBtn', {
            //                 btnlabel: _('Roll'),
            //                 dicevalue: '',
            //             }), 'dice_btn_content');
            //     }
            //     this.UpdateDiceBtn( 0 );
            //     break;
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
        case 'dummy':
                this.UpdateDiceBtn( 0 );
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */
        UpdatePositions: function( HorseId, value )
        {
            //var x =  (value-1) *71;
            var sDiceClass =  "pos_"+(value).toString();
            console.log('Updating ' + HorseId + ' and value ' + value)
            dojo.removeClass( 'horse_'+HorseId, ['pos_0','pos_1','pos_2','pos_3','pos_4','pos_5','pos_6','pos_7','pos_0','pos_9','pos_10','pos_11','pos_12'] );
            dojo.addClass( 'horse_'+HorseId, sDiceClass );
        },

        UpdateDice: function( DiceId, value )
        {
            //var x =  (value-1) *71;
            var sDiceClass =  "dice_"+(value-1).toString();

            dojo.removeClass( 'dice_'+DiceId, ['dice_0','dice_1'] );
            dojo.addClass( 'dice_'+DiceId, sDiceClass );

            //alert( "nX : " + x + " ## " + DiceId + " ## " + value );
            // player_id => direction
            //dojo.empty( 'content_'+DiceId );
            /*   dojo.place(
                   this.format_block( 'jstpl_dice', {
                       id : DiceId,
                       dClass: sDiceClass
                   } ), 'content_'+DiceId );*/
        },

        UpdateDiceBtn: function( value )
        {
            console.log('Update Dice')
            console.log(value)
            console.log(this.isCurrentPlayerActive())

            var nValue = parseInt( value );
            dojo.empty( 'dice_btn_content' );
            if (this.isCurrentPlayerActive()) {
                switch( nValue ) {
                    case 0:
                        dojo.place(
                            this.format_block('jstpl_rollBtn', {
                                btnlabel: _('Roll'),
                                dicevalue: '',
                            }), 'dice_btn_content');
                        break;
                    case 1:
                        dojo.place(
                            this.format_block('jstpl_moveBtn', {
                                btnlabel: _('Move Horse 2 spaces'),
                            }), 'dice_btn_content');
                        dojo.place(
                            this.format_block('jstpl_rerollBtn', {
                                btnlabel: _('Re-Roll'),
                            }), 'dice_btn_content');
                        break;
                }
            }
            dojo.query("#roll_btn").connect(  'onclick', this, 'onDiceRoll' );
            dojo.query("#move_horse_btn").connect(  'onclick', this, 'onMoveHorse' );
            dojo.query("#reroll_btn").connect(  'onclick', this, 'onReRoll' );
            // if (nValue === 0) {
            //     dojo.place(
            //         this.format_block('jstpl_rollBtn', {
            //             btnlabel: _('Roll'),
            //             dicevalue: '',
            //         }), 'dice_btn_content');
            // } else {
            //     if (nValue === 1 && this.isCurrentPlayerActive()) {
            //         dojo.place(
            //             this.format_block('jstpl_moveBtn', {
            //                 btnlabel: _('Move Horse 2 spaces'),
            //             }), 'dice_btn_content');
            //         dojo.place(
            //             this.format_block('jstpl_rerollBtn', {
            //                 btnlabel: _('Re-Roll'),
            //             }), 'dice_btn_content');
            //     } else {
                    // dojo.place(
                    //     this.format_block('jstpl_rollBtn', {
                    //         btnlabel: _('Done'),
                    //     }), 'dice_btn_content');
            //     }
            // }

        },
        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/homestretch/homestretch/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },        
        
        */

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your homestretch.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            //
            dojo.subscribe( 'newDice', this, "notif_newDice" );
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        notif_newDice: function( notif )
        {
            console.log('notif_newDice')
            console.log(notif)
            var anim = [];
            sum = notif.args.Total;
            for( var i in notif.args.Dices )
            {
                var value = notif.args.Dices[i];
                var sId = 'dice_'+i;
                var obj = document.getElementById( sId );

                if( (notif.args.Revived.indexOf( i ) != -1 || notif.args.Revived.length == 0 ) && obj != null  )
                {
                    dojo.addClass( sId, 'next_dice_'+((value-1).toString()) );
                    anim[i] = dojo.fx.chain( [
                        dojo.fadeOut( {
                            node: sId,
                            onEnd: function( node ) {
                                // Remove any dice class
                                dojo.removeClass( node, [ 'dice_0', 'dice_1', 'dice_2', 'dice_3', 'dice_4', 'dice_5' ] );
                                // ... and add the good one

                                var sPre =  'next_dice_';
                                var bFound = false;
                                var j=0;
                                while( j<6 && !bFound )
                                {
                                    if( dojo.hasClass( node, sPre+(j).toString()) )
                                    {
                                        dojo.removeClass( node, sPre+(j).toString() );
                                        dojo.addClass( node, 'dice_'+(j.toString()) );
                                        bFound = true;
                                    }
                                    j++;
                                }

                            }
                        } ),
                        dojo.fadeIn( { node: sId  } )

                    ] ); // end of dojo.fx.chain

                    // ... and launch the animation
                    anim[i].play();

                }
                else if( obj == null )
                {
                    //alert( i + "coco" + value );
                    this.UpdateDice( i, value );
                }
            }
            var pos = [];
            var prev = parseInt(notif.args.Previous_Total)
            if (prev > 0) {
                console.log('Moving ' + prev + ' one step')
                pos = notif.args.Positions[prev - 2];
                this.UpdatePositions(parseInt(pos.horse), parseInt(pos.progress));
            }
            if (notif.args.Launch != 1) {
                // We are using 2 -12 so offset the array
                pos = notif.args.Positions[sum - 2];
                this.UpdatePositions(parseInt(pos.horse), parseInt(pos.progress));
            }

            this.UpdateDiceBtn( notif.args.Launch );

            //dojo.query( '.dice' ).connect( 'onclick', this, 'onDice' );
            // dojo.query("#roll_btn").connect(  'onclick', this, 'onDiceRoll' );
            // dojo.query("#move_horse_btn").connect(  'onclick', this, 'onMoveHorse' );
            // dojo.query("#reroll_btn").connect(  'onclick', this, 'onReRoll' );
        },

        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
   });             
});
