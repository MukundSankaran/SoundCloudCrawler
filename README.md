SoundCloudCrawler
=================

These PHP scripts are used to collect the friendship network of a set of users from SoundCloud.com

To make this script work, you need to do the following

Download PHP SDK for SoundCloud.com
-----------------------------------
1. Download the PHP SDK for SoundCloud.com from https://github.com/mptre/php-soundcloud

2. Copy the 'Services' folder from the extracted PHP SDK and paste it inside the folder containing the 'connect.php' and 'callback.php' file.

Register an App on SoundCloud.com
---------------------------------

1. Register an App on SoundCloud.com. 
2. You will be provided with a Client ID and Client Secret.
3. In the field for Callback URL, provide the location of the 'callback.php' file.

Update Client ID, Client Secret and Callback URL
------------------------------------------------

1. On line# 6 - 8 of 'connect.php' and line# 9 - 11 of 'callback.php', update the placeholders with the values of the Client ID, Client Secret and Callback URL for your app.

Create Database and two tables for the users and edges
------------------------------------------------------

1. Create a database and two tables as follows.

  a) Create table 'SCUser' with two fields 'S.No' which is the primary key and 'SCUserId' which is a unique key. Both are of type Long Integer

  b) Create table 'Edges' with two fields 'UserID' and 'FollowingID'. Both are of type Long Integer.

Update Database Credentials
---------------------------

1. On line# 14 - 17, update the placeholders with the database credentials. To know more on what each placeholder is for, have a look at the parameters of mysql_connect() here http://php.net/manual/en/function.mysql-connect.php

Running the code
----------------

1. Run 'connect.php'. This will take you to the authentication screen for your App. 

2. Click on Authenticate and leave the code running.

3. After a while, the code terminates and you will have the results in the two tables you created earlier.

4. The table 'SCUser' tracks all the users visited during the crawl so that we avoid going in loops by visiting a user who has been visited already.

5. The table 'Edges' contains the edges of the friendship network. Each row stores the origin(current user) and the destination(user followed by current user) of the edge.  
 
6. The code has ample comments to help you understand what each segment of code does. Feel free to modify the code and experiment with it. 