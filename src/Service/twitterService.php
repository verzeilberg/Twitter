<?php

namespace Twitter\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class twitterService implements twitterServiceInterface {

    /**
     * validate object
     */
    public function twitterFy($tweet_text) {
        $strTweet = preg_replace(" #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'<a href=\"$1\" target=\"_blank\">$3</a>$4'",$tweet_text);
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

}
