<?php

class TwitterManager {

    function __construct() {
        
    }

    /*
     * Get all tweets from database
     * $oTwitterItem-> object
     */

    public static function getAllTweets() {

        $sQuery = " SELECT
                        `t`.*
                    FROM
                        `tweets` as `t`
                    ORDER BY
                        `t`.tweetId DESC
                    ;";

        $dbc = new DatabaseCon();

        return $dbc->query($sQuery, QRY_OBJECT, "Twitter");
    }

    /*
     * Get all tweets from database
     * $oTwitterItem-> object
     */

    public static function searchTweets($search) {
        if ($search) {
            $sQuery = " SELECT
                        `t`.*
                    FROM
                        `tweets` as `t`
                        WHERE `tweet_text` LIKE '%" . $search . "%' 
                    ORDER BY
                        `t`.tweetId DESC                   
                    ;";

            $dbc = new DatabaseCon();

            return $dbc->query($sQuery, QRY_OBJECT, "Twitter");
        } else {
            return false;
        }
    }
    
        /*
     * Get all tweets from database
     * $oTwitterItem-> object
     */

    public static function ShowSearchTweets($search, $iLimiStart = 0, $iLimitEnd = 0) {
        if ($search) {
            $sQuery = " SELECT
                        `t`.*
                    FROM
                        `tweets` as `t`
                        WHERE `tweet_text` LIKE '%" . $search . "%' 
                    ORDER BY
                        `t`.tweetId DESC
                    LIMIT " . $iLimiStart . "," . $iLimitEnd . "                        
                    ;";

            $dbc = new DatabaseCon();

            return $dbc->query($sQuery, QRY_OBJECT, "Twitter");
        } else {
            return false;
        }
    }

    /*
     * Get all tweets from database
     * $oTwitterItem-> object
     */

    public static function getAllTweetsforPagination($iLimiStart = 0, $iLimitEnd = 0) {

        $sQuery = " SELECT
                        `t`.*
                    FROM
                        `tweets` as `t`
                    ORDER BY
                        `t`.tweetId DESC
                        LIMIT " . $iLimiStart . "," . $iLimitEnd . "
                    ;";

        $dbc = new DatabaseCon();

        return $dbc->query($sQuery, QRY_OBJECT, "Twitter");
    }

    /*
     * Get all tweets from database
     * $oTwitterItem-> object
     */

    public static function getTweetsByFilter(array $aFilter = array(), $iLimit = null, $iStart = 0, $aOrderBy = array('tweetId' => 'DESC')) {

        $sWhere = '';
        if (!empty($aFilter['online'])) {
            $sWhere .= ($sWhere != '' ? ' AND ' : '') . '
                            `t`.`online` = 1';
        }

        # handle order by
        $sOrderBy = '';
        if (count($aOrderBy) > 0) {
            foreach ($aOrderBy AS $sColumn => $sOrder) {
                $sOrderBy .= ($sOrderBy !== '' ? ',' : '') . '`t`.`' . $sColumn . '`' . ' ' . $sOrder;
            }
        }
        $sOrderBy = ($sOrderBy !== '' ? 'ORDER BY ' : '') . $sOrderBy;

        # handle start,limit
        $sLimit = '';
        if (is_numeric($iLimit)) {
            $sLimit .= $iLimit;
        }
        if ($sLimit !== '') {
            $sLimit = (is_numeric($iStart) ? $iStart . ',' : '0,') . $sLimit;
        }
        $sLimit = ($sLimit !== '' ? 'LIMIT ' : '') . $sLimit;


        $sQuery = " SELECT
                        `t`.*
                    FROM
                        `tweets` as `t`
                    " . ($sWhere != '' ? 'WHERE ' . $sWhere : '') . "
                    " . $sOrderBy . "
                    " . $sLimit . "
                    ;";

        $dbc = new DatabaseCon();

        return $dbc->query($sQuery, QRY_OBJECT, "Twitter");
    }

    /*
     * Delete tweet by Tweetid
     * $iTweetId -> Integer
     */

    public static function deleteTweetByTweetId($iTweetId) {

        $sQuery = " DELETE FROM 
                        tweets
                    WHERE 
                        tweetId = " . db_str($iTweetId) . "
                    ;";

        $dbc = new DatabaseCon();
        $dbc->query($sQuery, QRY_NORESULT);
    }

    /*
     * Save Tweet
     * $aTweets-> array
     */

    public static function saveTweets(array $aTweets = array()) {

        $dbc = new DatabaseCon();

        $sTweetIdsValues = '';
        foreach ($aTweets AS $oTweet) {
            $sTweetIdsValues .= ($sTweetIdsValues ? ', ' : '') . $oTweet->id_str;
        }

        $sQuery = ' DELETE FROM tweets
        WHERE tweetId NOT IN (' . $sTweetIdsValues . ');';

        $dbc->query($sQuery, QRY_NORESULT);

        foreach ($aTweets AS $oTweet) {
            $sQuery = ' INSERT INTO tweets (
                                `tweetId`,
                                `retweet`,
                                `tweet_created`,
                                `tweet_text`
                            )
                            VALUES (
                                ' . db_str($oTweet->id_str) . ',
                                ' . db_int($oTweet->retweet_count) . ',
                                ' . db_str($oTweet->created_at) . ',
                                ' . db_str($oTweet->text) . '
                            )
                    ON DUPLICATE KEY UPDATE
                            `retweet` = ' . db_int($oTweet->retweet_count) . ',
                            `tweet_created` = ' . db_str($oTweet->created_at) . ',  
                            `tweet_text` = ' . db_str($oTweet->text) . '
                            ;';

            $dbc->query($sQuery, QRY_NORESULT);
        }
    }

    /**
     * update online by tweet item id
     * @param int $bOnline
     * @param int $iTweetId
     * @return boolean
     */
    public static function updateOnlineByTweetId($bOnline, $iTweetId) {
        $sQuery = ' UPDATE
                        `tweets`
                    SET
                        `online` = ' . db_int($bOnline) . '
                    WHERE
                        `tweetId` = ' . $iTweetId . '
                    ;';
        $dbc = new DatabaseCon();
        $dbc->query($sQuery, QRY_NORESULT);

        # check if something happened
        return $dbc->affected_rows > 0;
    }

}

?>