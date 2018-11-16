<?php

namespace App\Entities;

class SMSReportResponse
{
    public $id;
    public $from;
    public $to;
    public $body;
    public $createdAt;
    public $smsCount = 0;
    public $mccMnc;
    public $price = 0;
    public $currency;
    public $status;
    public $statusMessage;
    public $dataJson;

    function __construct( $value = array() )
    {
        $value = (array) $value;

        if (! empty ( $value ['id'] ))
            $this->id = $value['id'];

        if (! empty ( $value ['from'] ))
            $this->from = $value['from'];

        if (! empty ( $value ['to'] ))
            $this->to = $value['to'];

        if (! empty ( $value ['body'] ))
            $this->body = $value['body'];

        if (! empty ( $value ['createdAt'] ))
            $this->createdAt = $value['createdAt'];

        if (! empty ( $value ['smsCount'] ))
            $this->smsCount = $value['smsCount'];

        if (! empty ( $value ['mccMnc'] ))
            $this->mccMnc = $value['mccMnc'];

        if (! empty ( $value ['price'] ))
            $this->price = $value['price'];

        if (! empty ( $value ['currency'] ))
            $this->currency = $value['currency'];

        if (! empty ( $value ['status'] ))
            $this->status = $value['status'];

        if (! empty ( $value ['statusMessage'] ))
            $this->statusMessage = $value['statusMessage'];

        if (! empty ( $value ['dataJson'] ))
            $this->dataJson = $value['dataJson'];
    }

    public function setId( $value ) {
        $this->id = $value;
    }

    public function getId() {
        return $this->id;
    }

    public function setFrom( $value ) {
        $this->from = $value;
    }

    public function getFrom() {
        return $this->from;
    }

    public function setTo( $value ) {
        $this->to = $value;
    }

    public function getTo() {
        return $this->to;
    }

    public function setBody( $value ) {
        $this->body = $value;
    }

    public function getBody() {
        return $this->body;
    }

    public function setCreatedAt( $value ) {
        $this->createdAt = $value;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setSmsCount( $value ) {
        $this->smsCount= $value;
    }

    public function getSmsCount() {
        return $this->smsCount;
    }

    public function setMccMnc( $value ) {
        $this->mccMnc = $value;
    }

    public function getMccMnc() {
        return $this->mccMnc;
    }

    public function setPrice( $value ) {
        $this->price = $value;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setCurrency( $value ) {
        $this->currency = $value;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setStatus( $value ) {
        $this->status = $value;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatusMessage( $value ) {
        $this->statusMessage= $value;
    }

    public function getStatusMessage() {
        return $this->statusMessage;
    }

    public function setDataJson( $value ) {
        $this->dataJson= $value;
    }

    public function getDataJson() {
        return $this->dataJson;
    }

}