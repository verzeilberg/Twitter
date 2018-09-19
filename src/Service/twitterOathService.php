<?php

namespace Twitter\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class twitterOathService implements twitterOathServiceInterface {

    private $oauth_access_token;
    private $oauth_access_token_secret;
    private $consumer_key;
    private $consumer_secret;
    private $postfields;
    private $getfield;
    private $apiUrl = 'https://api.twitter.com/1.1/';
    private $userId; //this is the user id    
    protected $oauth;
    public $url;
    protected $twitter_credentials;
    protected $twitterService;

    /**
     * Create the API access object. Requires an array of settings::     
     * oauth access token, oauth access token secret, consumer key, consumer secret     
     * These are all available by creating your own application on dev.twitter.com     
     * Requires the cURL library     
     * @param array $settings    
     */
    public function __construct($config, $ts) {
        if ($config != NULL && array_key_exists('twitter_credentials', $config)) {
            $this->oauth_access_token = $config['twitter_credentials']['oauth_access_token'];
            $this->oauth_access_token_secret = $config['twitter_credentials']['oauth_access_token_secret'];
            $this->consumer_key = $config['twitter_credentials']['consumer_key'];
            $this->consumer_secret = $config['twitter_credentials']['consumer_secret'];
            $this->userId = $config['twitter_credentials']['userId'];
        } else {
            $message = 'You did not provide the config file with the twitter credentials!';
            throw new \ErrorException($message, 0, $severity, $file, $line);
        }

        $this->twitterService = $ts;
    }

    /**

     * Set postfields array, example: array('screen_name' => 'J7mbo')

     *

     * @param array $array Array of parameters to send to API

     *

     * @return TwitterOathInstance of self for method chaining

     */
    public function setPostfields(array $array) {

        if (!is_null($this->getGetfield())) {

            throw new Exception('You can only choose get OR post fields.');
        }



        if (isset($array['status']) && substr($array['status'], 0, 1) === '@') {

            $array['status'] = sprintf("\0%s", $array['status']);
        }



        $this->postfields = $array;



        return $this;
    }

    /**

     * Set getfield string, example: '?screen_name=J7mbo'

     *

     * @param string $string Get key and value pairs as string

     *

     * @return \TwitterOathInstance of self for method chaining

     */
    public function setGetfield($string) {

        if (!is_null($this->getPostfields())) {

            throw new Exception('You can only choose get OR post fields.');
        }



        $search = array('#', ',', '+', ':');

        $replace = array('%23', '%2C', '%2B', '%3A');

        $string = str_replace($search, $replace, $string);



        $this->getfield = $string;



        return $this;
    }

    /**

     * Get getfield string (simple getter)

     *

     * @return string $this->getfields

     */
    public function getGetfield() {

        return $this->getfield;
    }

    /**

     * Get postfields array (simple getter)

     *

     * @return array $this->postfields

     */
    public function getPostfields() {

        return $this->postfields;
    }

    /**

     * Build the Oauth object using params set in construct and additionals

     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1

     *

     * @param string $url The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json

     * @param string $requestMethod Either POST or GET

     * @return \TwitterOathInstance of self for method chaining

     */
    public function buildOauth($url, $requestMethod) {

        if (!in_array(strtolower($requestMethod), array('post', 'get'))) {

            throw new Exception('Request method must be either POST or GET');
        }



        $consumer_key = $this->consumer_key;

        $consumer_secret = $this->consumer_secret;

        $oauth_access_token = $this->oauth_access_token;

        $oauth_access_token_secret = $this->oauth_access_token_secret;



        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );



        $getfield = $this->getGetfield();



        if (!is_null($getfield)) {

            $getfields = str_replace('?', '', explode('&', $getfield));

            foreach ($getfields as $g) {

                $split = explode('=', $g);

                $oauth[$split[0]] = $split[1];
            }
        }



        $base_info = $this->buildBaseString($url, $requestMethod, $oauth);

        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);

        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));

        $oauth['oauth_signature'] = $oauth_signature;



        $this->url = $url;

        $this->oauth = $oauth;



        return $this;
    }

    /**

     * Perform the actual data retrieval from the API

     *

     * @param boolean $return If true, returns data.

     *

     * @return string json If $return param is true, returns json data.

     */
    public function performRequest($return = true) {

        if (!is_bool($return)) {

            throw new Exception('performRequest parameter must be true or false');
        }



        $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');



        $getfield = $this->getGetfield();

        $postfields = $this->getPostfields();



        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true
        );



        if (!is_null($postfields)) {

            $options[CURLOPT_POSTFIELDS] = $postfields;
        } else {

            if ($getfield !== '') {

                $options[CURLOPT_URL] .= $getfield;
            }
        }



        $feed = curl_init();

        curl_setopt_array($feed, $options);

        $json = curl_exec($feed);

        curl_close($feed);



        if ($return) {

            return $json;
        }
    }

    /**

     * Private method to generate the base string used by cURL

     *

     * @param string $baseURI

     * @param string $method

     * @param array $params

     *

     * @return string Built base string

     */
    private function buildBaseString($baseURI, $method, $params) {

        $return = array();

        ksort($params);



        foreach ($params as $key => $value) {

            $return[] = "$key=" . $value;
        }



        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
    }

    /**

     * Private method to generate authorization header used by cURL

     *

     * @param array $oauth Array of oauth data generated by buildOauth()

     *

     * @return string $return Header used by cURL for request

     */
    private function buildAuthorizationHeader($oauth) {

        $return = 'Authorization: OAuth ';

        $values = array();



        foreach ($oauth as $key => $value) {

            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }



        $return .= implode(', ', $values);

        return $return;
    }

    /*

     * Get the user timeline

     * $ReTweets -> integer, include the retweets

     * $iCount -> integer, how many tweets you want get

     */

    public function getTwitterUserTimeline($ReTweets = 1, $iCount = 200) {



        $getfield = '?user_id=' . $this->userId . '&include_rts=' . $ReTweets . '&count=' . $iCount;



        //Buld url

        $url = $this->apiUrl . 'statuses/user_timeline.json';



        return json_decode($this->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest());
    }

    /*

     * Get al the Mentions Tweets of a user f.e. @verzeilberg

     * $iCount -> integer, how many tweets you want get

     */

    public function getTwitterMentiondTimeline($iCount = 800) {



        $getfield = '?count=' . $iCount;

        //Buld url

        $url = $this->apiUrl . 'statuses/mentions_timeline.json';

        return json_decode($this->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest());
    }

    /*

     * Get home timeline of a user f.e. @verzeilberg

     * $iCount -> integer, how many tweets you want to get

     * $bReplies -> boolean, true without replies or false with replies

     */

    public function getTwitterHomeTimeline($iCount = 800, $bReplies = true) {



        $getfield = '?count=' . $iCount . '&exclude_replies=' . $bReplies;

        //Buld url

        $url = $this->apiUrl . 'statuses/home_timeline.json';

        return json_decode($this->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest());
    }

    /*

     * Get retweets of a user f.e. @verzeilberg

     * $iCount -> integer, how many tweets you want  max 100

     */

    public function getRetweetsOfUser($iCount = 100) {



        $getfield = '?count=' . $iCount;

        //Buld url

        $url = $this->apiUrl . 'statuses/retweets_of_me.json';

        return json_decode($this->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest());
    }

    public function postTweetOnTwitter($sTweetMessage) {
        if (empty($sTweetMessage)) {
            return false;
        } else {
            $sTweetMessage = stripslashes($sTweetMessage);
        }
        //First check if there are URLS in the string

        $aUrls = $this->twitterService->getUrlsFromString($sTweetMessage);

        if (count($aUrls) > 0) {
            //Replace urls with google short url
            foreach ($aUrls AS $sLongUrl) {
                $sTweetMessage = str_replace($sLongUrl, $this->twitterService->shortenUrl($sLongUrl), $sTweetMessage);
            }
        }

        if (strlen($sTweetMessage) > 180) {
            return false;
        }

        /** POST fields required by the URL above. See relevant docs as above * */
        $postfields = array(
            'status' => $sTweetMessage,
        );

        /** Perform a POST request and echo the response * */
        $url = 'https://api.twitter.com/1.1/statuses/update.json';

        return json_decode($this->buildOauth($url, 'POST')->setPostfields($postfields)->performRequest());
    }

    public function getTweetById($iTweetId) {

        if (!$iTweetId)
            return false;

        $getfield = '';

        /** Perform a POST request and echo the response * */
        $url = 'https://api.twitter.com/1.1/statuses/show/' . $iTweetId . '.json';

        
        return json_decode($this->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest());
    }

    public function deleteTweetOnTwitter($iTweetId) {

        if (!$iTweetId)
            return false;



        /** POST fields required by the URL above. See relevant docs as above * */
        $postfields = array(
            'trim_user' => true,
        );

        /** Perform a POST request and echo the response * */
        $url = 'https://api.twitter.com/1.1/statuses/destroy/' . $iTweetId . '.json';

        return json_decode($this->buildOauth($url, 'POST')->setPostfields($postfields)->performRequest());
    }

    public function getTweestByHashtag($hashtag = NULL) {

        $getfield = '?q=' . $hashtag;

        //Buld url

        $url = $this->apiUrl . 'search/tweets.json?';

        return json_decode($this->setGetfield($getfield)->buildOauth($url, 'GET')->performRequest());
    }

}

?>