<?php
session_start();
session_unset();
session_destroy();
header("Location: /PHP_GolfHandicapCalculator/public/login");
exit();
?>
