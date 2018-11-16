<?php

namespace App\Entities;

class BalanceResponse
{
    private $balance;
    private $currency;

    /**
     * @param float $balance
     * @param string $currency
     */
	function __construct( $balance, $currency ) {
	    $this->balance = $balance;
	    $this->currency = $currency;
	}

	public function getBalance()
	{
	    return $this->balance;
	}

	public function getCurrency()
	{
	    switch ($this->currency)
	    {
	        case 'euros':
	        case 'EUR':
	            return 'EUR';

            default:
                return 'EUR';
	    }
	}
}