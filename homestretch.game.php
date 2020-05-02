<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * Homestretch implementation : © <Your name here> <Your email address here>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * homestretch.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class Homestretch extends Table
{
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array(
            "dice_value_1" => 10,
            "dice_value_2" => 11,
            "dice_launch"  => 15,
            "actual_turn"  => 16,
            "turn_count"   => 17,
            "dice_total"   => 18,
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );

        $this->cards = self::getNew( "module.common.deck" );
        $this->cards->init( "card" );
        $this->racecards = self::getNew( "module.common.deck" );
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "homestretch";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, player_score) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $money = 50000;
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."', 50000)";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)
//        self::initStat( 'player', 'player_money', 0 );  // Init a player statistics (for all players)
        // TODO: setup the initial game situation here

        self::setGameStateInitialValue( 'dice_value_1', 1 );
        self::setGameStateInitialValue( 'dice_value_2', 1 );
        self::setGameStateInitialValue( 'turn_count', 8 );
//        self::setGameStateInitialValue( 'turn_count', self::GetTurnsCount( count($players) ) );
        self::setGameStateInitialValue( 'dice_launch', 0 );
        self::setGameStateInitialValue( 'actual_turn', 0 );

        // Insert (empty) intersections into database
        $sql = "INSERT INTO position (horse, progress) VALUES ";
        $values = array();
        for ($x = 2; $x <= 12; $x++) {
            $values[] = "($x, 0)";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );

        $redcards = array();
        $bluecards = array();

        for( $value=2; $value<=12; $value++ )   //  2, 3, 4, ... 12
        {
            $redcards[] = array( 'type' => 1, 'type_arg' => $value, 'nbr' => 2);
            $bluecards[] = array( 'type' => 2, 'type_arg' => $value, 'nbr' => 1);
        }

        $this->cards->createCards( $redcards, 'red' );
        $this->cards->createCards( $bluecards, 'blue' );

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        $result['Dice'] = self::GetDiceValues();
        $result['DiceLaunch'] =  self::getGameStateValue( 'dice_launch' );
        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        // Get horse positions
        $result['Positions'] = self::getObjectListFromDB( "SELECT horse, progress
                                                       FROM position" );

        // Cards in player hand
        $result['hand'] = $this->cards->getCardsInLocation( 'hand', $current_player_id );

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */
    function logToClient($value)
    {
        self::notifyAllPlayers( "logging", clienttranslate( 'Logged Message' ), array(
            'message' => $value,
        ) );
    }
    function GetDiceValues()
    {
        $Dice = array();

        for( $i=1; $i<=$this->DiceCount; $i++ )
        {
            $sId = "dice_value_".((string)$i);
            $Dice[$i] = self::getGameStateValue($sId );
        }
        return $Dice;
    }

    function GetSum( $Dices=null )
    {
        if( $Dices == null )
            $Dices = self::GetDiceValues();

        $nPoints = 0;
        foreach( $Dices as $dice )
        {
            $nPoints += $dice;
        }
        return $nPoints;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in homestretch.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */
    function draftCard( $card_ids )
    {
        self::checkAction( "draftCard" );

        // !! Here we have to get CURRENT player (= player who send the request) and not
        //    active player, cause we are in a multiple active player state and the "active player"
        //    correspond to nothing.
        $player_id = self::getCurrentPlayerId();

        if( count( $card_ids ) != 1 )
            throw new feException( self::_("You must take exactly 1 cards") );

        // Check if these cards are in player hands
        $cards = $this->cards->getCards( $card_ids );

        if( count( $cards ) != 1 )
            throw new feException( self::_("Some of these cards don't exist") );

        foreach( $cards as $card )
        {
            if( $card['location'] != 'hand' || $card['location_arg'] != $player_id )
                throw new feException( self::_("Some of these cards are not in your hand") );
        }



        // Allright, these cards can be given to this player
        // (note: we place the cards in some temporary location in order he can't see them before the hand starts)
        $this->cards->moveCards( $card_ids, "temporary", $player_id );

        // Notify the player so we can make these cards disapear
        self::notifyPlayer( $player_id, "draftCards", "", array(
            "cards" => $card_ids
        ) );

        // Make this player unactive now
        // (and tell the machine state to use transtion "giveCards" if all players are now unactive
        $this->gamestate->setPlayerNonMultiactive( $player_id, "draftCards" );
    }

    function roll( )
    {

        self::checkAction( 'rollDice' );

        $player_id = self::getActivePlayerId();
        $Dice = array();

        $re_rolled = array();

        $die_change = 0;
        $die_total = 0;

        for( $i=1; $i<=$this->DiceCount; $i++ )
        {
            $sId = "dice_value_".((string)$i);

            $nValue = self::getGameStateValue($sId );

            $newValue = bga_rand( 1, 6);

            if( $newValue != $nValue )
                $die_change ++;

            $nValue = $newValue;

            $Dice[ $i ] = $nValue;
            self::setGameStateValue($sId, $nValue );
            $re_rolled[] = $nValue;

            $die_total += $newValue;
        }

//        $sql = "UPDATE position SET progress=progress+2
//                    WHERE horse=" . $die_total;
//        self::DbQuery( $sql );

        $nLaunch = 1;
        self::setGameStateValue( 'dice_launch', $nLaunch );
        self::setGameStateValue( 'dice_total', $die_total );

        self::notifyAllPlayers( "newDice", clienttranslate( '${player_name} rolls and gets : ${values}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'Dices'=> $Dice,
            'Revived' => array(),
            'Positions' => self::getObjectListFromDB( "SELECT horse, progress
                                                       FROM position" ),
            'Launch' => $nLaunch,
            'values' => implode( ' / ', $re_rolled ),
            'total' => $die_total,
            'Previous_Total' => 0,
        ) );

        $this->gamestate->nextState( "rollDice" );
    }

    function reRoll( )
    {
        self::checkAction( 'reRoll' );

        $player_id = self::getActivePlayerId();
        $Dice = array();

        $re_rolled = array();

        $die_change = 0;
        $die_total = 0;

//        Record the single movement on a re roll
        $old_die_total = self::getGameStateValue( 'dice_total', 0 );
        $sql = "UPDATE position SET progress=progress+1
                    WHERE horse=" . $old_die_total;
        self::DbQuery( $sql );

        for( $i=1; $i<=$this->DiceCount; $i++ )
        {
            $sId = "dice_value_".((string)$i);

            $nValue = self::getGameStateValue($sId );

            $newValue = bga_rand( 1, 6);

            if( $newValue != $nValue )
                $die_change ++;

            $nValue = $newValue;

            $Dice[ $i ] = $nValue;
            self::setGameStateValue($sId, $nValue );
            $re_rolled[] = $nValue;

            $die_total += $newValue;
        }

        // Record the movement on the new dice
        $sql = "UPDATE position SET progress=progress+2
                    WHERE horse=" . $die_total;
        self::DbQuery( $sql );

        self::setGameStateValue( 'dice_launch', 0 );
        self::setGameStateValue( 'dice_total', $die_total );

        self::notifyAllPlayers( "newDice", clienttranslate( '${player_name} re rolls and gets : ${values}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'Dices'=> $Dice,
            'Revived' => array(),
            'Positions' => self::getObjectListFromDB( "SELECT horse, progress
                                                       FROM position" ),
            'Launch' => 0,
            'values' => implode( ' / ', $re_rolled ),
            'Total' => $die_total,
            'Previous_Total' => $old_die_total,
        ) );

        $this->gamestate->nextState( "reRoll" );
    }

    function moveHorse( )
    {
        self::checkAction( 'moveHorse' );

        $player_id = self::getActivePlayerId();
        $Dice = array();

        $re_rolled = array();

        $die_total = self::getGameStateValue( 'dice_total', 0 );

        $sql = "UPDATE position SET progress=progress+2
                    WHERE horse=" . $die_total;
        self::DbQuery( $sql );

        $nLaunch = 0;
        self::setGameStateValue( 'dice_launch', $nLaunch );

        self::notifyAllPlayers( "newDice", clienttranslate( '${player_name} rolls once' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'Dices'=> $Dice,
            'Revived' => array(),
            'Positions' => self::getObjectListFromDB( "SELECT horse, progress
                                                       FROM position" ),
            'Launch' => $nLaunch,
            'values' => implode( ' / ', $re_rolled ),
            'Total' => self::getGameStateValue( 'dice_total', 0 ),
            'Previous_Total' => 0,
        ) );

        $this->gamestate->nextState( "moveHorse" );
    }
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */
    function stNewHand()
    {

        // Take back all cards (from any location => null) to deck
        $this->cards->moveAllCardsInLocation( 'blue', "deck" );
        $this->cards->shuffle( 'deck' );

        // Deal 5 cards to each players
        // Create deck, shuffle it and give 13 initial cards
        self::logToClient("here");
        $players = self::loadPlayersBasicInfos();
//        var_dump('here');
//        die('ok');
        foreach( $players as $player_id => $player )
        {
            $cards = $this->cards->pickCards( 5, 'deck', 'draft_'.$player_id );

            self::logToClient($cards);
            // Notify player about his cards
            self::notifyPlayer( $player_id, 'newHand', '', array(
                'cards' => $cards
            ) );
        }

        $this->gamestate->nextState( "" );
    }
    function stdraftCard()
    {
        $this->gamestate->setAllPlayersMultiactive();
    }
    function stNewDice()
    {
        // TODO not shuffle the card but do a simple cut
        $players = self::loadPlayersBasicInfos();
        $player_id = self::getActivePlayerId();

        $Dice = array();

        for( $i=1; $i<=$this->DiceCount; $i++ )
        {
            $sId = "dice_value_".((string)$i);
            $nValue = bga_rand( 1, 6);
            $Dice[$i] = $nValue;
            self::setGameStateValue($sId, $nValue );

            // $Dice[$i] = 2;
            // self::setGameStateValue($sId, 2 );
        }


        self::notifyAllPlayers( "newDice", clienttranslate( '${player_name} rolls dice and get ${values}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'Dices'=> $Dice,
            'Revived' => array(),
            'Launch' => 0,
            'values' => implode( ' / ', $Dice )
        ) );

        $this->gamestate->nextState( "" );
    }

    function stEndCheck()
    {
        // $player_id = self::activeNextPlayer();
        // // self::giveExtraTime( $player_id );

        $nTurn = self::getGameStateValue( 'actual_turn' );

        if( $nTurn+1 == self::getGameStateValue( 'turn_count' ) )
        {
            $this->gamestate->nextState( "endGame" );
        }
        else
        {
            $player_id = $this->checkNextPlayer();
            $this->gamestate->changeActivePlayer( $player_id );
            self::giveExtraTime( $player_id );

            self::incGameStateValue( 'actual_turn', 1 );
            self::setGameStateValue( 'dice_launch', 0 );

            if( self::getGameStateValue( 'actual_turn' ) == self::getGameStateValue( 'turn_count' ) )
            {
                $this->gamestate->nextState( "endGame" );
            } else {
                $this->gamestate->nextState( "nextplayer" );
            }
        }
    }

    function checkNextPlayer() {
        $next_player_id= self::getActivePlayerId();
        $players =self::getPlayersNumber();


        for($i=0;$i<$players;$i++) {
            $next_player_id = self::getPlayerAfter( $next_player_id );

            $sql = "SELECT player_zombie FROM player WHERE player_id ='$next_player_id' ";
            $zombie = self::getUniqueValueFromDB($sql);

            if ( $zombie == 0) {
                return $next_player_id;
            } else {
                self::incGameStateValue( 'actual_turn', 1 );
            }
        }

        return $next_player_id;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
