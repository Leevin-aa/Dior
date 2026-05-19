<?php
require_once('../../config/database.php');

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'dashboard':
        include '../../page/admin/admin-page/dashboard.php';
        break;

    case 'form':
        include '../../page/admin/admin-page/form.php';
        break;
    
    default:
        # code...
        break;
}




?>