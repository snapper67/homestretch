{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Homestretch implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    homestretch_homestretch.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->

<div id="diceTable">

    <div class="diceblock" >
        <!-- BEGIN dice -->
        <div class="dice_content" id="content_{DICE_VALUE}" >

            <div class="dice dice_0 dice_select" id="dice_{DICE_VALUE}" ></div>

        </div>
        <!-- END dice -->
        <div id="dice_btn_content" >
        </div>

    </div>

</div>
<div id="board">

    <div id="discs">
        <div class="token blue_token"></div>
        <div class="token green_token"></div>
        <div class="token yellow_token"></div>
        <div class="token orange_token"></div>
        <div class="token black_token"></div>
        <div class="token red_token"></div>
    </div>
    <div class="gateBlock" id="gateBlock">
        <!-- BEGIN gate -->
        <div class="gate horse_{GATE_VALUE} pos_0 black_meeple" id="horse_{GATE_VALUE}" ></div>
        <!-- END gate -->
    </div>
</div>
<div id="myhand_wrap" class="whiteblock">
    <h3>{MY_HAND}</h3>
    <div id="myhand">
    </div>
</div>
<div id="myhand_wrap" class="whiteblock">
    <h3>{DRAFT_CARD}</h3>
    <div id="mydraft">
    </div>
</div>


<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';

*/
var jstpl_dice = '<div class="dice ${dClass}" id="dice_${id}" >\ </div>';
var jstpl_rollBtn = '<a class="bgabutton bgabutton_blue" href="#" id="roll_btn"><span>${btnlabel}</span></a>';
var jstpl_rerollBtn = '<a class="bgabutton bgabutton_blue" href="#" id="reroll_btn"><span>${btnlabel}</span></a>';
var jstpl_moveBtn = '<a class="bgabutton bgabutton_blue" href="#" id="move_horse_btn"><span>${btnlabel}</span></a>';
</script>

{OVERALL_GAME_FOOTER}
