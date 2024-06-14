<?php
namespace SloxAdditionalCurrency\Struct;

use Shopware\Core\Framework\Struct\Struct;

class AdditionalCurrencyStruct extends Struct
{
    protected $currency;
    protected $additionalList;
    protected $currenciesSymbol;

    public function __construct(string $currency)
    {
        $this->currency = $currency;
        $this->currenciesSymbol = [
            "HKD" => "HK$",
            "RON" => "RON",
            "SEK" => "SEK",
            "ZAR" => "ZAR",
            "THB" => "฿",
            "SGD" => "SGD",
            "PHP" => "PHP",
            "NZD" => "NZD",
            "MYR" => "MYR",
            "MXN" => "MXN",
            "KRW" => "₩",
            "INR" => "₹",
            "ILS" => "₪",
            "IDR" => "IDR",
            "PLN" => "PLN",
            "CNY" => "CNY",
            "CAD" => "CAD",
            "BRL" => "BRL",
            "AUD" => "AUD",
            "TRY" => "TRY",
            "NOK" => "NOK",
            "ISK" => "ISK",
            "CHF" => "CHF",
            "HUF" => "HUF",
            "GBP" => "£",
            "DKK" => "DKK",
            "CZK" => "CZK",
            "USD" => "$",
            "JPY" => "¥",
            "BGN" => "BGN"
        ];
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAdditionalList(): array
    {
        return $this->additionalList;
    }

    public function setAdditionalList(array $additionalList): void
    {
        $this->additionalList = $additionalList;
    }
    public function getCurrenciesSymbol(): array
    {
        return $this->currenciesSymbol;
    }
    public function setCurrenciesSymbol(array $currenciesSymbol): void
    {
        $this->currenciesSymbol = $currenciesSymbol;
    }

}