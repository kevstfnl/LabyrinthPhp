<?php
session_start();
// 0 = AIR class open
// 1 = WALL class close 
// 2 = MOUSE
$playerPos = [1, 1];
$defaultGrid = generateMazeWithBorders(15, 11);
$ended = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["reset"])) {
        session_destroy();
    } else {
        if (!empty($_SESSION["grid"])) {
            $defaultGrid = $_SESSION["grid"];
        } else {
            $_SESSION["grid"] = $defaultGrid;
        }
        if (!empty($_SESSION["pos"])) {
            $playerPos = $_SESSION["pos"];
        } else {
            $_SESSION["pos"] = $playerPos;
        }
        if (!empty($_POST["move"])) {
            switch ($_POST["move"]) {
                case "up": {
                        $hasMove = true;
                        if ($playerPos[0] - 1 < 0 || $playerPos[0] - 1 > count($defaultGrid)) $hasMove = false; // Grid border collide
                        if ($defaultGrid[$playerPos[0] - 1][$playerPos[1]] == 1) $hasMove = false; // Walls collide
                        if ($defaultGrid[$playerPos[0] - 1][$playerPos[1]] == 2) handleWin(); // Mouse collide
                        if ($hasMove) $playerPos[0] -= 1; // handleMove
                        break;
                    }
                case "down": {
                        $hasMove = true;
                        if ($playerPos[0] + 1 < 0 || $playerPos[0] + 1 > count($defaultGrid)) $hasMove = false; // Grid border collide
                        if ($defaultGrid[$playerPos[0] + 1][$playerPos[1]] == 1) $hasMove = false;  // Walls collide
                        if ($defaultGrid[$playerPos[0] + 1][$playerPos[1]] == 2) handleWin(); // Mouse collide
                        if ($hasMove) $playerPos[0] += 1; // handleMove
                        break;
                    }
                case "right": {
                        $hasMove = true;
                        if ($playerPos[1] + 1 < 0 || $playerPos[1] + 1 > count($defaultGrid[$playerPos[0]]) - 1) $hasMove = false; // Grid border collide
                        if ($defaultGrid[$playerPos[0]][$playerPos[1] + 1] == 1) $hasMove = false; // Walls collide
                        if ($defaultGrid[$playerPos[0]][$playerPos[1] + 1] == 2) handleWin(); // Mouse collide
                        if ($hasMove) $playerPos[1] += 1; // handleMove
                        break;
                    }
                case "left": {
                        $hasMove = true;
                        if ($playerPos[1] - 1 < 0 || $playerPos[1] - 1 > count($defaultGrid[$playerPos[0]]) - 1) $hasMove = false; // Grid border collide
                        if ($defaultGrid[$playerPos[0]][$playerPos[1] - 1] == 1) $hasMove = false; // Walls collide
                        if ($defaultGrid[$playerPos[0]][$playerPos[1] - 1] == 2) handleWin(); // Mouse collide
                        if ($hasMove) $playerPos[1] -= 1; // handleMove
                        break;
                    }
            }
            $_SESSION["pos"] = $playerPos;
        }
    }
}

function handleWin()
{
    global $ended;
    $ended = true;
    session_destroy();
}

function displayGrid($aGrid, $player)
{
    $visibleArea = [
        $player[0] - 1,
        $player[0] + 1,
        $player[1] - 1,
        $player[1] + 1
    ];
    foreach ($aGrid as $y => $mescouilles) {
        foreach ($aGrid[$y] as $x => $value) {
            $visible = true;
            if ($x == $player[1] && ($y == $visibleArea[0] || $y == $visibleArea[1])) $visible = true;
            if ($y == $player[0] && ($x == $visibleArea[2] || $x == $visibleArea[3])) $visible = true;

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

function generateMazeWithBorders($width, $height)
{
    $maze = array_fill(0, $height, array_fill(0, $width, 1));
    global $playerPos;
    $pathStack = [$playerPos]; //Start point
    $maze[$playerPos[0]][$playerPos[1]] = 0;

    while (!empty($pathStack)) {
        [$x, $y] = array_pop($pathStack);
        $directions = [[1, 0], [-1, 0], [0, 1], [0, -1]];
        shuffle($directions);

        foreach ($directions as [$dx, $dy]) {
            $nextX = $x + $dx * 2;
            $nextY = $y + $dy * 2;

            if ($nextX > 0 && $nextY > 0 && $nextX < $width - 1 && $nextY < $height - 1 && $maze[$nextY][$nextX] == 1) {
                $maze[$y + $dy][$x + $dx] = 0;
                $maze[$nextY][$nextX] = 0;
                $pathStack[] = [$nextX, $nextY];
            }
        }
    }

    $maze[$height - 2][$width - 2] = 2; // End
    return $maze;
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
        *,
        ::before,
        ::after {
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
            gap: 8px;
            grid-template-rows: 1fr 100px 1fr;
            height: 100%;
        }

        .controler {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            width: 250px;
            height: 250px;
            margin: auto;
        }

        button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 24px;
            transition: scale 250ms, background-color 250ms;
        }

        button:hover {
            background-color: rgba(0, 0, 0, 0.3);
            scale: 1.1;
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

        .text {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset {
            padding: 1em;

        }

        .game {
            display: grid;
            grid-template-columns: repeat(15, 40px);
            grid-template-rows: repeat(11, 40px);
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

        .pos.mouse {
            background-color: white;
            scale: 0.5;
            border-radius: 50%;
        }

        .pos.close {
            background-color: red;
        }

        .pos.open {
            background-color: transparent;
        }

        .end {
            grid-row: 3;
            grid-column: 4;
            color: white;
            font-size: 2rem;
            font-weight: 900;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="game">
            <?php
            global $defaultGrid;
            global $playerPos;
            global $ended;
            if (!$ended) {
                displayGrid($defaultGrid, $playerPos);
            } else {
                echo '<div class="end">gg</div>';
            }

            ?>
        </div>
        <form class="text" method="post">
            <button class="reset" name="reset">Recommencer</button>
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