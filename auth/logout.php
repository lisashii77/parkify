<?php
session_start();
session_unset();
session_destroy();
header("Location: /parkir-main/travelix/index.php");
exit;
