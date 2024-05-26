<?php

function randomString(int $length = 16): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $chars.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars.= '0123456789';
    return substr(str_shuffle($chars), 0, $length);
}
