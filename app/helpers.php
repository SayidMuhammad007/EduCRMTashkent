<?php
function format_money($amount_money)
{
    return number_format($amount_money, thousands_separator: ' ');
}
