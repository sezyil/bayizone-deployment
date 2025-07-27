<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    const DEFAULT_CURRENCY = 'tl';
    const CODE_TL = 'tl';
    const CODE_USD = 'usd';
    const CODE_EUR = 'euro';
    const CODE_GBP = 'gbp';
    const CODE_LENGTH = 10;
    use HasFactory;


    //format currency
    public static function formatCurrency($number, $code, $withSign = false): string
    {
        $currency = self::where('code', $code)->first();
        if (!$currency) {
            return $number;
        }
        $number = number_format($number, 2, ',', '.');

        if ($withSign) {
            if ($currency->symbol_right) {
                $number = $number . ' ' . $currency->sign;
            } else {
                $number = $currency->sign . ' ' . $number;
            }
        }
        return $number;
    }

    //get name from code if no parameter return current model name
    public static function getName($code): string
    {
        return self::where('code', $code)->first()->title;
    }

    //convert currency
    /**
     * @param float $amount -miktar
     * @param string $from -dönüştürülecek para birimi
     * @param string $to  -hedef para birimi
     * @return float
     */
    public static function convert($amount, $from, $to = 'tl'): float
    {
        $fromCurrency = self::where('code', $from)->first();
        $toCurrency = self::where('code', $to)->first();
        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }
        $amount = $amount * $fromCurrency->rate;
        $amount = $amount / $toCurrency->rate;
        return $amount;
    }

    //format
    public function format($number, $withSign = false): string
    {
        $number = number_format($number, 2, ',', '.');

        if ($withSign) {
            if ($this->symbol_right) {
                $number = $number . ' ' . $this->sign;
            } else {
                $number = $this->sign . ' ' . $number;
            }
        }
        return $number;
    }
}
