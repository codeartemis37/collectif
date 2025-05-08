<?php
$user = hash('sha256', $_GET["id"]);
$users = [
    '3206164b6398373253ecc05bca96cc39b40c957e7953b1d1b62ef1dcc75eb566', //artemis37
    'c631906619af0d1b5acd415037a740ab94d5c4d5c56962dbd5ca947dfd78dd41', //gaby
    '5f12eb0af8365d8a455cc6e08b996d6e386659e96d3157da4cecb165edfb4cd8', //h3x147
    '5014f9af3a684fdd64a775c1c4c532ee66b1b96cb56b1d02170d2249b6764f75' //invite
];
if (in_array($user, $users)) {
    $pseudochoisi=$_GET['id'];
    $connecte=$pseudochoisi;
    include('chat.php');
} else {
    echo 'not exist pseudo';
}
?>
