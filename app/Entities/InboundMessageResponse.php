<?php

namespace App\Entities;

class InboundMessageResponse
{
    private $messageId;
    private $from;
    private $to;
    private $text;
    private $keywords;
    private $receivedAt;
    private $jsonData;

    /**
     */
	function __construct() {}

	public function setMessageId($value)
	{
	    return $this->messageId = $value;
	}
	public function getMessageId()
	{
	    return $this->messageId;
	}

	public function setFrom($value)
	{
	    return $this->from = $value;
	}
	public function getFrom()
	{
	    return $this->from;
	}

	public function setTo($value)
	{
	    return $this->to = $value;
	}
	public function getTo()
	{
	    return $this->to;
	}

	public function setText($value)
	{
	    return $this->text = $value;
	}
	public function getText()
	{
	    return $this->text;
	}

	public function setKeyword($value)
	{
	    return $this->keywords = $value;
	}
	public function getKeyword()
	{
	    return $this->keywords;
	}

	public function setReceivedAt($value)
	{
	    return $this->receivedAt = $value;
	}
	public function getReceivedAt()
	{
	    return $this->receivedAt;
	}

	public function setJsonData($value)
	{
	    return $this->jsonData = $value;
	}
	public function getJsonData()
	{
	    return $this->jsonData;
	}
}