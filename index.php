<?php

//echo '{"c":"sdfgsdfgsdfg"}';


include('Parsedown.php');
$configs = include('config.php');


$servername = $configs['SERVERNAME'];
$database = $configs['DATABASE'];
$username = $configs['USERNAME'];
$password = $configs['PASSWORD'];

$con  = mysqli_connect($servername,$username,$password,$database);
if(!$con){

    die('could not connect:'.mysqli_error($con));
}




try {
if (isset($_POST['mydata'])) {
    //echo 'hi';
    // $tt = json_decode($_POST);
    $obj = $_POST['mydata'];
    //   $decoded = json_decode($_POST);
    //  $json = $_POST;
    $dec1 = json_encode($obj);
    $dec = json_decode($dec1);



    {$questionasked = $dec->result->resolvedQuery;}
    $chatid=$dec->id;
    //$chatidd=$chatid;
    $messages = $dec->result->fulfillment->messages;
    $action = $dec->result->action;
    $datetime = $dec->timestamp;
    $questionasked = $dec->result->resolvedQuery;
    $intentid = $dec->result->metadata->intentId;
    $intentname = $dec->result->metadata->intentName;
    //$isEndOfConversation = $dec->result->metadata->endConversation;
    $speech = '';
    for ($idx = 0; $idx < count($messages); $idx++) {
        $obj = $messages[$idx];
        if ($obj->type == '0') {
            $speech = $obj->speech;
        }
    }

    $Parsedown = new Parsedown();
    $transformed = $Parsedown->text($speech);
    $response = new \stdClass();
    $response->speech = $transformed;
    $response->messages = $messages;
    //$response->isEndOfConversation = $isEndOfConversation;
    header('Content-type: application/json');
    //echo json_encode($response);
    // $json["json"] = json_encode($response);
    //echo json_encode($json);

    echo json_encode($response);
}
}catch (Exception $e) {
    $speech = $e->getMessage();
    $fulfillment = new stdClass();
    $fulfillment->speech = $speech;
    $result = new stdClass();
    $result->fulfillment = $fulfillment;
    $response = new stdClass();
    $response->result = $result;
    echo json_encode($response);
}
/*function givechatid(){
    return $chatid;

}*/
if (!($dec->result->resolvedQuery === 'yes' || $dec->result->resolvedQuery ==='YES' || $dec->result->resolvedQuery ==='Yes' || $dec->result->resolvedQuery === 'No' || $dec->result->resolvedQuery === 'NO' || $dec->result->resolvedQuery === 'no')) {
    $query = "INSERT INTO chatanalytics (idforchat,Datetime,IntentCalled,QuestionAsked) VALUES ('$chatid','$datetime','$intentname','$questionasked')";
    //echo 'huio';
    mysqli_query($con, $query);

}

if ($dec->result->resolvedQuery === 'yes' || $dec->result->resolvedQuery === 'YES' || $dec->result->resolvedQuery === 'Yes' || $dec->result->resolvedQuery === 'No' || $dec->result->resolvedQuery === 'NO' || $dec->result->resolvedQuery === 'no')
{
    $satisfaction = $dec->result->resolvedQuery;


    $result = mysqli_query($con, "SELECT MAX(sno) FROM chatanalytics");
    $row = mysqli_fetch_array($result);
    $roww = $row[0];

    $query = "UPDATE chatanalytics SET Satisfaction='$satisfaction' WHERE sno='$roww'";

    mysqli_query($con, $query);
}


?>