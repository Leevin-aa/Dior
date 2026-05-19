<?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';

    if ($page == 'home') {
        include "page/Home.php";
    } elseif ($page == 'product') {
        include "page/Product.php";
    }
?>