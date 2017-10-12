<?php

namespace App\Loader;

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Loader;
class CommandLoader extends Loader
{
    /**
     * @param string $whoisHost
     * @param string $domain
     * @param bool $strict
     * @return string
     * @throws ConnectionException
     */
    public function loadText($whoisHost, $domain, $strict = false)
    {
        $result = exec('whois ' . $domain, $string);

        $string = join("\n", $string);
        $string_encoding = mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true);
        $string_utf8 = mb_convert_encoding($string, "UTF-8", $string_encoding);
        return htmlspecialchars($string_utf8, ENT_COMPAT, "UTF-8", true);
    }
}