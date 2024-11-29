<?php
    session_start(); 
    // 0 = AIR class open
    // 1 = WALL class close 
    // 2 = MOUSE
    $defaultGrid = [
        [0,0,1,0,1,0,0],
        [0,0,1,0,1,0,0],
        [0,0,1,0,1,0,0],
        [0,0,1,0,1,0,0],
        [0,0,1,2,1,0,0],
    ];
    $playerPos = [0, 3];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        print_r($_SESSION) ;
        if (!empty($_SESSION["pos"])) {
            $playerPos = $_SESSION["pos"];
        } else {
            $_SESSION["pos"] = $playerPos;
        }

        if (!empty($_SESSION["move"])) {
            switch ($_SESSION["move"]) {
                case "up": {

                    //Border collide
                    if ($playerPos[0] - 1 < 0 || $playerPos[0] - 1 > 4) {
                        
                    }

                    //Wall collide
                    if ($defaultGrid[$playerPos[0] - 1][$playerPos[1]] == 1) {
                        
                    }

                    if ($defaultGrid[$playerPos[0] - 1][$playerPos[1]] == 3) {
                        //Win 
                    }

                    // handleMove
                    $playerPos[0] -= 1;

                    break;
                }
    
                case "down": {

                    //Border collide
                    if ($playerPos[0] + 1 < 0 || $playerPos[0] + 1 > 4) {
                        
                    }

                    //Wall collide
                    if ($defaultGrid[$playerPos[0] + 1][$playerPos[1]] == 1) {
                        
                    }

                    if ($defaultGrid[$playerPos[0] + 1][$playerPos[1]] == 3) {
                        //Win 
                    }

                    // handleMove
                    $playerPos[0] += 1;
                    break;
                }
                case "right": {

                    //Border collide
                    if ($playerPos[1] + 1 < 0 || $playerPos[1] + 1 > 6) {
                        
                    }

                    //Wall collide
                    if ($defaultGrid[$playerPos[0]][$playerPos[1] + 1] == 1) {
                       
                    }
                    if ($defaultGrid[$playerPos[0]][$playerPos[1] + 1] == 3) {
                        //Win 
                    }

                    // handleMove
                    $playerPos[1] += 1;
                    break;
                }
                case "left": {
                    //Border collide
                    if ($playerPos[1] - 1 < 0 || $playerPos[1] - 1 > 6) {
                        
                    }

                    //Wall collide
                    if ($defaultGrid[$playerPos[0]][$playerPos[1] - 1] == 1) {
                        
                    }
                    if ($defaultGrid[$playerPos[0]][$playerPos[1] - 1] == 3) {
                        //Win 
                    }

                    // handleMove
                    $playerPos[1] -= 1;
                    break;
                }
            }
            $_SESSION["pos"] = $playerPos;
            
        }
    }

    function displayGrid($aGrid, $player) {

        $visibleArea = [
            $player[0] - 1,
            $player[0] + 1,
            $player[1] - 1, 
            $player[1] + 1
        ];
        foreach ($aGrid as $y => $mescouilles) {
       
            foreach ($aGrid[$y] as $x => $value) {

                $visible = false;
                if ($x == $player[1] && ($y == $visibleArea[0] || $y == $visibleArea[1])) {
                    $visible = true;
                }
                if ($y == $player[0] && ($x == $visibleArea[2] || $x == $visibleArea[3])) {
                    $visible = true;
                }
                
                if ($y == $player[0] && $x == $player[1]) {
                    echo '<div class="pos player"></div>';
                } else {
                    
                    if ($visible) {
                        switch ($value) {
                            case 0: {
                                echo '<div class="pos open"></div>';
                                break;
                            }
                            case 1: {
                                echo '<div class="pos close"></div>';
                                break;
                            }
                            case 2: {
                                echo '<div class="pos mouse"></div>';
                                break;
                            }
                        }
                    } else {
                        echo '<div class="pos hidden"></div>';
                    }
                }
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Laby</title>
    <style>
        *,::before,::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            height: 100svh;
            background-color: hsl(200, 50%, 20%);
        }
        #app {
            display: grid;
            grid-template-rows: 1fr 50px 1fr;
            height: 100%;
        }
        .controler {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            width: 300px;
            height: 300px;
            margin: auto;
        }
        button {
            background-color: rgba(0,0,0,0.5);
            color: white;
            border: 5px solid transparent;
            border-radius: 24px;
        }
        button:hover {
            border: 5px solid rgba(255,255,255,0.15);
        }
        .up {
            grid-row: 1;
            grid-column: 2;
        }
        .down {
            grid-row: 3;
            grid-column: 2;
        }
        .right {
            grid-row: 2;
            grid-column: 3;
        }
        .left {
            grid-row: 2;
            grid-column: 1;
        }
        .reset {
            padding: 0.5em;
            margin-inline: auto;
        }
        .game {
            display: grid;
            grid-template-columns: repeat(7, 100px);
            grid-template-rows: repeat(5, 100px);
            margin: auto;
            padding: 2em;
        }
        .pos.hidden {
            background-color: green;
            scale: 1.5;
            filter: blur(25px);
        }
        .pos.player {
            background-color: yellow;
            scale: 0.5;
            border-radius: 50%;
        }
        .pos.close {
            background-color: red;
        }
        .pos.open {
            background-color: transparent;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="game">
           <?php 
            global $defaultGrid;
            global $playerPos;
            displayGrid($defaultGrid, $playerPos);
           ?>
        </div>
        <form class="text" method="post">
            <button class="reset">Recommencer</button>
        </form>
        <form class="controler" method="post">
            <button class="up" name="move" value="up">▲</button>
            <button class="down" name="move" value="down">▼</button>
            <button class="right" name="move" value="right">►</button>
            <button class="left" name="move" value="left">◄</button>
        </form>
    </div>
</body>
</html>