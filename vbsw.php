<?php
require_once('./simple_html_dom.php');
$GLOBALS['URL'] = 'http://localhost';

/**
 * Created by PhpStorm.
 * User: oslinux
 * Date: 02/11/13
 * Time: 01:16
 */
$user = htmlspecialchars($_GET["user"]);
$pass = htmlspecialchars($_GET["pass"]);
$query = htmlspecialchars($_GET["query"]);
if(isset($_GET["debug"]))
    debug($user, $pass, $query);
else
    search($user, $pass, $query);

/**
 * Executes search in debug mode
 * @param $user String Username
 * @param $pass String Password
 * @param $query String Search Query
 */
function debug ($user, $pass, $query){
    echo "Username = ".$user."<br/>";
    echo "Password = ".$pass."<br/>";
    echo "Query = ".$query."<br/>";
    $cookie = getcookie();
    if($cookie != false) {
        echo "Initial Session ID: " . $cookie['PHPSESSID']."<br/>";
        $logged_cookie = postLogin($user, $pass, $cookie);
        if($logged_cookie != false) {
            echo "Logged Session ID: " . $logged_cookie['PHPSESSID']."<br/>";
            $array = querySearch($query, $logged_cookie);
            if($array != false) {
                echo "JSON Results: " . json_encode($array);
            }
        } else {
            echo "<p style=\"color:red\">Authentication failed. </p><br/>";
        }
    }
}

/**
 * Executes search in json mode
 * @param $user String Username
 * @param $pass String Password
 * @param $query String Search Query
 */
function search ($user, $pass, $query){
    $cookie = getcookie();
    if($cookie != false) {
        $logged_cookie = postLogin($user, $pass, $cookie);
        if($logged_cookie != false) {
            $array = querySearch($query, $logged_cookie);
            if($array != false) {
                echo json_encode($array);
            }
        }
    }
}

/**
 * @return bool|Array Initial session cookie or false
 */
function getcookie (){
    $r = new HttpRequest($GLOBALS['URL'].'/', HttpRequest::METH_GET);
    try {
        $r->send();
        if ($r->getResponseCode() == 200) {
            return $r->getResponseCookies()[0]->cookies;
        }
    } catch (HttpException $ex) {
        echo $ex;
    }
    return false;
}

/**
 * Executes login and returns login session cookie
 * @param $user String username
 * @param $pass String password
 * @param $cookie Array cookie
 * @return bool|Array Cookie or false
 */
function postLogin ($user, $pass, $cookie) {
    $r = new HttpRequest($GLOBALS['URL'].'/login_check', HttpRequest::METH_POST);
    $r->setOptions(array('cookies' => $cookie));
    $r->setOptions(array('redirect' => 0));
    $r->addPostFields(array('_username' => $user, '_password' => $pass));
    try {
        $r->send();
        if ($r->getResponseCode() == 302) {
            if(count($r->getResponseCookies()) > 0)
                return $r->getResponseCookies()[0]->cookies;
        }
    } catch (HttpException $ex) {
        echo $ex;
    }
    return false;
}

/**
 * Performs a search in remote frontend
 * @param $query String search query
 * @param $cookie Array logged session cookie
 * @return Array array of query results.
 */
function querySearch ($query, $cookie) {
    $r = new HttpRequest($GLOBALS['URL'].'/books/search', HttpRequest::METH_GET);
    $r->addQueryData(array('q' => $query));
    $r->setOptions(array('cookies' => $cookie));
    try {
        $r->send();
        if ($r->getResponseCode() == 200) {
            $html = str_get_html($r->getResponseBody());
            $books_array = array();
            $books = $html->find('tr[itemscope]');
            $i = 0;
            foreach($books as $book) {
                $book_contents = array();
                $j = 0;
                foreach($book->find('td') as $td) {
                    switch($j++) {
                        case 0: /* Image and URL */
                            $book_contents['url'] = $GLOBALS['URL'] . $td->find('a', 0)->href;
                            $book_contents['img'] = $td->find('img', 0)->src;
                            break;
                        case 1:
                            $book_contents['name'] = $td->find('a', 0)->plaintext;
                            break;
                        case 2:
                            $book_contents['description'] = $td->plaintext;
                            break;
                    }
                }
                $books_array[$i++] = $book_contents;
            }
            return $books_array;
        }
    } catch (HttpException $ex) {
        echo $ex;
    }
    return false;
}