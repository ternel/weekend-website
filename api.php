<?php

require_once('core.php');

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode(array('text' => Weekend::getText(), 'subtext' => Weekend::getSubText()));
