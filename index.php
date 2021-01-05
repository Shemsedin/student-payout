<?php

require_once './vendor/autoload.php';

use StudentPayout\Student;

$student = new Student();
echo $student->displayPayout();
