<?php
//callback.php for SoundCloudCrawler.
//Author: Mukund Sankaran

//This is from the Soundcloud PHP SDK 
require_once 'Services/Soundcloud.php';

//Store SoundCloud app credentials 
$clientID = "CLIENT-ID";
$clientSecret = "CLIENT-SECRET";
$callback = "CALLBACK-URL";

//Create client object with app credentials
$client = new Services_Soundcloud($clientID,$clientSecret,$callback);

//Get Access Token from the URL
$code = $_GET['code'];
$access_token = $client->accessToken($code);
$client->setAccessToken($access_token);

//Seed for our crawl 
$user_id = 3207;

//This variable tracks the user number and the next user
$user_cnt = 1;
$next_cnt = 2;

//This variable tracks the number of nodes visited
$visits = 0;

//We do this to allow unlimited running time
set_time_limit(0);

//We crawl the friendship network until we have visited 5000 nodes
while($visits < 10)
{
    //Fetch user information
    $user_info = json_decode(fetch_user_info('http://api.soundcloud.com/users/'.$user_id.'.json?client_id='.$clientID));    

    //Find the number of people user is following
    $user_following_cnt = $user_info->followings_count;

    //Store the user's cnt and info in the database
    store_user($user_cnt,$user_id);
    
    //Set the limit and offset for each crawl.
    $offset = 0;
    $user_following_length = 100;
    
    //find the number of times we need to run the following loop to fetch all the people the user follows
    $iterations = (int)($user_following_cnt/$user_following_length);
    
    for($i=0;$i<=$iterations;$i++)
    {
        //Get the list of people followed by the user
        $user_following = json_decode(fetch_user_info('http://api.soundcloud.com/users/'.$user_id.'/followings.json?client_id='.$clientID.'&limit=100&offset='.$offset));

        //Get number of people followed in the final iteration
        if($i == $iterations)
        {
            $user_following_length = ($user_following_cnt%$user_following_length);
        }
        
        for($j=0;$j<$user_following_length;$j++)
        {
            $user_cnt++;
            
            //Sleep for every 100 users retrieved. This is just to avoid getting blocked by the website.
            if($user_cnt % 100 == 0)
            {
                sleep(2);
            }
            
            //Get the user ID
            $following_id = $user_following[$j]->id;
            
            if($user_cnt<=50000)
            {
                //Store the user in the database
                store_user($user_cnt,$following_id);
            }
            
            //Store the network edge in the database
            store_edge($user_id,$following_id);
        }
        
        //Increase the offset to retrieve the next set of users
        $offset+=100;
    }
    
    //Fetch the id of the next user from the database
    $user_row = fetch_next_user($next_cnt);
    $user_array = mysql_fetch_row($user_row);
    $user_id = $user_array[0];
    $visits++;
    
    //Update the user number for the next user
    $user_cnt++;
    $next_cnt++;
}

//Store edges in Database
function store_edge($user_id,$following_id)
{
    //Database credentials
    $hostname = "HOSTNAME";
    $username = "USERNAME";
    $dbname = "DBNAME";
    $password = "PASSWORD";
    
    //Edge insertion queries
    mysql_connect($hostname,$username,$password) or die("Unable to Connect to Database");
    mysql_select_db($dbname);
    $query = "INSERT IGNORE INTO Edges VALUES(".$user_id.",".$following_id.");";
    mysql_query($query);
}

//Store Users in Database
function store_user($cnt,$id)
{
    //Database credentials
    $hostname = "HOSTNAME";
    $username = "USERNAME";
    $dbname = "DBNAME";
    $password = "PASSWORD";

    //User insertion queries
    mysql_connect($hostname,$username,$password) or die("Unable to Connect to Database");
    mysql_select_db($dbname);
    $query = "INSERT IGNORE INTO SCUser VALUES(".$cnt.",".$id.");";
    mysql_query($query);
}

//Get next user from Database
function fetch_next_user($cnt)
{
    //Database credentials
    $hostname = "HOSTNAME";
    $username = "USERNAME";
    $dbname = "DBNAME";
    $password = "PASSWORD";

    //User ID fetch queries
    mysql_connect($hostname,$username,$password) or die("Unable to Connect to Database");
    mysql_select_db($dbname);
    $query = "SELECT SCUserId FROM SCUser WHERE `S.No` = ".$cnt.";";  
    return mysql_query($query);   
}

//Fetch user information
function fetch_user_info($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);    
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
?>