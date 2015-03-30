<?php

require_once 'c:/xampp/htdocs/bamding/wpmain/wp-content/tardis/bamding_lib.php';

$details = new BandDetails('test_user');
$details->setBandName("Test User's Band");
$details->setCalendar('testuserband.com/calendar');
$details->setDraw('30 to 40');
$details->setEmail('test@user.com');
$details->setGenre('polka metal');
$details->setMusic('testuserband.com/music');
$details->setPhone('206.849.8653');
$details->setSites('facebook.com/testuerband');
$details->setSolo(false);
$details->setSoundsLike('Test Rockers, The Quality Assurers');
$details->setVideo('testuserband.com/video');
$details->setWebsite('testuserband.com');
$details->update();
