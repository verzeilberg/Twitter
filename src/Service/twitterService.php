<?php

namespace Twitter\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class twitterService implements twitterServiceInterface {

    /**
     * validate object
     */
    public function twitterFy($tweet_text) {
        $strTweet = preg_replace(" #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'<a href=\"$1\" target=\"_blank\">$3</a>$4'", $tweet_text);
        $strTweet = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a href="http://twitter.com/search?q=%23\2" target="new">#\2</a>', $strTweet);
        $strTweet = preg_replace('/(^|\s)@([a-z0-9_]+)/i', '$1<a href="http://www.twitter.com/$2" target="new">@$2</a>', $strTweet);
        return $strTweet;
    }

    /**
     * Generate twitter like date/time
     * @param timestamp
     */
    public function ShowDate($timestamp) {
        $stf = 0;
        $cur_time = time();
        $diff = $cur_time - $timestamp;
        $length = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
        for ($i = sizeof($length) - 1; ($i >= 0) && (($no = $diff / $length[$i]) <= 1); $i--)
            ;
        if ($i < 0)
            $i = 0;
        $_time = $cur_time - ($diff % $length[$i]);
        $no = floor($no);
        if ($no <> 1) {
            $phrase = array('seconden', 'minuten', 'uur', 'dagen', 'weken', 'maanden', 'jaren', 'eeuwen');
            $value = sprintf("%d %s ", $no, $phrase[$i]);
            //$phrase[$i] .='s'; $value = sprintf("%d %s ", $no, $phrase[$i]);
        } else {
            $phrase = array('seconde', 'minuut', 'uur', 'dag', 'week', 'maand', 'jaar', 'eeuw');
            $value = sprintf("%d %s ", $no, $phrase[$i]);
        }
        if (($stf == 1) && ($i >= 1) && (($cur_tm - $_time) > 0))
            $value .= time_ago($_time);
        return $value . ' geleden ';
    }

    /**
     * Get the urls from a string and put them in a array
     * @param string $string
     */
    public function getUrlsFromString($string) {
        $aProtocols = array('http:\/\/', 'https:\/\/', 'ftp:\/\/', 'news:\/\/', 'nntp:\/\/', 'telnet:\/\/', 'irc:\/\/', 'mms:\/\/', 'ed2k:\/\/', 'xmpp:', 'mailto:');
        $aSubdomains = array('www' => 'http://', 'ftp' => 'ftp://', 'irc' => 'irc://', 'jabber' => 'xmpp:');
        $sRELinks = '/(?:(' . implode('|', $aProtocols) . ')[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])|(?:(?:(?:(?:[^@:<>(){}`\'"\/\[\]\s]+:)?[^@:<>(){}`\'"\/\[\]\s]+@)?(' . implode('|', array_keys($aSubdomains)) . ')\.(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6}(?:[\/#?](?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?)|(?:(?:[^@:<>(){}`\'"\/\[\]\s]+@)?((?:(?:(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))(?:\.(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))){3})|(?:[A-Fa-f0-9:]{16,39}))|(?:(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6}))\/(?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s](?:[#?](?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?)?)|(?:[^@:<>(){}`\'"\/\[\]\s]+:[^@:<>(){}`\'"\/\[\]\s]+@((?:(?:(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))(?:\.(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))){3})|(?:[A-Fa-f0-9:]{16,39}))|(?:(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6}))(?:\/(?:(?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?)?(?:[#?](?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?))|([^@:<>(){}`\'"\/\[\]\s]+@(?:(?:(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6})|(?:(?:(?:(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))(?:\.(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))){3})|(?:[A-Fa-f0-9:]{16,39}))))(?:[^\^*\[\]{}|\\"<>\/`\s]+[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)/i';

        preg_match_all($sRELinks, $string, $matches);
        return ($matches[0]);
    }

    /**
     * Short a url to google url
     * @param string $longUrl
     */
    public function shortenUrl($longUrl) {

        define('GOOGLE_API_KEY', 'AIzaSyDIw8MCqnETlVYrH_4ekuz98PX0newMwtI');
        define('GOOGLE_ENDPOINT', 'https://www.googleapis.com/urlshortener/v1');

        // initialize the cURL connection
        $ch = curl_init(
                sprintf('%s/url?key=%s', GOOGLE_ENDPOINT, GOOGLE_API_KEY)
        );

        // tell cURL to return the data rather than outputting it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // create the data to be encoded into JSON
        $requestData = array(
            'longUrl' => $longUrl
        );

        // change the request type to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // set the form content type for JSON data
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

        // set the post body to encoded JSON data
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

        // perform the request
        $result = curl_exec($ch);
        curl_close($ch);

        // decode and return the JSON response
        $sGurl = json_decode($result, true);

        return $sGurl['id'];
    }
    
    
    public function shortenText($text, $delimiter = 120, $useHellip = false){
        if($text != null) {
            $result = substr($text, 0, $delimiter) . ($useHellip === true? '...':'');
        }
        return $result;
    }

}
