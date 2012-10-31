<?php

require_once('core.php');

$type = null;

if (isset($_GET['type']))
{
  $type = $_GET['type'];
}

$result = array('text' => Weekend::getText(), 'subtext' => Weekend::getSubText());

  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json');
  echo json_encode($result);