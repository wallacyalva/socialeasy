<?php
if(!function_exists("get_setting")){
    function get_setting($key, $value = "", $account_id, $field){
        $CI = &get_instance();
        $setting = $CI->model->get($field, INSTAGRAM_ACCOUNTS, " id = {$account_id}")->$field;
        $option = json_decode($setting);
        if(is_array($option) || is_object($option)){
            $option = (array)$option;
            if( isset($option[$key]) ){
                return row($option, $key);
            }else{
                $option[$key] = $value;
                $CI->db->update(INSTAGRAM_ACCOUNTS, array($field => json_encode($option)), array("id" => $account_id) );
                return $value;
            }
        }else{

            $option = json_encode(array($key => $value));

            $CI->db->update(INSTAGRAM_ACCOUNTS, array($field => $option),array("id" => $account_id));

            return $value;
        }
    }
}


if(!function_exists("update_setting")){

    function update_setting($key, $value, $account_id, $field = 'logs_counter'){

        $CI = &get_instance();

        $setting = $CI->model->get($field, INSTAGRAM_ACCOUNTS, "id = {$account_id}")->$field;

        $option = json_decode($setting);

        if(is_array($option) || is_object($option)){

            $option = (array)$option;

            if( isset($option[$key]) ){

                $option[$key] = $value;

                $CI->db->update(INSTAGRAM_ACCOUNTS, array($field => json_encode($option)), array("id" => $account_id) );

                return true;

            }

        }

        return false;

    }

}


if(!function_exists("row")){

    function row($data, $field){

        if(is_object($data)){

            if(isset($data->$field)){

                return $data->$field;

            }else{

                return "";

            }

        }

        if(is_array($data)){

            if(isset($data[$field])){

                return $data[$field];

            }else{

                return "";

            }

        }

    }

}


if(!function_exists("update_followed_iguser")){

    function update_followed_iguser($data=array()){

        if($data["type"]=="follow"||$data["type"]=="followback"||$data["type"]=="like_follow"){

            // add followed user

            get_setting($data["key"],strtotime(NOW), $data["account_id"],"followed_username");

        }

        // Increase counter

        $n = get_setting($data["type"], 0, $data["account_id"],"logs_counter") + 1;

        update_setting($data["type"],$n,$data["account_id"],"logs_counter");

    }

}

/**

* Compare strtotime

*/

class LowerThanFilter{

    private $limit;

    function __construct($limit){

        $this->limit = $limit;

    }

    function isLower($i){

        return $i <= $this->limit;

    }

}


if (!function_exists('divisible_cronjob')) {

    function divisible_cronjob($category='',$segment){

        $CI = &get_instance();

        $data_schedule = $CI->model->fetch("*",INSTAGRAM_SCHEDULES,"`category` ='".$category."' AND `time_post` <='".NOW."' AND `status`=5");

        if(!empty($data_schedule)){

           $numrows = count($data_schedule); 

            if(!empty($segment)){

                $segment = (int)$segment;

                if($numrows < 3){

                    $limit_per_page = $numrows;

                    $start_index = 0;

                }else{

                    $limit_per_page = floor($numrows/3);

                    $start_index = ($segment - 1) * $limit_per_page;

                    if($numrows%3 != 0 && $segment == 3){

                        $limit_per_page+= ($numrows%3);

                    }

                }

            }else{

                $limit_per_page = $numrows;

                $start_index = 0;

            }

            return $result = array(

                "limit_per_page"    => $limit_per_page,

                "start_index"       => $start_index,

            );

        }

        return false;

    }

}





function hashcheck(){

    if(EX == 1){

        return false;

    }else{

        return true;

    }

}



function file_get_contents_curl($url) {

    $ch = curl_init();



    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);

    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $data = curl_exec($ch);

    curl_close($ch);



    return $data;

}



function removeFeedVideo($result){

    if(!empty($result)){

        foreach ($result as $key => $row) {

            if($row->media_type != 1){

                unset($result[$key]);

                continue;

            }

        }

        return array_values($result);

    }

}



function removeFeedPrivate($result){

    if(!empty($result)){

        foreach ($result as $key => $row) {

            if(isset($row->user) && $row->user->is_private == 1){

                unset($result[$key]);

                continue;

            }

        }

        return array_values($result);

    }

}



function removePrivateUser($result){

    if(!empty($result)){

        foreach ($result as $key => $row) {

            if($row->is_private == 1){

                unset($result[$key]);

                continue;

            }

        }



        return array_values($result);

    }

}



function removePrivateUserComments($result){

    if(!empty($result)){

        foreach ($result as $key => $row) {

            if($row->user->is_private == 1){

                unset($result[$key]);

                continue;

            }

        }

        return array_values($result);

    }

}



function removeFeedBlackLists($feeds,$bl_tags,$bl_usernames,$bl_keywords){

    foreach ($feeds as $key => $row) {

        $check_tags=false;

        $check_usernames=false;

        $check_keywords=false;

        if(!empty($row->preview_comments)&&is_array($row->preview_comments)) {

            if(!empty($row->preview_comments[0])){

                $preview_comments_1 = $row->preview_comments[0];

                $preview_comments_1 = strtolower($preview_comments_1->text);

                if(!empty($row->preview_comments[1])){

                    $preview_comments_2 = $row->preview_comments[1];

                    $preview_comments_2 = strtolower($preview_comments_2->text);

                    $preview_comments = $preview_comments_1.' '.$preview_comments_2;

                }

            }

        }

        $caption_text="";

        if(!empty($row->caption->text)){

            $caption_text = strtolower($row->caption->text);

        }

        if(isset($preview_comments)){

            $caption_text.=' '.$preview_comments;

            $caption_hashtags=getDataFromString($caption_text,$target="hashtags");

        }

        if (!empty($row->user)) {

            $blist_usernames = $row->user;

            $blist_usernames = strtolower($blist_usernames->username);

        }

        

        if(!empty($caption_text)){

            $caption_usernames=getDataFromString($caption_text,$target="usernames");

            $caption_usernames[]="@".$blist_usernames;

            $caption_usernames = array_count_values($caption_usernames);

            $caption_usernames = array_keys($caption_usernames);

        }

        if (!empty($bl_tags)&&is_array($bl_tags)&&!empty($caption_hashtags)&&is_array($caption_hashtags)) {

            if ($check_usernames==false&&$check_keywords==false) {

                foreach ($bl_tags as $pattern) {

                    $pattern  = trim($pattern);

                    $pattern  = "#".$pattern;

                    foreach ($caption_hashtags as $val) {

                        if ($pattern==$val) {

                            $check_tags=true;

                            break;

                        }

                    }

                    if ($check_tags) {

                        break;

                    }

                }

            }

        }

        // usernames

        if(!empty($bl_usernames)&&is_array($bl_usernames)&&!empty($caption_usernames)&&is_array($caption_usernames)){

            if ($check_tags==false&&$check_keywords==false) {

                foreach ($bl_usernames as $pattern) {

                    $pattern  = trim($pattern);

                    $pattern  = "@".$pattern;

                    foreach ($caption_usernames as $val) {

                        if ($pattern==$val) {

                            $check_usernames=true;

                            break;

                        }

                    }

                    if ($check_usernames) {

                        break;

                    }

                }

            }

        }

        // keywords

        if(!empty($bl_keywords)&&is_array($bl_keywords)&&is_string($caption_text)){

            if ($check_tags==false&&$check_usernames==false) {

                $re = '/\b(?:' . join('|', array_map(function($keyword) {return preg_quote($keyword, '/'); }, $bl_keywords)) . ')\b/i';

                if (preg_match_all($re, $caption_text, $matches)>0) {

                    $check_keywords=true;

                }

            }

        }

        if ($check_tags==true||$check_usernames==true||$check_keywords==true) {

            unset($feeds[$key]);

        }

    }

    return array_values($feeds);

}





function removeUserBlackLists($users,$bl_usernames){

    if(!empty($bl_usernames)&&is_array($bl_usernames)&&!empty($users)&&is_array($users)){

        foreach ($users as $key => $value) {

            $check_usernames=false;

            foreach ($bl_usernames as $pattern) {

                $pattern  = trim($pattern);

                if ($pattern==$value->username) {

                    $check_usernames=true;

                }

            }

            if($check_usernames){

                unset($users[$key]);

            }   

        }

    }

    return array_values($users);

}



function removeUserCommentBlacklists($users,$bl_usernames){

    if(!empty($bl_usernames)&&is_array($bl_usernames)&&!empty($users)&&is_array($users)){

        foreach ($users as $key => $value) {

            $user=$value->user;

            $check_usernames=false;

            foreach ($bl_usernames as $pattern) {

                $pattern  = trim($pattern);

                if ($pattern==$user->username) {



                    $check_usernames=true;

                }

            }

            if($check_usernames){

                unset($users[$key]);

            }   

        }

    }

    return array_values($users);

}



function removeUserFollowBackBlacklist($users,$bl_usernames){

    if(!empty($bl_usernames)&&is_array($bl_usernames)){

        foreach ($users as $key => $value) {

            $check_usernames=false;

            foreach ($bl_usernames as $pattern) {

                $pattern  = trim($pattern);

                if ($pattern==$value->username) {

                    $check_usernames=true;

                }

            }

            if($check_usernames){

                unset($users[$key]);

            }   

        }

    }

    return array_values($users);

}



function removeUserUnFollowBackBlacklist($users,$bl_usernames){

    if(!empty($bl_usernames)&&is_array($bl_usernames)){

        foreach ($users as $key => $value) {

            $check_usernames=false;

            $user = explode("@",$key);

            foreach ($bl_usernames as $pattern) {

                $pattern  = trim($pattern);

                if ($pattern==$user[1]) {

                    $check_usernames=true;

                }

            }

            if($check_usernames){

                unset($users[$key]);

            }   

        }

    }

    return $users;

}



function unset_match_values($tags,$blacklist_tags){

    $blacklists = json_decode($blacklist_tags);

    $blacklist_tags = json_decode(strtolower($blacklists->bl_tags));

    $tags = (array)json_decode($tags);

    if (!empty($blacklist_tags) && is_array($blacklist_tags)&&!empty($tags)&&is_array($tags)) {

        foreach ($tags as $key => $tag) {

            foreach ($blacklist_tags as $bl) {

                if ($tag==$bl) {

                    unset($tags[$key]);

                    continue;

                }

            }

        }

    }

    return $tags=json_encode($tags);

}



function getDataFromString($string,$target){

    if ($target=="hashtags") {

        $hashtags = array();

        preg_match_all("/(#\w+)/", $string, $matches);  

        if ($matches) {

            $hashtagsArray = array_count_values($matches[0]);

            $hashtags = array_keys($hashtagsArray);

            return $hashtags;

        }

    }

    if ($target=="usernames") {

        $usernames = array();

        preg_match_all("/(@\w+)/", $string, $matches);  

        if ($matches) {

            $usernamesArray = array_count_values($matches[0]);

            $usernames = array_keys($usernamesArray);

            return $usernames;

        }

    }

}


function check_point($username,$password, $message , $i = ""){

    $CI = &get_instance();

    if(is_string($message)){

        if(strpos($message, 'proxy') !== false && strpos($message, 'Connection refused') !== false){

            //strpos($message, 'cURL error') !== false && strpos($message, '443') !== true && strpos($message, 'OpenSSL') !== true

            file_put_contents('logs.txt', $message.PHP_EOL , FILE_APPEND | LOCK_EX);

            $CI->db->update(INSTAGRAM_ACCOUNTS, array("checkpoint" => 3), array("username" => $username));

        }



        if(strpos($message, 'The password you entered is incorrect') !== false){

            $CI->db->update(INSTAGRAM_ACCOUNTS, array("checkpoint" => 2), array("username" => $username));

        }



        if(strpos($message, 'checkpoint') !== false){

            $CI->db->update(INSTAGRAM_ACCOUNTS, array("checkpoint" => 1), array("username" => $username));

        }



        if(strpos($message, 'User not logged in') !== false && $i != ""){

            try{

                $i->login($username,$password);

            } catch (Exception $e){

                if(strpos($e->getMessage(), 'checkpoint') !== false){

                    $CI->db->update(INSTAGRAM_ACCOUNTS, array("checkpoint" => 1), array("username" => $username));

                }

            }

        }



    }



}


function Instagram_Get_Avatar($username){

    try{

        $sites_html = file_get_contents('https://www.instagram.com/'.$username);



        $html = new DOMDocument();

        @$html->loadHTML($sites_html);

        $meta_og_img = null;

        //Get all meta tags and loop through them.

        foreach($html->getElementsByTagName('meta') as $meta) {

            //If the property attribute of the meta tag is og:image

            if($meta->getAttribute('property')=='og:image'){

                //Assign the value from content attribute to $meta_og_img

                $meta_og_img = $meta->getAttribute('content');

            }

        }

        return $meta_og_img;

    }catch(Exception $e){

        return BASE."assets/images/noavatar.png";

    }

}



if(!function_exists("Instagram_Loader")){

    function Instagram_Loader($username, $password,$proxy = ""){
        try {
            $ig = new \InstagramAPI\Instagram(false, false, [

            'storage'    => 'mysql',

            'dbhost'     => DB_HOST,

            'dbname'     => DB_NAME,

            'dbusername' => DB_USER,

            'dbpassword' => DB_PASS,

            'dbtablename'=> INSTAGRAM_DATA

        ]);
        $ig->setVerifySSL(false);

        if($proxy != ""){

            $ig->setProxy($proxy);

        }
        $ig->login($username, $password);
        return $ig;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            echo $msg;
            if (stripos($msg, "Login required") !== false || stripos($msg, "Challenge required") !== false) {
             $CI = &get_instance();
             $CI->db->update(INSTAGRAM_ACCOUNTS, array("checkpoint" => 1), array("username" => $username));
                                                                }
        }


    }

}

if (!function_exists('getRankToken')) {
    function getRankToken($ig){
        $rankToken = \InstagramAPI\Signatures::generateUUID();
        return $rankToken;
    }
}


if(!function_exists("Instagram_Login")){

    function Instagram_Login($username, $password, $proxy = ""){

        try {

            $ig = new \InstagramAPI\Instagram(false, false, [

            'storage'    => 'mysql',

            'dbhost'     => DB_HOST,

            'dbname'     => DB_NAME,

            'dbusername' => DB_USER,

            'dbpassword' => DB_PASS,

            'dbtablename'=> INSTAGRAM_DATA

        ]);
        $ig->setVerifySSL(false);

        if($proxy != ""){

            $ig->setProxy($proxy);

        }
        
            $ig->login($username, $password);

            

            //$ig->login($username, $password);

            $CI = &get_instance();

            $CI->db->update(INSTAGRAM_ACCOUNTS, array("checkpoint" => 0), array("username" => $username));



            return $ig;

        }

        catch ( Exception $e ) {

            return array(

                "txt"   => getInstagramMessage($e->getMessage()),

                "type"  => getInstagramMessage($e->getMessage()),

                "label" => "bg-red",

                "st"    => "error",

            );

        }

    }

}



if(!function_exists("Instagram_Search_Hashtags")){

    function Instagram_Search_Hashtags($data, $hashtag,$proxy=""){

        $i = Instagram_Loader($data->username, $data->password, $proxy);

        try{

            $result = $i->hashtag->search($hashtag);
            return json_decode($result);

        }catch(InstagramException $e){

            return $e->getMessage();

        }

    }

}



if(!function_exists("Instagram_Search_Locations")){

    function Instagram_Search_Locations($data, $lat, $lng,  $keyword, $proxy=""){

        $i = Instagram_Loader($data->username, $data->password,$proxy);

        try{

            $result = $i->location->search($lat, $lng);

            return json_decode($result);

        }catch(InstagramException $e){

            return $e->getMessage();

        }

    }

}



if(!function_exists("Instagram_Search_Usernames")){

    function Instagram_Search_Usernames($data, $username, $proxy=""){

        $i = Instagram_Loader($data->username, $data->password, $proxy);

        try{

            $result = $i->people->search($username);
            $result = json_decode($result);

            if(!empty($result)){

                $result_tmp = $result->users;

                foreach ($result_tmp as $key => $row) {

                    if($row->is_private == 1){

                        unset($result_tmp[$key]);

                        continue;

                    }

                }

                $result->users = array_values($result_tmp);

            }

            return $result;

        }catch(InstagramException $e){

            return $e->getMessage();

        }

    }

}

if(!function_exists("Instagram_Sort_Tags")){

    function Instagram_Sort_Tags($data){

        usort($data, function($a, $b) {

            if($a->media_count==$b->media_count) return 0;

            return $a->media_count < $b->media_count?1:-1;

        });

        return $data;

    }

}

if (!function_exists('Instagram_Get_Id')) {

    function Instagram_Get_Id($url){

        $link = str_replace("https://", "", $url);

        $link = str_replace("http://", "", $link);

        $link = explode("/", $link);

        if(count($link) >= 3){

            $url = $link[2];

        }else{

            $url = $url;

        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.instagram.com/oembed/?url=http://instagram.com/p/'.$url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

        curl_setopt($curl, CURLOPT_HEADER, false);

        $data = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($data);

        if(!empty($result)){

            return $result->media_id;

        }else{

            return false;

        }

    }

}

if(!function_exists("Instagram_Get_Feed")){

    function Instagram_Get_Feed($i, $type, $keyword = ""){
        if($i == ""){
            return (object)array("st" => "error");
        }
        $keyword = trim($keyword);
        $result = false;
        $rankToken = getRankToken($i);
        try {
            switch ($type) {
                case 'timeline':
                    $timeline_feed = json_decode($i->timeline->getTimelineFeed());
                    $result = array();
                    $feeds  = $timeline_feed->feed_items;
                    if(!empty($feeds) && is_array($feeds)){
                        foreach ($feeds as $key => $row) {
                            if(isset($row->media_or_ad)){
                                $result[] = $row->media_or_ad;
                            }
                        }
                    }
                    break;
                case 'popular':
                    $result = json_decode($i->discover->getPopularFeed());
                    if($result->status == "ok"){
                        $result = $result->items;
                    }
                    break;
                case 'explore_tab':
                    $explode_feed = json_decode($i->discover->getExploreFeed());
                    if($explode_feed->status == "ok"){
                        $result = array();
                        $feeds = $explode_feed->items;
                        if(!empty($feeds) && is_array($feeds)){
                            foreach ($feeds as $key => $row) {
                                if(isset($row->media)){
                                    $result[] = $row->media;
                                }
                            }
                        }
                    }
                    break;
                case 'reels_tray':
                    $reels_tray_feed = json_decode($i->story->getReelsTrayFeed());
                    if($reels_tray_feed->status == "ok"){
                        $result = $reels_tray_feed->tray[0]->items;
                    }
                    break;
                case 'your_feed':
                    $self_user_feed = json_decode($i->timeline->getSelfUserFeed());
                    if($self_user_feed->status == "ok"){
                        $result = $self_user_feed->items;
                    }
                    break;
                case 'tag':
                    $hashtag_feed = json_decode($i->hashtag->getFeed($keyword,$rankToken));
                    if($hashtag_feed->status == "ok"){
                        $result = $hashtag_feed->items;
                    }
                    break;
                case 'search_tags':
                    $search_tags = json_decode($i->hashtag->search($keyword));
                    if($search_tags->status == "ok"){
                        $result = Instagram_Sort_Tags($search_tags->results);
                    }
                    break;
                case 'search_users':
                    $search_users = json_decode($i->people->search($keyword));
                    if($search_users->status == "ok"){
                        $result = $search_users->users;
                    }
                    break;
                case 'following':
                    $following = json_decode($i->people->getSelfFollowing($rankToken));
                    if($following->status == "ok"){
                        $result = $following->users;
                    }
                    break;
                case 'followers':
                    $followers = json_decode($i->people->getSelfFollowers($rankToken));
                    if($followers->status == "ok"){
                        $result = $followers->users;
                    }
                    break;
                case 'feed':
                    $mediaId   = Instagram_Get_Id($keyword);
                    if($mediaId != ""){
                        $feed      = json_decode($i->media->getInfo($mediaId));
                        if($feed->status == "ok"){
                            $result = $feed->items[0];
                        }
                    }
                    break;
                case 'feed_by_id':
                    $feed = json_decode($i->media->getInfo($keyword));
                    if($feed->status == "ok"){
                        $result = $feed->items[0];
                    }
                    break;
                case 'user_feed':
                    $array_username = explode("|", $keyword);
                    if(count($array_username) == 2){
                        $user_feed = json_decode($i->timeline->getUserFeed($array_username[0]));
                        if($user_feed->status == "ok"){
                            $result = $user_feed->items;
                        }
                    }
                    break;
                case 'user_following':
                    $array_username = explode("|", $keyword);
                    if(count($array_username) == 2){
                        $following = json_decode($i->people->getFollowing($array_username[0],$rankToken));
                        if($following->status == "ok"){
                            $result = $following->users;
                        }
                    }
                    break;

                case 'user_followers':
                    $array_username = explode("|", $keyword);
                    if(count($array_username) == 2){
                        $followers = json_decode($i->people->getFollowers($array_username[0],$rankToken));
                        if($followers->status == "ok"){
                            $result = $followers->users;
                        }

                    }
                    break;
                case 'following_recent_activity':
                    $followback = json_decode($i->people->getRecentActivityInbox());
                    $followback = $followback->old_stories;
                    if(!empty($followback)){
                        $result = array();
                        foreach ($followback as $key => $row) {
                            if(isset($row->args->inline_follow) && $row->args->inline_follow->following != 1 && $row->args->inline_follow->outgoing_request != 1 && strpos($row->args->text, 'started following you') !== false ){
                                $result[] = $row->args->inline_follow->user_info;
                            }
                        }
                    }
                    break;
                case 'location':
                    $array_location = explode("|", $keyword);
                    if(count($array_location) == 4){
                        $location = json_decode($i->location->getFeed($array_location[3],$rankToken));
                        if($location->status == "ok"){
                            $result = $location->items;
                        }
                    }

                case 'username':

                    $follow_types  = array("user_following","user_followers");

                    $follow_index  = array_rand($follow_types);

                    $follow_type   = $follow_types[$follow_index];

                    switch ($follow_type) {

                        case 'user_following':

                            $array_username = explode("|", $keyword);

                            if(count($array_username) == 2){

                                $following = json_decode($i->people->getFollowing($array_username[0]));

                                if($following->status == "ok"){

                                    $result = $following->users;

                                }

                            }

                            break;

                        case 'user_followers':

                            $array_username = explode("|", $keyword);

                            if(count($array_username) == 2){

                                $followers = json_decode($i->people->getFollowers($array_username[0]));

                                if($followers->status == "ok"){

                                    $result = $followers->users;

                                }

                            }

                            break;

                    }

                    break;

            }

        } catch (Exception $e){

            $result = $e->getMessage();

            //check_point($i->username,$i->password, $result, $i);

        }

        return $result;

    }

}


if(!function_exists("Instagram_Genter")){

    function Instagram_Genter($fullnames){

        $app_url = "https://api.genderize.io/?";

        $names = array();

        $names_count = 0;

        $count_up = 0;

        $data_name = array();

        if(!empty($fullnames)){

            foreach ($fullnames as $key => $row) {

                $names[$names_count]["name[".$count_up."]"] = $row;

                if(count($names[$names_count]) == 10){

                    $names_count++;

                    $count_up = 0;

                }

                $count_up++;

            }

        }



        if(!empty($names)){

            foreach ($names as $key => $row) {

                $url = 'https://api.genderize.io/?'.urldecode(http_build_query($row));

                pr($url);

                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, $url);

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($curl, CURLOPT_HEADER, false);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $data = curl_exec($curl);

                curl_close($curl);

                $data = json_decode($data);

                if(!empty($data)){

                    foreach ($data as $key => $value) {

                        $data_name[] = $value;

                    }

                }

            }

        }

        pr($data_name,1);

        pr($names,1);

    }

}



if(!function_exists('Instagram_Filter')){

    function Instagram_Filter($data = array(), $filter = array(), $timezone = "", $type = "feed"){

        //$names  = array();

        //$names_count = 0;

        $filter = json_decode($filter);

        $result = array();

        if(!empty($filter) && !empty($data)){

            switch ($type) {

                case 'feed':

                    if(!empty($data) && !is_string($data)){

                        $data = removeFeedPrivate($data);

                        foreach ($data as $key => $row) {

                            //Media age

                            if($filter->media_age != "" && $timezone != ""){

                                if(isset($row->caption->created_at_utc)){

                                    $time_media = "";

                                    switch ($filter->media_age) {

                                        case 'new':

                                            $time_media = 600;

                                            break;

                                        case '1h':

                                            $time_media = 3600;

                                            break;

                                        case '12h':

                                            $time_media = 43200;

                                            break;

                                        case '1d':

                                            $time_media = 86400;

                                            break;

                                        case '3d':

                                            $time_media = 259000;

                                            break;

                                        case '1w':

                                            $time_media = 604800;

                                            break;

                                        case '2w':

                                            $time_media = 1209600;

                                            break;

                                        case '1M':

                                            $time_media = 2419200;

                                            break;

                                    }



                                    if($time_media != ""){

                                        $time_now  = strtotime(NOW);

                                        $date = new DateTime(date("Y-m-d H:i:s", $time_now), new DateTimeZone(TIMEZONE_SYSTEM));

                                        $date->setTimezone(new DateTimeZone($timezone));

                                        $time_of_user = $date->format('Y-m-d H:i:s');

                                        if(strtotime($time_of_user) - $row->caption->created_at_utc > $time_media){

                                            unset($data[$key]);

                                            continue;

                                        }

                                    }

                                }

                            }
                            //Media type
                            switch ($filter->media_type) {

                                case 'photo':

                                    if($row->media_type == 2){

                                        unset($data[$key]);

                                        continue;

                                    }

                                    break;



                                case 'video':

                                    if($row->media_type == 1){

                                        unset($data[$key]);

                                        continue;

                                    }

                                    break;

                            }
                            //Min. likes filter
                            if($row->like_count < $filter->min_likes && $filter->min_likes != 0){

                                unset($data[$key]);

                                continue;

                            }
                            //Max. likes filter
                            if($row->like_count > $filter->max_likes && $filter->max_likes != 0){

                                unset($data[$key]);

                                continue;

                            }
                            //Min. comments filter
                            if(isset($row->comment_count) && $row->comment_count < $filter->min_comments && $filter->min_comments != 0){

                                unset($data[$key]);

                                continue;

                            }

                            if(isset($row->comments_disabled) && $row->comments_disabled == 1 && $filter->min_comments != 0){

                                unset($data[$key]);

                                continue;
                            }
                            //Max. comments filter
                            if(isset($row->comment_count) && $row->comment_count > $filter->max_comments && $filter->max_comments != 0){

                                unset($data[$key]);

                                continue;

                            }

                            if(isset($row->comments_disabled) && $row->comments_disabled == 1 && $filter->max_comments != 0){

                                unset($data[$key]);

                                continue;

                            }



                            //User relation filter

                            switch ($filter->user_relation) {

                                case 'followers':

                                    if(isset($row->user->friendship_status) && is_object($row->user->friendship_status) && isset($row->user->friendship_status->followed_by) && $row->user->friendship_status->followed_by != ""){

                                        unset($data[$key]);

                                        continue;

                                    }

                                    break;



                                case 'followings':

                                    if(isset($row->user->friendship_status) && is_object($row->user->friendship_status) && isset($row->user->friendship_status->following) && $row->user->friendship_status->following != ""){

                                        unset($data[$key]);

                                        continue;

                                    }

                                    break;



                                case 'both':

                                    if(isset($row->user->friendship_status) && is_object($row->user->friendship_status) && isset($row->user->friendship_status->followed_by) && $row->user->friendship_status->followed_by != ""){

                                        unset($data[$key]);

                                        continue;

                                    }



                                    if(isset($row->user->friendship_status) && is_object($row->user->friendship_status) && isset($row->user->friendship_status->following) && $row->user->friendship_status->following != ""){

                                        unset($data[$key]);

                                        continue;

                                    }

                                    break;

                            }



                            //Get Fullname

                            /*if(isset($row->user->full_name) && $row->user->full_name != ""){

                                $remove_emoji = preg_replace('/[^\w\s]+/u','' , $row->user->full_name);

                                $remove_emoji = str_replace("_", " ", $remove_emoji);

                                if(!empty($remove_emoji != ""){

                                    $explode_name = explode(" ", $remove_emoji);

                                    $names["name[".$names_count."]"] = $explode_name[0];

                                    $names_count++;

                                }

                            }*/



                        }

                    }

                    break;



                case 'user':



            }

        }



        //Check gender

        /*Instagram_Genter($names);



        if(!empty($data)){

            foreach ($data as $key => $row) {



            }

        }*/

        return $data;

    }

}



if(!function_exists('Instagram_Filter_Item')){

    function Instagram_Filter_Item($data = array(), $filter = array(), $type = "feed", $i){
        $id = isset($data->pk)?$data->pk:$data->id;
        $userinfo = json_decode($i->people->getInfoById($id));
        $filter   = json_decode($filter);
        if(!empty($filter) && !empty($data)){
            switch ($type) {
                case 'user':

                    //User profile filter

                    switch ($filter->user_profile) {

                        case 'low':

                            if($userinfo->user->profile_pic_id == "" || (int)$userinfo->user->media_count == 0){

                                return false;

                            }

                            break;

                        case 'medium':

                            if($userinfo->user->profile_pic_id == "" || (int)$userinfo->user->media_count < 10 || $userinfo->user->full_name == ""){

                                return false;

                            }

                            break;

                        case 'height':

                            if($userinfo->user->profile_pic_id == "" || (int)$userinfo->user->media_count < 30 || $userinfo->user->full_name == "" || $userinfo->user->biography == ""){

                                return false;

                            }

                            break;

                    }



                    //Min. followers filter

                    if($userinfo->user->follower_count < $filter->min_followers && $filter->min_followers != 0){

                        return false;

                    }



                    //Max. followers filter

                    if($userinfo->user->follower_count > $filter->max_followers && $filter->max_followers != 0){

                        return false;

                    }



                    //Min. following filter

                    if($userinfo->user->following_count < $filter->min_followings && $filter->min_followings != 0){

                        return false;

                    }



                    //Max. follow filter

                    if($userinfo->user->following_count > $filter->max_followings && $filter->max_followings != 0){

                        return false;

                    }

                    break;

            }

        }

        return $data;

    }

}



if(!function_exists("Instagram_Get_Follow")){
    function Instagram_Get_Follow($i, $type, $limit = 0){
        $result = false;
        $rankToken = getRankToken($i);
        try {
            switch ($type) {
                case 'following':
                    $data = array();
                    $following = json_decode($i->people->getSelfFollowing($rankToken));
                    if($following->status == "ok"){
                        $result = array_slice($following->users,0,$limit);    
                    }
                    break;
                case 'followers':
                    $data = array();
                    $followers = json_decode($i->people->getSelfFollowers($rankToken));
                    if($followers->status == "ok"){
                        $result = array_slice($followers->users, 0,$limit); 
                    }
                    break;
            }

        } catch (Exception $e){

            $result = $e->getMessage();

        }

        return $result;

    }

}



if(!function_exists("Instagram_Post")){

    function Instagram_Post($data){

        if($data->category != 'post' && $data->category != "message"){
            $blacklists             = json_decode($data->blacklists);
            $blacklist_tags         = json_decode(strtolower($blacklists->bl_tags));
            $blacklist_usernames    = json_decode(strtolower($blacklists->bl_usernames));
            $blacklist_keywords     = json_decode(strtolower($blacklists->bl_keywords));
        }
        $spintax = new Spintax();
        $CI = &get_instance();
        $response = array();
        
        $i = Instagram_Loader($data->username, $data->password, $data->proxy);
        
        if($i == ""){
            $response = (object)array(
                "st"      => "error",
            );
            return $response;
        }
        
        if(!is_string($i)){
            switch ($data->category) {
                case 'post':
                    switch ($data->type) {
                        case 'photo':
                            try {
                                
                                if (!file_exists('app/helpers/up/')) {
                                    mkdir('app/helpers/up/', 0777, true);
                                }
                                $url = $data->image;
                                $img_path = 'app/helpers/up/'."image".$data->username.".jpg";
                                file_put_contents($img_path, file_get_contents($url));
                                $response =$i->timeline->uploadPhoto($img_path, array("caption" => $data->message));
                                $response = json_decode($response);
                                unlink($img_path);

                            } catch (Exception $e){
                                $response = $e->getMessage();
                            }

                            break;

                        case 'photocarousel':

                            try {

                                $images = json_decode($data->image);

                                if(!empty($images)){

                                    foreach ($images as $key => $image) {
                                        $image_tmp = explode(BASE,$image);

                                        $image = $image_tmp[1];

                                        $check_type = explode(".", $image);

                                        $check_type = end($check_type);

                                        $check_type = strtolower($check_type);

                                        if($check_type == "mp4"){

                                            $medias[] = array(

                                                "type" => "video",

                                                "file" => $image

                                            );

                                        }else{

                                            $medias[] = array(

                                                "type" => "photo",

                                                "file" => $image

                                            );

                                        }

                                    }

                                }

                                

                                $response =$i->timeline->uploadAlbum($medias, array("caption" => $data->message));
                                $response = json_decode($response);

                            } catch (Exception $e){

                                $response = $e->getMessage();

                            }

                            break;

                        case 'story':

                            try {
                                $image_tmp = explode(BASE,$data->image);
                                $response =$i->story->uploadPhoto($image_tmp[1], array("caption" => $data->message));
                                $response = json_decode($response);

                            } catch (Exception $e){

                                $response = $e->getMessage();

                            }

                            break;

                        case 'video':

                            $url = str_replace(BASE, "", $data->image);

                            try {

                                $response =$i->timeline->uploadVideo($url, array("caption" => $data->message));
                                $response = json_decode($response);

                                if(isset($response->fullResponse)){

                                    $response = $response->fullResponse;

                                }

                            } catch (Exception $e){

                                $response = $e->getMessage();

                            }

                            break;

                        case 'storyvideo':

                            $url = str_replace(BASE, "", $data->image);

                            try {

                                $response =$i->story->uploadVideo($url, array("caption" => $data->message));
                                $response = json_decode($response);

                                if(isset($response->fullResponse)){

                                    $response = $response->fullResponse;

                                }

                            } catch (Exception $e){

                                $response = $e->getMessage();

                            }



                            break;

                    }



                    if(isset($response->status) && $response->status == "ok"){

                        $response = array(

                            "st"      => "success",

                            "id"      => $response->media->pk,

                            "code"    => $response->media->code,

                            "txt"     => l('Post successfully')

                        );

                    }



                    if(is_string($response)){

                        $response = array(

                            "st"      => "error",

                            "txt"     => $response

                        );

                    }

                    return $response;

                    break;
                case 'like':
                    $targets          = (array)json_decode($data->title);
                    $target           = array_rand((array)json_decode($data->title));
                    $tags             = (array)json_decode($data->description);
                    $tag_index        = array_rand((array)json_decode($data->description));
                    $locations        = (array)json_decode($data->url);
                    $location_index   = array_rand((array)json_decode($data->url));
                    $usernames        = (array)json_decode($data->image);
                    $username_index   = array_rand((array)json_decode($data->image));
                    $tag              = @$spintax->process($tags[$tag_index]);
                    $location         = @$spintax->process($locations[$location_index]);
                    $username         = @$spintax->process($usernames[$username_index]);
                    switch ($target) {
                        case 'location':
                            try {
                                $feeds  = Instagram_Get_Feed($i, $target, $location);
                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");
                                if(!empty($feeds)&&is_array($feeds)){
                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }
                                if(!empty($feeds) && is_array($feeds)){
                                    $index  = array_rand($feeds);
                                    $feed   = $feeds[$index];
                                    $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                    if(empty($history)){

                                        $like = json_decode($i->media->like($feed->pk));

                                        //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                        if($like->status == "ok"){

                                            $response = array(

                                                "st"      => "success",

                                                "data"    => json_encode($feed),

                                                "code"    => $feed->code,

                                                "txt"     => l('Successfully')

                                            );

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'tag':
                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $tag);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }

                                if(!empty($feeds) && is_array($feeds)){

                                    $index  = @array_rand($feeds);

                                    $feed   = @$feeds[$index];
                                    $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                    if(empty($history)){
                                        $like = json_decode($i->media->like($feed->pk));
                                        //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                        if($like->status == "ok"){
                                            $response = array(
                                                "st"      => "success",
                                                "data"    => json_encode($feed),
                                                "code"    => $feed->code,
                                                "txt"     => l('Successfully')
                                            );

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'username':

                            try {

                                $feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }

                                if(!empty($feeds) && is_array($feeds)){

                                    $index  = array_rand($feeds);

                                    $feed   = $feeds[$index];

                                    $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                    if(empty($history)){

                                        $like = json_decode($i->media->like($feed->pk));

                                        //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                        if($like->status == "ok"){

                                            $response = array(

                                                "st"      => "success",

                                                "data"    => json_encode($feed),

                                                "code"    => $feed->code,

                                                "txt"     => l('Successfully')

                                            );

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'followers':
                            try {
                                switch ((int)$targets['followers']) {
                                    case 1:
                                        //Usernames
                                        $users  = Instagram_Get_Feed($i, "user_followers", $username);
                                        break;
                                    case 2:
                                        //My Account
                                        $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);
                                        break;
                                    case 3:
                                        //All
                                        switch (rand(1, 2)) {
                                            case 2:
                                                $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                                break;
                                            case 1:
                                                $users  = Instagram_Get_Feed($i, "user_followers", $username);
                                                break;
                                        }
                                        break;
                                }
                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){
                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index];
                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                    if(!empty($feeds)&&is_array($feeds)){

                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                    }

                                    if(!empty($feeds) && is_array($feeds)){

                                        $index  = array_rand($feeds);
                                        $feed   = $feeds[$index];
                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        if(empty($history)){
                                            $like = json_decode($i->media->like($feed->pk));
                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";
                                            if($like->status == "ok"){
                                                $response = array(
                                                    "st"      => "success",
                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')
                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'followings':
                            try {
                                switch ((int)$targets['followings']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_following", $username);

                                        break;
                                    case 2:
                                        //My Account
                                        $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);
                                        break;

                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_following", $username);

                                                break;

                                        }

                                        break;

                                }

                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            //Activity
                            if(!empty($users)){

                                try {

                                    $index      = array_rand($users);

                                    $user       = $users[$index];

                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                    if(!empty($feeds)&&is_array($feeds)){

                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                    }

                                    if(!empty($feeds) && is_array($feeds)){
                                        $index  = array_rand($feeds);
                                        $feed   = $feeds[$index];
                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        if(empty($history)){

                                            $like = json_decode($i->media->like($feed->pk));
                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                            if($like->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'likers':
                            try {
                                switch ((int)$targets['likers']) {
                                    case 1:
                                        //Usernames Post
                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);
                                        break;
                                    case 2:
                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;
                                    case 3:
                                        //All
                                        switch (rand(1, 2)) {
                                            case 1:
                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);
                                                break;
                                            case 2:
                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);
                                                break;
                                        }
                                        break;
                                }

                                if(!empty($user_feeds)){
                                    $index       = array_rand($user_feeds);
                                    $user_feed   = $user_feeds[$index];
                                    $likers = json_decode($i->media->getLikers($user_feed->pk));
                                    $users  = $likers->users;
                                    $users  = removePrivateUser($users);
                                }

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){

                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index];
                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);
                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");
                                    if(!empty($feeds)&&is_array($feeds)){
                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);
                                    }
                                    if(!empty($feeds) && is_array($feeds)){
                                        $index  = array_rand($feeds);
                                        $feed   = $feeds[$index];
                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        if(empty($history)){
                                            $like = json_decode($i->media->like($feed->pk));
                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";
                                            if($like->status == "ok"){
                                                $response = array(
                                                    "st"      => "success",
                                                    "data"    => json_encode($feed),
                                                    "code"    => $feed->code,
                                                    "txt"     => l('Successfully')
                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }
                            break;
                        case 'commenters':

                            try {

                                switch ((int)$targets['commenters']) {

                                    case 1:

                                        //Usernames Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                        break;



                                    case 2:

                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 1:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                                break;



                                            case 2:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                                break;

                                        }

                                        break;

                                }



                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $commenters  = json_decode($i->media->getComments($user_feed->pk));

                                    $users       = $commenters->comments;

                                    $users       = removePrivateUserComments($users);

                                }



                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }



                            //Activity

                            if(!empty($users)){

                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index]->user;
                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);
                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");
                                    if(!empty($feeds)&&is_array($feeds)){
                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);
                                    }
                                    if(!empty($feeds) && is_array($feeds)){
                                        $index  = array_rand($feeds);
                                        $feed   = $feeds[$index];
                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        if(empty($history)){
                                            $like = json_decode($i->media->like($feed->pk));
                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                            if($like->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;

                    }



                    return $response;

                    break;



                case 'comment':

                    $targets          = (array)json_decode($data->title);

                    $target       = array_rand((array)json_decode($data->title));



                    $tags             = (array)json_decode($data->description);

                    $tag_index        = array_rand((array)json_decode($data->description));



                    $locations        = (array)json_decode($data->url);

                    $location_index   = array_rand((array)json_decode($data->url));



                    $usernames        = (array)json_decode($data->image);

                    $username_index   = array_rand((array)json_decode($data->image));



                    $comments         = (array)json_decode($data->comment);

                    $comment_index    = array_rand((array)json_decode($data->comment));



                    $tag              = @$spintax->process($tags[$tag_index]);

                    $location         = @$spintax->process($locations[$location_index]);

                    $username         = @$spintax->process($usernames[$username_index]);

                    $comment          = @$spintax->process($comments[$comment_index]);

                    switch ($target) {

                        case 'location':

                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $location);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);
                                }
                                if(!empty($feeds) && is_array($feeds)){

                                    $index  = array_rand($feeds);

                                    $feed   = $feeds[$index];

                                    //$feed   = Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);

                                    $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                    if(empty($history)){

                                        $comment = json_decode($i->media->comment($feed->pk, $comment));

                                        //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                        if($comment->status == "ok"){

                                            $response = array(

                                                "st"      => "success",

                                                "data"    => json_encode($feed),

                                                "code"    => $feed->code,

                                                "txt"     => l('Successfully')

                                            );

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;

                        case 'username':

                            try {

                                $feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }
                                if(!empty($feeds) && is_array($feeds)){
                                    $index  = array_rand($feeds);
                                    $feed   = $feeds[$index];
                                    //$feed   = Instagram_Filter_Item($user, $data->filter, 'user', $i);

                                    $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                    if(empty($history)){

                                        $comment = json_decode($i->media->comment($feed->pk, $comment));

                                        //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                        if($comment->status == "ok"){

                                            $response = array(

                                                "st"      => "success",

                                                "data"    => json_encode($feed),

                                                "code"    => $feed->code,

                                                "txt"     => l('Successfully')

                                            );

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;

                        case 'tag':

                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $tag);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }
                                if(!empty($feeds) && is_array($feeds)){
                                    $index  = @array_rand($feeds);
                                    $feed   = @$feeds[$index];
                                    //$feed   = Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);
                                    $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                    if(empty($history)){
                                        $comment = json_decode($i->media->comment($feed->pk, $comment));
                                        //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                        if($comment->status == "ok"){

                                            $response = array(

                                                "st"      => "success",

                                                "data"    => json_encode($feed),

                                                "code"    => $feed->code,

                                                "txt"     => l('Successfully')

                                            );

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'followers':

                            try {

                                switch ((int)$targets['followers']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_followers", $username);

                                        break;
                                    case 2:

                                        //My Account

                                        $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                        break;
                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_followers", $username);

                                                break;

                                        }

                                        break;

                                }



                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){

                                try {

                                    $index      = array_rand($users);

                                    $user       = $users[$index];

                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");
                                    if(!empty($feeds)&&is_array($feeds)){

                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                    }

                                    if(!empty($feeds) && is_array($feeds)){

                                        $index  = array_rand($feeds);

                                        $feed   = $feeds[$index];

                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        if(empty($history)){

                                            $comment = json_decode($i->media->comment($feed->pk, $comment));

                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                            if($comment->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'followings':

                            try {

                                switch ((int)$targets['followings']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_following", $username);

                                        break;



                                    case 2:

                                        //My Account

                                        $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_following", $username);

                                                break;

                                        }

                                        break;

                                }

                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }



                            //Activity

                            if(!empty($users)){

                                try {

                                    $index      = array_rand($users);

                                    $user       = $users[$index];

                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                    if(!empty($feeds)&&is_array($feeds)){

                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                    }

                                    if(!empty($feeds) && is_array($feeds)){





                                        $index  = array_rand($feeds);

                                        $feed   = $feeds[$index];

                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        if(empty($history)){

                                            $comment = $i->media->comment($feed->pk, $comment);

                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                            if($comment->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'likers':
                            try {
                                switch ((int)$targets['likers']) {
                                    case 1:
                                        //Usernames Post
                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);
                                        break;

                                    case 2:
                                        //My Post
                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);
                                        break;
                                    case 3:
                                        //All
                                        switch (rand(1, 2)) {
                                            case 1:
                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);
                                                break;
                                            case 2:
                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);
                                                break;

                                        }

                                        break;

                                }



                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $likers = json_decode($i->media->getLikers($user_feed->pk));

                                    $users  = $likers->users;

                                    $users  = removePrivateUser($users);

                                }

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){
                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index];
                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);
                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");
                                    if(!empty($feeds)&&is_array($feeds)){
                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);
                                    }
                                    if(!empty($feeds) && is_array($feeds)){
                                        $index  = array_rand($feeds);
                                        $feed   = $feeds[$index];
                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        if(empty($history)){
                                            $comment = $i->media->comment($feed->pk, $comment);
                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";
                                            if($comment->status == "ok"){
                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }





                            break;
                        case 'commenters':

                            try {

                                switch ((int)$targets['commenters']) {

                                    case 1:

                                        //Usernames Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                        break;



                                    case 2:

                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 1:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                                break;



                                            case 2:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                                break;

                                        }

                                        break;

                                }



                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $commenters  = json_decode($i->media->getComments($user_feed->pk));

                                    $users       = $commenters->comments;

                                    $users       = removePrivateUserComments($users);

                                }

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){

                                try {

                                    $index      = array_rand($users);

                                    $user       = $users[$index]->user;

                                    $feeds      = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                    $feeds      = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                    if(!empty($feeds)&&is_array($feeds)){

                                        $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);
                                    }
                                    if(!empty($feeds) && is_array($feeds)){
                                        $index  = array_rand($feeds);
                                        $feed   = $feeds[$index];
                                        $history = $CI->db->select("*")->where("pk", $feed->code)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        if(empty($history)){

                                            $comment = json_decode($i->media->comment($feed->pk, $comment));

                                            //echo "<a href='https://instagram.com/p/".$feed->code."' target='_blank'>".$feed->code."</a>";

                                            if($comment->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($feed),

                                                    "code"    => $feed->code,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;

                    }

                    return $response;

                    break;

                case 'follow':

                    $targets          = (array)json_decode($data->title);

                    $target           = array_rand((array)json_decode($data->title));

                    $tags             = (array)json_decode($data->description);

                    $tag_index        = array_rand((array)json_decode($data->description));
                    $locations        = (array)json_decode($data->url);
                    $location_index   = array_rand((array)json_decode($data->url));
                    $usernames        = (array)json_decode($data->image);
                    $username_index   = array_rand((array)json_decode($data->image));

                    $tag              = @$spintax->process($tags[$tag_index]);

                    $location         = @$spintax->process($locations[$location_index]);

                    $username         = @$spintax->process($usernames[$username_index]);



                    switch ($target) {
                        case 'location':

                            try {
                                $feeds  = Instagram_Get_Feed($i, $target, $location);
                                if(!empty($feeds)&&is_array($feeds)){
                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }

                                if(!empty($feeds) && is_array($feeds)){
                                    $index  = array_rand($feeds);
                                    $feed   = $feeds[$index];
                                    $user   = Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        if($user->friendship_status->following == "" && $user->friendship_status->outgoing_request == ""){
                                            $follow = json_decode($i->people->follow($user->pk));
                                            //echo "<a href='https://instagram.com/".$feed->user->username."' target='_blank'>".$feed->user->username."</a>";
                                            if($follow->status == "ok"){
                                                $response = array(
                                                    "st"      => "success",
                                                    "data"    => json_encode($user),
                                                    "code"    => $user->username,
                                                    "txt"     => l('Successfully')
                                                );

                                            }

                                        }
                                    }
                                }

                            } catch (Exception $e){
                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'username':
                            try {

                                $follow_types  = array("user_following","user_followers");

                                $follow_index  = array_rand($follow_types);

                                $follow_type   = $follow_types[$follow_index];

                                $users  = Instagram_Get_Feed($i, $follow_type, $username);

                                $users = removeUserBlackLists($users,$blacklist_usernames);

                                if(!empty($users)){

                                    $index  = array_rand($users);

                                    $user   = $users[$index];
                                    
                                    $user   = Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);

                                    if(!empty($user)){

                                        $info   = json_decode($i->userFriendship($user->pk));

                                        if($info->status == "ok"){

                                            if($info->following == "" && $info->outgoing_request == ""){

                                                $follow = $i->people->follow($user->pk);

                                                //echo "<a href='https://instagram.com/".$user->user->username."' target='_blank'>".$user->user->username."</a>";

                                                if($follow->status == "ok"){

                                                    $response = array(

                                                        "st"      => "success",

                                                        "data"    => json_encode($user),

                                                        "code"    => $user->username,

                                                        "txt"     => l('Successfully')

                                                    );

                                                }

                                            }

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'tag':
                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $tag);

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }

                                if(!empty($feeds) && is_array($feeds)){


                                    $index  = @array_rand($feeds);

                                    $feed   = @$feeds[$index];

                                    $user   = @Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);

                                    if(!empty($user)){

                                        if($user->friendship_status->following == "" && $user->friendship_status->outgoing_request == ""){

                                            $follow = json_decode($i->people->follow($user->pk));

                                            //echo "<a href='https://instagram.com/".$feed->user->username."' target='_blank'>".$feed->user->username."</a>";

                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'followers':

                            try {

                                switch ((int)$targets['followers']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_followers", $username);

                                        break;

                                    case 2:

                                        //My Account

                                        $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                        break;

                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_followers", $username);

                                                break;

                                        }

                                        break;

                                }



                                $users = removePrivateUser($users);



                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }



                            //Activity

                            if(!empty($users)){

                                $users = removeUserBlackLists($users,$blacklist_usernames);

                            }

                            if(!empty($users)){

                                try {

                                    $index      = array_rand($users);

                                    $user       = $users[$index];

                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);

                                    if(!empty($user)){

                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));

                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){

                                            $follow = $i->people->follow($user->pk);

                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";

                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'followings':

                            try {

                                switch ((int)$targets['followings']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_following", $username);

                                        break;



                                    case 2:

                                        //My Account

                                        $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_following", $username);

                                                break;

                                        }

                                        break;

                                }



                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }



                            //Activity

                            if(!empty($users)){

                                $users = removeUserBlackLists($users,$blacklist_usernames);                                

                            }

                            if(!empty($users)){

                                try {

                                    $index      = array_rand($users);

                                    $user       = $users[$index];

                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);

                                    if(!empty($user)){

                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));

                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){

                                            $follow = json_decode($i->people->follow($user->pk));

                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";

                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'likers':

                            try {

                                switch ((int)$targets['likers']) {

                                    case 1:

                                        //Usernames Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                        break;



                                    case 2:

                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 1:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                                break;



                                            case 2:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                                break;

                                        }

                                        break;

                                }



                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $likers = json_decode($i->media->getLikers($user_feed->pk));

                                    $users  = $likers->users;

                                    $users  = removePrivateUser($users);

                                }



                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            //Activity

                            if(!empty($users)){

                                $users = removeUserBlackLists($users,$blacklist_usernames);

                            }

                            if(!empty($users)){

                                try {


                                    $index      = array_rand($users);

                                    $user       = $users[$index];

                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);
                                    
                                    if(!empty($user)){

                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));

                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){

                                            $follow = json_decode($i->people->follow($user->pk));

                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";

                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;
                        case 'commenters':

                            try {

                                switch ((int)$targets['commenters']) {

                                    case 1:

                                        //Usernames Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                        break;



                                    case 2:

                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 1:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                                break;



                                            case 2:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                                break;

                                        }

                                        break;

                                }
                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $commenters  = json_decode($i->media->getComments($user_feed->pk));

                                    $users       = $commenters->comments;

                                    $users       = removePrivateUserComments($users);

                                }
                            } catch (Exception $e) {
                                $response = array(
                                    "st"      => "error",
                                    "txt"     => $e->getMessage()
                                );
                            }
                            //Activity
                            if(!empty($users)){
                                $users = removeUserCommentBlacklists($users,$blacklist_usernames);
                            }
                            if(!empty($users)){
                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index]->user;
                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));
                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){
                                            $follow = json_decode($i->people->follow($user->pk));
                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";
                                            if($follow->status == "ok"){
                                                $response = array(
                                                    "st"      => "success",
                                                    "data"    => json_encode($user),
                                                    "code"    => $user->username,
                                                    "txt"     => l('Successfully')
                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;

                    }
                    return $response;
                    break;
                case 'like_follow':

                    $targets          = (array)json_decode($data->title);

                    $target           = array_rand((array)json_decode($data->title));

                    $tags             = (array)json_decode($data->description);

                    $tag_index        = array_rand((array)json_decode($data->description));

                    $locations        = (array)json_decode($data->url);

                    $location_index   = array_rand((array)json_decode($data->url));

                    $usernames        = (array)json_decode($data->image);

                    $username_index   = array_rand((array)json_decode($data->image));

                    $tag              = @$spintax->process($tags[$tag_index]);

                    $location         = @$spintax->process($locations[$location_index]);

                    $username         = @$spintax->process($usernames[$username_index]);

                    switch ($target) {

                        case 'location':

                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $location);

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }                                    

                                if(!empty($feeds) && is_array($feeds)){



                                    $index  = array_rand($feeds);

                                    $feed   = $feeds[$index];

                                    $user   = Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);

                                    if(!empty($user)){

                                        if($user->friendship_status->following == "" && $user->friendship_status->outgoing_request == ""){

                                            $feed_like  = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                            $max_like = rand(3, 5);

                                            $count = 0;

                                            foreach($feed_like as $k => $fl){

                                              if($count < $max_like){

                                                $i->media->like($fl->pk);

                                              }else{

                                                break;

                                              }

                                              $count++;

                                            }



                                            $follow = json_decode($i->people->follow($user->pk));

                                            //echo "<a href='https://instagram.com/".$feed->user->username."' target='_blank'>".$feed->user->username."</a>";

                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;



                        case 'username':

                            try {
                                $follow_types  = array("user_following","user_followers");
                                $follow_index  = array_rand($follow_types);
                                $follow_type   = $follow_types[$follow_index];
                                $users  = Instagram_Get_Feed($i, $follow_type, $username);
                                if(!empty($users)){
                                    $index  = array_rand($users);
                                    $user   = $users[$index];
                                    $user   = Instagram_Filter_Item($user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        $info   = json_decode($i->people->getFriendship($user->pk));
                                        if($info->status == "ok"){
                                            if($info->following == "" && $info->outgoing_request == ""){
                                                $follow = json_decode($i->people->follow($user->pk));
                                                //echo "<a href='https://instagram.com/".$user->user->username."' target='_blank'>".$user->user->username."</a>";
                                                if($follow->status == "ok"){
                                                    $response = array(
                                                        "st"      => "success",
                                                        "data"    => json_encode($user),
                                                        "code"    => $user->username,
                                                        "txt"     => l('Successfully')
                                                    );

                                                }

                                            }

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;



                        case 'tag':

                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $tag);

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                } 

                                if(!empty($feeds) && is_array($feeds)){
                                    $index  = @array_rand($feeds);
                                    $feed   = @$feeds[$index];
                                    $user   = @Instagram_Filter_Item($feed->user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        if($user->friendship_status->following == "" && $user->friendship_status->outgoing_request == ""){
                                            $feed_like  = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);
                                            $max_like = rand(3, 5);
                                            $count = 0;
                                            foreach($feed_like as $k => $fl){

                                              if($count < 3){

                                                $i->media->like($fl->pk);
                                              }else{
                                                break;
                                              }
                                              $count++;
                                            }
                                            $follow = json_decode($i->people->follow($user->pk));
                                            //echo "<a href='https://instagram.com/".$feed->user->username."' target='_blank'>".$feed->user->username."</a>";
                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;



                        case 'followers':

                            try {

                                switch ((int)$targets['followers']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_followers", $username);

                                        break;



                                    case 2:

                                        //My Account

                                        $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_followers", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_followers", $username);

                                                break;

                                        }

                                        break;

                                }



                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }



                            //Activity

                            if(!empty($users)){

                                $users = removeUserBlackLists($users,$blacklist_usernames);

                            }

                            if(!empty($users)){

                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index];
                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));
                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){
                                            $feed_like  = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);
                                            $max_like = rand(3, 5);
                                            $count = 0;
                                            foreach($feed_like as $k => $fl){
                                              if($count < 3){
                                                $i->media->like($fl->pk);
                                              }else{
                                                break;
                                              }
                                              $count++;
                                            }
                                            $follow = json_decode($i->people->follow($user->pk));

                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";

                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;



                        case 'followings':

                            try {

                                switch ((int)$targets['followings']) {

                                    case 1:

                                        //Usernames

                                        $users  = Instagram_Get_Feed($i, "user_following", $username);

                                        break;



                                    case 2:

                                        //My Account

                                        $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 2:

                                                $users  = Instagram_Get_Feed($i, "user_following", $data->fid."|".$data->username);

                                                break;



                                            case 1:

                                                $users  = Instagram_Get_Feed($i, "user_following", $username);

                                                break;

                                        }

                                        break;

                                }



                                $users = removePrivateUser($users);

                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){
                                $users = removeUserBlackLists($users,$blacklist_usernames);
                            }
                            if(!empty($users)){
                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index];
                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));
                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){
                                            $feed_like  = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);
                                            $max_like = rand(3, 5);
                                            $count = 0;
                                            foreach($feed_like as $k => $fl){

                                              if($count < 3){

                                                $i->media->like($fl->pk);

                                              }else{

                                                break;

                                              }

                                              $count++;

                                            }
                                            $follow = json_decode($i->people->follow($user->pk));
                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";
                                            if($follow->status == "ok"){
                                                $response = array(
                                                    "st"      => "success",
                                                    "data"    => json_encode($user),
                                                    "code"    => $user->username,
                                                    "txt"     => l('Successfully')
                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;



                        case 'likers':

                            try {

                                switch ((int)$targets['likers']) {

                                    case 1:

                                        //Usernames Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                        break;



                                    case 2:

                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 1:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                                break;



                                            case 2:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);
                                                break;
                                        }
                                        break;
                                }
                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $likers = json_decode($i->media->getLikers($user_feed->pk));

                                    $users  = $likers->users;

                                    $users  = removePrivateUser($users);

                                }
                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }



                            //Activity

                            if(!empty($users)){

                                $users = removeUserBlackLists($users,$blacklist_usernames);

                            }

                            if(!empty($users)){

                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index];
                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);
                                    if(!empty($user)){
                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));

                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){

                                            $feed_like  = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                            $max_like = rand(3, 5);

                                            $count = 0;

                                            foreach($feed_like as $k => $fl){

                                              if($count < 3){

                                                $i->media->like($fl->pk);

                                              }else{

                                                break;

                                              }

                                              $count++;

                                            }
                                            $follow = json_decode($i->people->follow($user->pk));
                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";
                                            if($follow->status == "ok"){

                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')

                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;



                        case 'commenters':

                            try {

                                switch ((int)$targets['commenters']) {

                                    case 1:

                                        //Usernames Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                        break;



                                    case 2:

                                        //My Post

                                        $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                        break;



                                    case 3:

                                        //All

                                        switch (rand(1, 2)) {

                                            case 1:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $username);

                                                break;



                                            case 2:

                                                $user_feeds  = Instagram_Get_Feed($i, "user_feed", $data->fid."|".$data->username);

                                                break;

                                        }

                                        break;

                                }



                                if(!empty($user_feeds)){

                                    $index       = array_rand($user_feeds);

                                    $user_feed   = $user_feeds[$index];

                                    $commenters  = json_decode($i->media->getComments($user_feed->pk));

                                    $users       = $commenters->comments;

                                    $users       = removePrivateUserComments($users);

                                }



                            } catch (Exception $e) {

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }
                            //Activity
                            if(!empty($users)){

                                $users = removeUserCommentBlacklists($users,$blacklist_usernames);

                            }
                            if(!empty($users)){

                                try {
                                    $index      = array_rand($users);
                                    $user       = $users[$index]->user;
                                    $user       = Instagram_Filter_Item($user, $data->filter, 'user', $i);

                                    if(!empty($user)){

                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                                        $userFriendship = json_decode($i->people->getFriendship($user->pk));

                                        if(empty($history) && $userFriendship->following == "" && $userFriendship->outgoing_request == ""){

                                            $feed_like  = Instagram_Get_Feed($i, "user_feed", $user->pk."|".$user->username);

                                            $max_like = rand(3, 5);

                                            $count = 0;
                                            foreach($feed_like as $k => $fl){

                                              if($count < 3){

                                                $i->media->like($fl->pk);

                                              }else{

                                                break;

                                              }

                                              $count++;

                                            }
                                            $follow = json_decode($i->people->follow($user->pk));
                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";
                                            if($follow->status == "ok"){
                                                $response = array(
                                                    "st"      => "success",
                                                    "data"    => json_encode($user),
                                                    "code"    => $user->username,
                                                    "txt"     => l('Successfully')
                                                );

                                            }

                                        }

                                    }

                                } catch (Exception $e){

                                    $response = array(

                                        "st"      => "error",

                                        "txt"     => $e->getMessage()

                                    );

                                }

                            }

                            break;



                    }

                    return $response;

                    break;
                case 'followback':
                    try {
                        $users  = Instagram_Get_Feed($i, "following_recent_activity");
                        if(!empty($users)&&is_array($users)){
                            $users = removeUserFollowBackBlacklist($users,$blacklist_usernames);
                        }
                        if(!empty($users)){
                            foreach ($users as $user) {
                                if(!empty($user)){
                                    $info   = json_decode($i->people->getFriendship($user->id));
                                    if($info->status == "ok"){
                                        $history = $CI->db->select("*")->where("pk", $user->username)->where("type", $data->category)->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();
                                        if(empty($history) && $info->following == "" && $info->outgoing_request == "" && $info->followed_by == 1){
                                            $follow = json_decode($i->people->follow($user->id));
                                            //echo "<a href='https://instagram.com/".$user->username."' target='_blank'>".$user->username."</a>";
                                            if($follow->status == "ok"){
                                                $messages         = (array)json_decode($data->message);
                                                $message_index    = array_rand((array)json_decode($data->message));
                                                if(!empty($messages)){
                                                    $message          = $spintax->process($messages[$message_index]);
                                                    if($message != ""){
                                                        $mess = $i->direct->sendText(['users' => [$user->id]], $message);
                                                    }
                                                }
                                                $user->pk = $user->id;
                                                $response = array(

                                                    "st"      => "success",

                                                    "data"    => json_encode($user),

                                                    "code"    => $user->username,

                                                    "txt"     => l('Successfully')
                                                );
                                            }
                                            break;
                                        }
                                    }
                                }

                            }

                        }

                    } catch (Exception $e){

                        $response = array(

                            "st"      => "error",

                            "txt"     => $e->getMessage()

                        );

                    }

                    return $response;

                    break;

                case 'unfollow':

                    $unfollow = $data->description;

                    $unfollow = json_decode($unfollow);

                    $unfollow_source = $unfollow->unfollow_source;

                    $unfollow_followers = !empty($unfollow->unfollow_followers)?$unfollow->unfollow_followers:0;

                    $unfollow_follow_age = !empty($unfollow->unfollow_follow_age)?$unfollow->unfollow_follow_age:0;

                    if($unfollow_followers==1||$unfollow_source==2){

                        $account_id = $data->account_id;

                        $users      =   $CI->db

                                        ->select('followed_username')

                                        ->from(INSTAGRAM_ACCOUNTS)

                                        ->where("id",$account_id)

                                        ->get()->row()->followed_username;

                        if(!empty($users)){

                            $users_tmp = (array)json_decode($users);

                        }

                        if($unfollow_follow_age!=0){

                            if(!empty($users_tmp)){

                                $time_limit = strtotime(NOW) - $unfollow_follow_age;

                                $users_tmp = array_filter($users_tmp,array(new LowerThanFilter($time_limit),"isLower"));
                            }
                        }

                        if(!empty($users_tmp)){

                            $users_tmp= removeUserUnFollowBackBlacklist($users_tmp,$blacklist_usernames);

                        }
                        try {

                            if(!empty($users_tmp)){

                                $index      = array_rand($users_tmp);

                                $user_data  = explode("@",$index);

                                $user = array(

                                    "pk"                => $user_data[0],

                                    "username"          => $user_data[1],

                                );
                                $unfollow = json_decode($i->people->unfollow($user["pk"]));

                                if($unfollow->status == "ok"){

                                    $users_all = (array)json_decode($users);

                                    unset($users_all[$index]);

                                    $CI->db->update(INSTAGRAM_ACCOUNTS,

                                        array("followed_username"=>json_encode($users_all)),

                                        "`id` = '".$account_id."'"

                                    );

                                    $response = array(

                                        "st"      => "success",

                                        "data"    => json_encode($user),

                                        "code"    => $user["username"],

                                        "txt"     => l('Successfully')

                                    );

                                }

                            }



                        } catch (Exception $e){

                            $response = array(

                                "st"      => "error",

                                "txt"     => $e->getMessage()

                            );

                        }

                    }

                    if($unfollow_source==1&&$unfollow_followers==0){

                        try {

                            $users  = Instagram_Get_Feed($i, 'following');

                            if(!empty($users)&&is_array($users)){

                                $users=removeUserBlackLists($users,$blacklist_usernames);

                            }

                            if(!empty($users)){

                                $index  = array_rand($users);

                                $user   = $users[$index];

                                $unfollow = json_decode($i->people->unfollow($user->pk));

                                if($unfollow->status == "ok"){

                                    $response = array(

                                        "st"      => "success",

                                        "data"    => json_encode($user),

                                        "code"    => $user->username,

                                        "txt"     => l('Successfully')

                                    );

                                }

                            }

                        } catch (Exception $e){

                            $response = array(

                                "st"      => "error",

                                "txt"     => $e->getMessage()

                            );

                        }

                    }

                    return $response;

                    break;

                case 'deletemedia':

                    try {

                        $feeds  = Instagram_Get_Feed($i, "your_feed", "");

                        if(!empty($feeds) && is_array($feeds)){

                            $index  = @array_rand($feeds);

                            $feed   = @$feeds[$index];

                            $delete = json_decode($i->media->delete($feed->id));

                            //echo "<a href='https://instagram.com/".$feed->code."' target='_blank'>".$feed->code."</a>";

                            if($delete->status == "ok"){

                                $response = array(

                                    "st"      => "success",

                                    "data"    => json_encode($feed),

                                    "code"    => $feed->code,

                                    "txt"     => l('Successfully')

                                );

                            }

                        }

                    } catch (Exception $e){

                        $response = array(

                            "st"      => "error",

                            "txt"     => $e->getMessage()

                        );

                    }



                    return $response;

                    break;

                case 'repost':
                    $targets          = (array)json_decode($data->title);
                    $target           = array_rand((array)json_decode($data->title));
                    $tags             = (array)json_decode($data->description);
                    $tag_index        = array_rand((array)json_decode($data->description));
                    $locations        = (array)json_decode($data->url);
                    $location_index   = array_rand((array)json_decode($data->url));
                    $usernames        = (array)json_decode($data->image);
                    $username_index   = array_rand((array)json_decode($data->image));

                    $tag              = @$spintax->process($tags[$tag_index]);
                    $location         = @$spintax->process($locations[$location_index]);
                    $username         = @$spintax->process($usernames[$username_index]);

                    $feed             = array();
                    switch ($target) {
                        case 'location':

                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $location);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                $feeds  = removeFeedVideo($feeds);

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                } 

                                if(!empty($feeds) && is_array($feeds)){

                                    $index  = array_rand($feeds);

                                    $feed   = $feeds[$index];

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'username':
                            try {

                                $feeds  = Instagram_Get_Feed($i, 'user_feed', $username);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                $feeds  = removeFeedVideo($feeds);

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }                                    

                                if(!empty($feeds) && is_array($feeds)){

                                    $index  = array_rand($feeds);

                                    $feed   = $feeds[$index];

                                }

                            } catch (Exception $e){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $e->getMessage()

                                );

                            }

                            break;
                        case 'tag':
                            try {

                                $feeds  = Instagram_Get_Feed($i, $target, $tag);

                                $feeds  = Instagram_Filter($feeds, $data->filter, $data->timezone, "feed");

                                $feeds  = removeFeedVideo($feeds);

                                if(!empty($feeds)&&is_array($feeds)){

                                    $feeds=removeFeedBlackLists($feeds,$blacklist_tags,$blacklist_usernames,$blacklist_keywords);

                                }
                                if(!empty($feeds) && is_array($feeds)){
                                    $index  = array_rand($feeds);

                                    $feed   = $feeds[$index];
                                }

                            } catch (Exception $e){
                                $response = array(
                                    "st"      => "error",
                                    "txt"     => $e->getMessage()
                                );

                            }

                            break;

                    }



                    if(isset($feed) && !empty($feed)){

                        $CI = &get_instance();

                        $history = $CI->db->select("*")->where("pk", $feed->pk)->where("type", "repost")->where("account_id", $data->account_id)->get(INSTAGRAM_HISTORY)->row();

                        if(empty($history)){

                            switch ($feed->media_type) {

                                case 1:


                                    try {

                                        if (!file_exists('app/helpers/up/')) {
                                            mkdir('app/helpers/up/', 0777, true);
                                        }
                                        $url = $feed->image_versions2->candidates[0]->url;
                                        $img_path = 'app/helpers/up/'."image".$data->username.".jpg";
                                        file_put_contents($img_path, file_get_contents($url));
                                        $response =json_decode($i->timeline->uploadPhoto($img_path, array("caption" => $feed->caption->text)));
                                        unlink($img_path);

                                    } catch (Exception $e){

                                        $response = $e->getMessage();

                                    }



                                    break;

                                case 2:

                                    /*try {

                                        $response =$i->uploadTimelineVideo($feed->video_versions[0]->url,  array("caption" => $feed->caption->text));

                                    } catch (Exception $e){

                                        $response = $e->getMessage();

                                    }*/

                                    break;

                            }



                            if(isset($response->status) && $response->status == "ok"){

                                $response = array(

                                    "st"      => "success",

                                    "pk"      => $feed->pk,

                                    "data"    => json_encode($feed),

                                    "code"    => $feed->pk,

                                    "txt"     => l('Successfully')

                                );

                            }



                            if(is_string($response)){

                                $response = array(

                                    "st"      => "error",

                                    "txt"     => $response

                                );

                            }

                        }



                    }



                    return $response;

                    break;

                case 'message':
                    try {
                        $message = $i->direct->sendText(['users' => [$data->group_id]], $spintax->process($data->message));
                        $message = json_decode($message);
                        if($message->status == "ok"){

                            $response = array(

                                "st"      => "success",

                                "code"    => $data->name,

                                "txt"     => l('Successfully')

                            );

                        }



                    } catch (Exception $e){

                        $response = array(

                            "st"      => "error",

                            "txt"     => $e->getMessage()

                        );

                    }



                    return $response;

                    break;

            }

        }else{

            $response["message"] = "Upload failed, Please try again";

            $response = array(

                "st"  => "error",

                "message" => $response["message"]

            );

        }

    }

    function removeElementWithValue($array, $key, $value){

        $array = (array)$array;

         foreach($array as $subKey => $subArray){

            $subArray = (array)$subArray;

              if($subArray[$key] != $value){

                   unset($array[$subKey]);

              }

         }

         return $array;

    }

}

?>