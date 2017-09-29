<?php
namespace App\SIT;
use Twilio\Rest\Client;
// Loads the library
require_once(base_path()."/vendor/twilio/sdk/Twilio/autoload.php"); 
class SendSMS {
    // Your Account Sid and Auth Token from twilio.com/user/account
    function sendMsg($toNumber,$msg){
        $sid = "AC287218d9ee4be52bf655f52a170b90e7";
        $token = "fec5f5b147f485c4e7081739f521240b";
        $client = new Client($sid, $token);
        try{
            $client->messages->create(
                $toNumber,
                array(
                    'from' => '+12013836869',
                    'body' => $msg
                )
            );
        }
        catch(exception $e){
            print_r($e);
        }
    }
}