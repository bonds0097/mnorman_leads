<?php

require('ClassLibrary/User.class.php');

var_dump(filter_var('pants', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
?>
