<?php

$host = 'localhost';       
$user = 'root';
$password = '';
$dbname = 'projet_bibliotheque';

$connection = new mysqli($host ,$user ,$password ,$dbname);

if($connection->connect_error){
	die("Erreur de connection :".$connection->connect_error);
}    


?>