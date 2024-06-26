<?php
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/site.css" media="screen">
</head>


<body>

    <div class="sidebar">
        <?php
        if ($_SESSION['role'] == 'admin') { ?>
            <a href="admin.php">Home</a>
            <a href="viewAgents.php">Agent</a>
            <a href="viewProduct.php">Products</a>
            <!-- <a href="Sales.php">Sales</a> -->
            <a href="salesReports.php">Sales Report</a>
            <a href="analytic.php">Analytic</a>
            <a href="adminSetting.php">Setting</a>
        <?php } elseif ($_SESSION['role'] == 'agent') { ?>
            <a href="agent.php">Home</a>
            <a href="agentProduct.php">Product</a>
            <a href="agentSales.php">Sales</a>
            <a href="agentProfile.php">Profile</a>
        <?php } ?>

        <a href="logout.php">Logout</a>
    </div>


    <div class="content flex-vertical">
        <div class="flex-ctn" style="flex-grow: 1">
