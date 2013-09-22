<?php
//connect.php for SoundCloudCrawler.
//Author: Mukund Sankaran

//Store app credentials 
$clientID = "CLIENT-ID";
$clientSecret = "CLIENT-SECRET";
$callback = "CALLBACK-URL";

require_once 'Services/Soundcloud.php';

//Create client object with app credentials
$client = new Services_Soundcloud($clientID,$clientSecret,$callback);

//Redirect user to authorize url
header("Location: ".$client->getAuthorizeUrl().'&scope=non-expiring');
?>