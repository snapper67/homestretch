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
 * homestretch.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in homestretch_homestretch.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
  require_once( APP_BASE_PATH."view/common/game.view.php" );
  
  class view_homestretch_homestretch extends game_view
  {
    function getGameName() {
        return "homestretch";
    }    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );

        /*********** Place your code below:  ************/
        $this->tpl['DICE_TITLE'] = self::_("Dices");
        $this->page->begin_block( "homestretch_homestretch", "dice" );
        for( $i=1; $i<=2; $i++ )
        {
            $x = $i*70;
            $this->page->insert_block( "dice", array( "DICE_VALUE" => $i, "x"=>$x ) );
        }

        $this->page->begin_block( "homestretch_homestretch", "gate" );
        for( $i=2; $i<=12; $i++ )
        {
            $this->page->insert_block( "gate", array( "GATE_VALUE" => $i ) );
        }

        $this->page->begin_block( "homestretch_homestretch", "token" );
        for( $i=2; $i<=12; $i++ )
        {
            $this->page->insert_block( "token", array( "TOKEN_VALUE" => $i ) );
        }

        $this->tpl['MY_HAND'] = self::_("My hand");
        $this->tpl['DRAFT_CARD'] = self::_("Draft cards");
        $this->tpl['RACE_CARD'] = self::_("Race cards");
        /*
        
        // Examples: set the value of some element defined in your tpl file like this: {MY_VARIABLE_ELEMENT}

        // Display a specific number / string
        $this->tpl['MY_VARIABLE_ELEMENT'] = $number_to_display;

        // Display a string to be translated in all languages: 
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::_("A string to be translated");

        // Display some HTML content of your own:
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::raw( $some_html_code );
        
        */
        
        /*
        
        // Example: display a specific HTML block for each player in this game.
        // (note: the block is defined in your .tpl file like this:
        //      <!-- BEGIN myblock --> 
        //          ... my HTML code ...
        //      <!-- END myblock --> 
        

        $this->page->begin_block( "homestretch_homestretch", "myblock" );
        foreach( $players as $player )
        {
            $this->page->insert_block( "myblock", array( 
                                                    "PLAYER_NAME" => $player['player_name'],
                                                    "SOME_VARIABLE" => $some_value
                                                    ...
                                                     ) );
        }
        
        */



        /*********** Do not change anything below this line  ************/
  	}
  }
  

