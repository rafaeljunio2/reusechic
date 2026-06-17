<?php require_once __DIR__.'/../php/config/init.php';
unset($_SESSION['admin_id'],$_SESSION['admin_nome']);
header('Location:/admin/login.php');
