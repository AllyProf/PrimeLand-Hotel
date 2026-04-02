<?php
$guest_type = 'tanzanian';
$totalPriceInput = 200000;
$lockedExchangeRate = 2598.78;

$totalPrice = ($guest_type === 'tanzanian') 
    ? ($totalPriceInput / $lockedExchangeRate) 
    : $totalPriceInput;

var_dump($totalPrice);
