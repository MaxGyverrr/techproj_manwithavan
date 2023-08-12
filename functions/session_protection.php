<?php
if(!isset($_SESSION)) {
    session_start();
}

function checkUserAccess()
{
    if (!isset($_SESSION['user_id'])) {
        die("Access Denied!"); // User is not authenticated
    }
}

function checkDriverAccess()
{
    if (!isset($_SESSION['user_id'])) {
        die("Access Denied!"); // User is not authenticated
    } elseif ($_SESSION['usertype'] !== 'driver') {
        die("Access Denied! You must be an driver to access this page."); // Deny access for non-admin users
    }
}

function checkAdminAccess()
{
    if (!isset($_SESSION['user_id'])) {
        die("Access Denied!"); // User is not authenticated
    } elseif ($_SESSION['usertype'] !== 'admin') {
        die("Access Denied! You must be an admin to access this page."); // Deny access for non-admin users
    }
}