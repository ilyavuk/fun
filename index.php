<?php
// Temp solution
session_start();
use App\Routes;

require  __DIR__ .'/vendor/autoload.php';

Routes::Run();

