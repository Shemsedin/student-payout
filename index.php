<?php

require_once './vendor/autoload.php';

use StudentPayout\Student;

$test = new Student();
echo $test->displayPayout();
