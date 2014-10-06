<?php

include 'classes/world.php';

//create the world.

$world = new World($dbh);

list ($x, $y, $z, $image) = $world->CreateWorld();

//create a div that holds an image for the sprite character, the map image, and use $x and $y as 'px' of the div.

?>
