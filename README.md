# Twitter
Twitter api

This is a Twitter api for zf2/3. 

just clone into your project and provide Twitter credentials in global.php, see below:
<pre>
'twitter_credentials' => [

        'oauth_access_token' => '',
        
        'oauth_access_token_secret' => '',
        
        'consumer_key' => '',
        
        'consumer_secret' => '',
        
        'userId' => ''
        
    ]
</pre>
Add your own credentials and your good to go. See /src/Controller/IndexController.php for example
