<?php
if (!function_exists('_i'))
{
    function _i(string $message, ...$params)
    {
        if (count($params) > 0)
        {
            return sprintf($message, ...$params);
        }
        return $message;
    }
}

if (!function_exists('errors_key'))
{
    function errors_key()
    {
        return 'errors';
    }
}
