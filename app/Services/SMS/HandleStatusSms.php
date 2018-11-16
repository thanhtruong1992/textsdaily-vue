<?php

namespace App\Services\SMS;

use App\Services\SMS\IHandleStatusSms;

class HandleStatusSms implements IHandleStatusSms {
    public function handleConvertStatus( $status ) {
        $status = (string) $status;
        $outStatus = '';
        //
        switch ( $status ) {
            case 'PENDING':
            case 'scheduled':
            case 'sent':
            case 'buffered':
            case '0':
            case '1701':
                $outStatus = 'PENDING';
                break;
            case 'DELIVERED':
            case 'delivered':
                $outStatus = 'DELIVERED';
                break;
            case 'EXPIRED':
            case 'expired':
                $outStatus = 'EXPIRED';
                break;
            case 'REJECTED':
            case 'UNDELIVERABLE':
            case 'delivery_failed':
                $outStatus = 'FAILED';
                break;
            default:
                $outStatus = 'PENDING';
                break;
        }
        return $outStatus;
    }

    public function formatResultInfoBip( $response )
    {
        $results = array ();

        $default = array (
                'return_bulk_id'        => null,
                'return_sms_count'      => 0,
                'return_message_id'     => null,
                'return_status'         => null,
                'return_status_message' => null,
                'return_json'           => null
        );

        // For INFOBIP
        if ( $response->getMessages() ) {
            foreach ( $response->getMessages() as $message )
            {
                $data = $default;

                if ( $response->getBulkId() )
                    $data['return_bulk_id'] = $response->getBulkId();
                if ( $message->getSmsCount() )
                    $data['return_sms_count'] = $message->getSmsCount();
                if ( $message->getMessageId() )
                    $data['return_message_id'] = $message->getMessageId();
                if ( $message->getStatus() ) {
                    $data['return_status'] = $this->handleConvertStatus( $message->getStatus()->getGroupName() );
                    $data['return_status_message'] = $message->getStatus()->getDescription();
                }

                $data['return_json'] = json_encode($response);

                if ( $message->getTo() ) {
                    $results[ $message->getTo() ] = $data;
                } else {
                    $results[] = $data;
                }
            }
        }

        return $results;
    }

    public function formatResultMessageBird( $response )
    {
        $results = array ();

        $default = array (
                'return_bulk_id'        => null,
                'return_sms_count'      => 0,
                'return_message_id'     => null,
                'return_status'         => null,
                'return_status_message' => null,
                'return_json'           => null
        );

        // FOR MESSAGE BIRD
        if ( !empty($response->recipients) ) {
            $data = $default;
            $data['return_json'] = json_encode($response);
            if ( $response->getId() )
                $data['return_message_id'] = $response->getId();
            if ( $response->recipients->totalSentCount )
                $data['return_sms_count'] = $response->recipients->totalSentCount;
            if ( $response->recipients->items ) {
                foreach ( $response->recipients->items as $item ) {
                    if ( $item->status ) {
                        $data['return_status'] = $this->handleConvertStatus( $item->status );
                    }
                    if ( $item->recipient ) {
                        $results[ $item->recipient ] = $data;
                    } else {
                        $results[] = $data;
                    }
                }
            }
        }

        return $results;
    }

    /**
     * fn format result when finished send tm sms
     * param object response
     */
    public function formatResultTMSMS($response) {
        $response = (object) $response;
        $results = array ();

        $default = array (
                'return_bulk_id'        => null,
                'return_sms_count'      => 0,
                'return_message_id'     => null,
                'return_status'         => null,
                'return_status_message' => null,
                'return_json'           => null
        );

        // FOR TM SMS
        if ( !empty($response) ) {
            $data= $default;
            $data['return_json'] = json_encode($response);
            if ( !empty($response->msgid))
                $data['return_message_id'] = $response->msgid;
            if (isset($response->status))
                $data['return_status'] = $this->handleConvertStatus( $response->status );
            $results[] = $data;
        }

        return $results;
    }

    /**
     * fn format result when finished send route mobile
     * param object response
     */
    public function formatResultRouteMobile($response) {
        $results = array ();

        $default = array (
                'return_bulk_id'        => null,
                'return_sms_count'      => 0,
                'return_message_id'     => null,
                'return_status'         => null,
                'return_status_message' => null,
                'return_json'           => null
        );

        // FOR TM SMS
        if ( !empty($response) ) {
            $data= $default;
            $data['return_json'] = json_encode($response);
            if ( !empty($response[2]))
                $data['return_message_id'] = $response[2];
            if (isset($response[0]))
                $data['return_status'] = $this->handleConvertStatus( $response[0] );
            $results[] = $data;
        }

        return $results;
    }

    /**
     * fn get error message of tmsms
     * param int error code
     */
    public function getErrorCodeTMSMS($code) {
        $arrError = [
            "001" => "Absent subscriber.",
            "002" => "Handset memory capacity exceeded. Handset has run out of free memory to store new message.",
            "003" => "Equipment protocol error.",
            "004" => "Equipment not equipped with short-message capability.",
            "005" => "Unknown subscriber. The IMSI is unknown in the HLR.",
            "006" => "Illegal subscriber. The mobile station failed authentication.",
            "007" => "Teleservice not provisioned. Mobile subscription identified by the MSISDN number does include the short message service.",
            "008" => "Illegal equipment. IMEI check failed, i.e. the IMEI is either black listed or not white listed.",
            "009" => "Call barred. Operator barred the MSISDN number.",
            "010" => "Facility not supported. VLR in the PLMN does not support MT short message service.",
            "011" => "Subscriber busy for MT short message.",
            "012" => "System failure. Task cannot be completed because of a problem in another entity.",
            "013" => "Data missing. Necessary parameter is not present in the primitive.",
            "014" => "Unexpected data value. Necessary data is badly formatted in the primitive.",
            "015" => "Unidentified subscriber.",
            "016" => "Absent subscriber. No paging response.",
            "017" => "Absent subscriber. IMSI detached.",
            "018" => "Absent subscriber. Roaming restriction.",
            "047" => "Application context not supported.",
            "050" => "Temporary error received from peer.",
            "051" => "SMS malformed. SMS is not formed correctly. This error is specific to IP-based protocols like SMPP",
            "052" => "SMS expired.",
            "053" => "Insufficient credit. The user has insufficient credit/not allowed to send to that destination.",
            "054" => "Invalid destination. Receiver is not a valid number.",
            "055" => "Unable to find outbound route for this SMS.",
            "056" => "SMS buffered.",
            "057" => "Timeout waiting for response from peer.",
            "058" => "Throttling error. The user has exceeded allowed message limit.",
            "059" => "SMS suspected spam message.",
            "061" => "Subscriber blacklisted.",
            "062" => "Subscriber not white listed.",
            "069" => "Invalid sender ID.",
            "071" => "Subscriber opted out from receiving SMS.",
            "074" => "SMS rejected. Error received from peer.",
            "075" => "SMS rejected. Inappropriate SMS content.",
            "076" => "Sender ID blacklisted.",
            "077" => "Sender ID not while listed.",
            "255" => "Unknown error.",
        ];

        if(array_key_exists($code, $arrError)) {
            return $arrError[$code];
        }

        return null;
    }

    /**
     * fn get error message route mobile
     */
    public function getErrorCodeRouteMobile($code) {
        $arrError = [
            '001'  => 'Unidentified Subscriber',
            '105'  => 'Unidentified Subscriber',
            '109'  => 'Illegal Subscriber',
            '011'  => 'Teleservice not provisioned',
            '012'  => 'Illegal Equipment',
            '013'  => 'CallBarred',
            '021'  => 'Facility Not Supported',
            '027'  => 'Absent Subscriber',
            '031'  => 'SubscriberBusyForMT_SMS',
            '032'  => 'SM-Delivery Failure',
            '034'  => 'System Failure',
            '035'  => 'Data Missing',
            '036'  => 'Unexpected Data Value',
            '144'  => 'Unrecognized component',
            '145'  => 'Mistyped component',
            '146'  => 'Body structured component',
            '160'  => 'Duplicate invoke ID',
            '161'  => 'Unrecognized operation',
            '162'  => 'Mistyped parameter',
            '163'  => 'Resource limitation',
            '164'  => 'Initiating release',
            '165'  => 'Unrecognized linked ID',
            '166'  => 'Linked Response expected',
            '167'  => 'Unexpected linked operation',
            '176'  => 'Unrecognized invode ID',
            '177'  => 'Return result expected',
            '178'  => 'Mistyped parameter',
            '192'  => 'Unrecognized invoke ID',
            '193'  => 'Return error unexpected',
            '194'  => 'Unrecognized error',
            '195'  => 'Unexpected error',
            '196'  => 'Mistyped parameter',
            '200'  => 'Unable to decode response',
            '201'  => 'Provider abort',
            '202'  => 'User abort',
            '203'  => 'Timeout',
            '204'  => 'API Error',
            '205'  => 'Unknown Error',
            '408'  => 'Destination in Dnd',
            '409'  => 'Sender or Template Mismatch',
            '410'  => 'Source or Template Long Message Err',
            '411'  => 'Duplicate Message',
            '404'  => 'Message filtered in Spam content',
            '412'  => 'Destination Barred',
            '1702' => 'Invalid URL Error, This means that one of the parameters was not provided or left blank',
            '1703' => 'Invalid value in username or password field',
            '1704' => 'Invalid value in type field',
            '1705' => 'Invalid Message',
            '1706' => 'Invalid Destination',
            '1707' => 'Invalid Source (Sender)',
            '1708' => 'nvalid value for dlr field',
            '1709' => 'User validation failed',
            '1710' => 'Internal Error',
            '1025' => 'Insufficient Credit',
            '1715' => 'Response timeout'
        ];

        if(array_key_exists($code, $arrError)) {
            return $arrError[$code];
        }

        return null;
    }
}