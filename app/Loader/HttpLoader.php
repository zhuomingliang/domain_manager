<?php

namespace App\Loader;

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Loader;
class HttpLoader extends Loader
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
            $string = '';
            $whois_server = $whoisHost;
            // If TLDs have been found
            if ($whois_server != '') {
                // if whois server serve replay over HTTP protocol instead of WHOIS protocol
                if (preg_match("/^https?:\/\//i", $whois_server)) {
                    // curl session to get whois reposnse
                    $ch = curl_init();
                    $url = $whois_server . $domain;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $data = curl_exec($ch);
                    if (curl_error($ch)) {
                        return "Connection error!";
                    } else {
                        $string = strip_tags($data);
                    }
                    curl_close($ch);
                }
                $string_encoding = mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true);
                $string_utf8 = mb_convert_encoding($string, "UTF-8", $string_encoding);
                return htmlspecialchars($string_utf8, ENT_COMPAT, "UTF-8", true);
            } else {
                return "No whois server for this tld in list!";
            }
    }
}