<?php
namespace App\Services\SMS;

interface ISMSService
{
    public static function checkValidPhoneNumber( $phoneNumber );

    public function sendSMS( $sender, $phone = array(), $message, $serviceProvider = null, $validityPeriodHours = 48 );

    public function getSMSInfo($id, $serviceProvider = null);

    public function getBalance($serviceProvider);

    public function getReceivedMessages($serviceProvider = null);

    public function getSMSInfoNew( $id, $serviceProvider = null );
}
?>