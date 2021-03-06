<?php
session_start();
require('./includes/connection.php');
function mres($x)
{
	global $connection;
	return mysqli_real_escape_string($connection,$x);
}
function hsc($x)
{
	return htmlspecialchars($x);
}
// Files that inculde this file at the very top would NOT require 
// connection to database or session_start(), be careful.
// Initialize some vars
$superadmin_ok = false;
$admin_ok = false;
$admin_id = "";
$admin_class = "";
$admin_password = "";
// User Verify function
function evalLoggedAdmin($conx,$id,$u,$p){
	$sql = "SELECT * FROM admins WHERE id='{$id}' AND class='{$u}' AND password='{$p}' LIMIT 1";
	$query = $conx->query($sql);
	if($query == false) return false;
    $numrows = $query->num_rows;
	if($numrows > 0){
		return true;
	}
	else return false;
}
if(isset($_SESSION["userid"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]))
{
	$log_id = preg_replace('#[^0-9]#', '', $_SESSION['userid']);
	$log_username = preg_replace('#[^a-z0-9_]#i', '', $_SESSION['username']);
	$log_password = preg_replace('#[^a-z0-9_]#i', '', $_SESSION['password']);
	// Verify the user
	$admin_ok = evalLoggedAdmin($connection,$log_id,$log_username,$log_password);
}
else if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"]))
{
	$_SESSION['userid'] = preg_replace('#[^0-9]#', '', $_COOKIE['id']);
    $_SESSION['username'] = preg_replace('#[^a-z0-9_]#i', '', $_COOKIE['user']);
    $_SESSION['password'] = preg_replace('#[^a-z0-9_]#i', '', $_COOKIE['pass']);
	$log_id = $_SESSION['userid'];
	$log_username = $_SESSION['username'];
	$log_password = $_SESSION['password'];
	// Verify the user
	$admin_ok = evalLoggedAdmin($connection,$log_id,$log_username,$log_password);
	if($admin_ok == true){
		// Update their lastlogin datetime field
		$sql = "UPDATE admins SET lastlogin=now() WHERE id='$log_id' LIMIT 1";
        $query = $connection->query($sql);
	}
}
if($admin_ok == true)
{
	$admin_id = $log_id;
	$admin_class = $log_username;
	$admin_password = $log_password;
	if($admin_class == "superadmin") $superadmin_ok = true;
}
?>