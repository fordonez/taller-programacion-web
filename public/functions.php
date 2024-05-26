<?php

function base64_url_decode(string $input): string|false
{
    $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $input), true);
    if ($decoded === false) {
        return false;
    }
    return $decoded;
}

