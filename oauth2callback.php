<?php

define('REDIRECT_AFTER_AUTH', '');

require_once(dirname(__FILE__) . '/assets/loader.php');

session_start();

new GoogleAuth($_GET['code']);

header('Location: ' . filter_var(REDIRECT_AFTER_AUTH, FILTER_SANITIZE_URL));

?>