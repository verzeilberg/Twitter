<?php

/* Models and managers used by the Blog model */
require_once 'Support.class.php';

class Twitter extends Support {

    public $tweetId; //Id of the tweet
    public $online; //Online or offline status
    public $retweet; //count of how much the tweet is retweeted
    public $tweet_created; //Date and time tweet was created
    public $tweet_text; //The tweet itself

    /**
     * validate object
     */

    public function validate() {
        if (empty($this->tweet_text))
            $this->setPropInvalid('tweet_text');
    }

    public function twitterFy() {
        $strTweet = preg_replace(" #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'<a href=\"$1\" target=\"_blank\">$3</a>$4'", $this->tweet_text);
        $strTweet = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a href="http://twitter.com/search?q=%23\2" target="new">#\2</a>', $strTweet);
        $strTweet = preg_replace('/(^|\s)@([a-z0-9_]+)/i', '$1<a href="http://www.twitter.com/$2" target="new">@$2</a>', $strTweet);
        return $strTweet;
    }

}

?>