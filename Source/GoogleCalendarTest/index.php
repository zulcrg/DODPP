<?php

require_once 'google-api-php-client-1.1.2/src/Google/Client.php';
require_once 'google-api-php-client-1.1.2/src/Google/Service/Calendar.php';
//require_once 'CalendarHelper.php';
session_start();
$client = new Google_Client();
$client->setApplicationName("DOODP");
//$client->setDeveloperKey("AIzaSyBBH88dIQPjcl5nIG-n1mmuQ12J7HThDBE");
$client->setClientId('846619274223-07jc4p5k1lufecik16fnosdb4s1k1uu1.apps.googleusercontent.com');
$client->setClientSecret('najk_JNjhmI64yOr5RFArylY');
$client->setRedirectUri('http://localhost/GoogleCalendarTest/index.php');
$client->setAccessType('offline');   // Gets us our refreshtoken

$client->setScopes(array('https://www.googleapis.com/auth/calendar.readonly'));


//For loging out.
if (isset($_GET['logout'])) {
    unset($_SESSION['token']);
}


// Step 2: The user accepted your access now you need to exchange it.
if (isset($_GET['code'])) {

    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

// Step 1:  The user has not authenticated we give them a link to login    
if (!isset($_SESSION['token'])) {

    $authUrl = $client->createAuthUrl();

    print "<a class='login' href='$authUrl'>Connect Me!</a>";
}
// Step 3: We have access we can now create our service
if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
    print "<a class='logout' href='" . $_SERVER['PHP_SELF'] . "?logout=1'>LogOut</a><br>";

    $service = new Google_Service_Calendar($client);

    $calendarList = $service->calendarList->listCalendarList();
    
    print_r($calendarList);
    while (true) {
        foreach ($calendarList->getItems() as $calendarListEntry) {
            echo $calendarListEntry->getSummary() . "<br>\n";
        }
        $pageToken = $calendarList->getNextPageToken();
        if ($pageToken) {
            $optParams = array('pageToken' => $pageToken);
            $calendarList = $service->calendarList->listCalendarList($optParams);
        } else {
            break;
        }
    }
}
?>