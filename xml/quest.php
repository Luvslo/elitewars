<?php

  $mysongs = simplexml_load_file('songs.xml');
    echo $mysongs->song[0]->artist;
