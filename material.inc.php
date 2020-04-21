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
 * material.inc.php
 *
 * Homestretch game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/
$this->DiceCount = 2;
$this->building_types  = array(
    1 => array( 'name' => clienttranslate('Horse 2'),
        'nametr' => self::_('2'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    2 => array( 'name' => clienttranslate('Horse 3'),
        'nametr' => self::_('3'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    3 => array( 'name' => clienttranslate('Horse 4'),
        'nametr' => self::_('4'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    4 => array( 'name' => clienttranslate('Horse 5'),
        'nametr' => self::_('5'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    5 => array( 'name' => clienttranslate('Horse 6'),
        'nametr' => self::_('6'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    6 => array( 'name' => clienttranslate('Horse 7'),
        'nametr' => self::_('7'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    7 => array( 'name' => clienttranslate('Horse 8'),
        'nametr' => self::_('8'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    8 => array( 'name' => clienttranslate('Horse 9'),
        'nametr' => self::_('9'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    9 => array( 'name' => clienttranslate('Horse 10'),
        'nametr' => self::_('10'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    10 => array( 'name' => clienttranslate('Horse 11'),
        'nametr' => self::_('11'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
    11 => array( 'name' => clienttranslate('Horse 12'),
        'nametr' => self::_('12'),
        'handicap' => 0,
        'salary' => 2,
        'position' => 1,
    ),
);



