<?php

$conn = new mysqli('127.0.0.1', 'root', '', 'test');
if ($conn->connect_error) {
    die('Ошибка подключения (' . $conn->connect_errno . ') ' . $conn->connect_error);
}
