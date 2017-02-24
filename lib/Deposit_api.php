<?php  

require_once('DepositClientConfig.php');
require_once('DepositParams.php');

/**
 * Interface to the DepositPhotos RPC API client.
 * 
 * @copyright   Copyright (c) 2010 DepositPhotos Inc.
 */
class Deposit_api
{
    const VERSION = '0.1';
    
    /**
     * DepositPhotos RPC uri.
     *
     * @var     string
     */
    protected $apiUrl;

    /**
     * DepositPhotos API key.
     *
     * @var     string
     */
    protected $apiKey;

    /**
     * Deposit API session id.
     *
     * @var     string
     */
    protected $sessionId;

    /**
     * Deposit API session name.
     *
     * @var     string
     */
    protected $sessionName;


    /**
     * Constructor
     *
     * @param   string $apiUrl  DepositPhotos RPC uri
     * @param   string $apiKey  DepositPhotos API key
     */
    public $fullurl;


    public function  __construct($params=array('sessionid' => null))
    {
        $this->apiUrl = DEPOSIT_API_URL;
        $this->apiKey = DEPOSIT_API_RESELLER_KEY;
        $this->sessionId = $params['sessionid'];
    }

    public function getApiKey()
    {
        echo "Api Key:".$this->apiKey."<br/>";
        echo "Api Url:".$this->apiUrl;
    }
    /**
     * This method makes possible to search media in the DepositPhotos image bank.
     * 
     * The $criteria array must conform to the following format:
     * <code>
     * array(
     *  // all params are optional
     *  DepositParams::SEARCH_QUERY  => 'query string',
     *  DepositParams::SEARCH_SORT   => 'sort result by one of 1-6 sort types',
     *                              // WEIGHT       = 1;
     *                              // DOWNLOADS    = 2;
     *                              // POPULARITY   = 3;
     *                              // BEST_SALES   = 4;
     *                              // TIME         = 5;
     *                              // TIME_DESC    = 6;
     *  DepositParams::SEARCH_LIMIT  => 'limit of results to return',
     *  DepositParams::SEARCH_OFFSET => 'results offset', // if used without DepositParams::SEARCH_LIMIT, then ignored
     *  DepositParams::SEARCH_CATEGORIES => 'list of categories id's separated by whitspace',
     *  DepositParams::SEARCH_COLOR  => 'search by one of 1-17 colors',
     *  DepositParams::SEARCH_NUDITY => 'if true, exclude nudity photos',
     *  DepositParams::SEARCH_EXTENDED   => 'if true, include only with extended license',
     *  DepositParams::SEARCH_EXCLUSIVE  => 'if true, include only with exclusive',
     *  DepositParams::SEARCH_USER   => 'media author id',
     *  DepositParams::SEARCH_DATE1  => 'include results from date',
     *  DepositParams::SEARCH_DATE2  => 'include results to date',
     *  DepositParams::SEARCH_ORIENTATION=> 'image orientation, can be one of DepositParams::ORIENT_* constants',
     *  DepositParams::SEARCH_IMAGESIZE  => 'image size, can be one of DepositParams::SIZE_* constants',
     *  DepositParams::SEARCH_VECTOR => 'if true, include only vector media',
     *  DepositParams::SEARCH_PHOTO  => 'if true, include only photo media' // if both, vector and photo media is true, returns all media
     * )
     * </code>
     *
     * Response format:
     * <code>
     * array(
     *  0 => stdClass(
     *      id -> 'media id'
     *      url -> 'small tumbnail url'
     *      title -> 'media title'
     *      description -> 'media description'
     *      userid -> 'author id'
     *  )
     *  [, 1 -> ...]
     * )
     * </code>
     *
     * @param   array $criteria
     * @return  stdClass 
     */
    //public function search($criteria = array())
    public function search($query, $limit, $offset)
    {
        $postParams = array(
            DepositParams::APIKEY   => $this->apiKey,
            DepositParams::COMMAND  => DepositParams::SEARCH_CMD,
            DepositParams::SEARCH_QUERY  => $query,
            DepositParams::SEARCH_ORIENTATION => '',
            DepositParams::SEARCH_LIMIT  => $limit,
            DepositParams::SEARCH_OFFSET => $offset,
            DepositParams::SEARCH_COLOR => ''//urlencode($data['color_sel'])
            );

        //$postParams = array_merge($postParams, $criteria);
        return $this->apiUrl . "?". http_build_query($postParams);

        //return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * This method returns list of categories used on the DepositPhotos website.
     *
     * Response format:
     * <code>
     * stdClass(
     *  category_id -> 'category title'
     *  [, category_id -> ...]
     * )
     * </code>
     *
     * @return  stdClass
     */
    public function getCategoriesList()
    {
        $postParams = array(
            DepositParams::APIKEY   => $this->apiKey,
            DepositParams::COMMAND  => DepositParams::GET_CATEGORIES_CMD);

        return $this->checkResponse($this->post($this->apiUrl, $postParams))->result;
    }

    /**
     * This method return all information about a media.
     * 
     * Response format:
     * <code>
     * stdClass(
     *  id -> 'media id'
     *  userid -> 'author id'
     *  username -> 'author name'
     *  title -> 'media title'
     *  description -> 'media description'
     *  published -> 'publish date'
     *  isextended -> 'is extended license'
     *  isexclusive -> 'is exclusive'
     *  width -> 'media width'
     *  height -> 'media height'
     *  mp -> 'media mega pixels'
     *  views -> 'count of media views'
     *  downloads -> 'count of media downloads'
     *  tags -> array( // tags associated with media
     *      0 => 'tag name'
     *      [, 1 => ...]
     *  )
     *  categories -> stdClass( // categories associated with media
     *      category_id -> 'category title'
     *      [, category_id -> ...]
     *  )
     *  url -> 'large tumbnail url'
     * )
     * </code>
     *
     * @param   integer $mediaId
     * @return  stdClass
     */
    public function getMediaData($mediaId)
    {
        $postParams = array(
            DepositParams::APIKEY   => $this->apiKey,
            DepositParams::COMMAND  => DepositParams::GET_MEDIA_DATA_CMD,
            DepositParams::MEDIA_ID => $mediaId
            );

        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * This method returns most searched tag and most used tag on the DepositPhotos website.
     *
     * This method may help you to create a tags cloud.
     * 
     * Response format:
     * <code>
     * array(
     *  0 => stdClass(
     *      rate -> 'rate of tag'
     *      tag -> 'tag name'
     *  )
     *  [, 1 => ...]
     * )
     * </code>
     *
     * @return  array
     */
    public function getTagCloud()
    {
        $postParams = array(
            DepositParams::APIKEY   => $this->apiKey,
            DepositParams::COMMAND  => DepositParams::GET_TAG_CLOUD_CMD);

        return $this->checkResponse($this->post($this->apiUrl, $postParams))->result;
    }

    /**
     * This method authenticates the API client and gives the session ID.
     *
     * NOTE: Some methods require authentication before calling.
     *
     * @param   string $user
     * @param   string $password
     * @return  array   of session id and session name
     */
    public function login($user, $password)
    {
        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::LOGIN_CMD,
            DepositParams::LOGIN_USER   => $user,
            DepositParams::LOGIN_PASSWORD=> $password);

        /* @var $result HttpRpcLogin */
        $result = $this->checkResponse($this->post($this->apiUrl, $postParams));

        //$this->sessionId    = $result->sessionid;
        //$this->sessionName  = $result->sessionName;
        //return array($this->sessionId, $this->sessionName);
        return $result;
    }

    public function logout($sessionId)
    {
        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::LOGOUT_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId());

        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }    
    /**
     * This method returns download link for specified media.
     *
     * NOTE: This method require authentication before calling.
     *
     * @param   integer $mediaId
     * @return  string
     */
    public function getMedia($mediaId, $license = DepositParams::LICENSE_STANDARD, $size = DepositParams::SIZE_SMALL, $subaccountId = null)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::GET_MEDIA_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::MEDIA_ID     => $mediaId,
            DepositParams::MEDIA_LICENSE=> $license,
            DepositParams::MEDIA_OPTION => $size);

        if (null != $subaccountId) {
            $postParams[DepositParams::SUBACC_ID] = $subaccountId;
        }

        return $this->checkResponse($this->post($this->apiUrl, $postParams))->downloadLink;
    }

    public function getPurchases($limit = DEF_NUMBER_OF_ROWS, $offset = 0, $sortfield = null, $sorttype = null, $dateformat = null)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::GET_PURCHASES_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::PURCHASES_LIMIT => $limit,
            DepositParams::PURCHASES_OFFSET => $offset);

        if (null != $sortfield) {
            $postParams[DepositParams::PURCHASES_SORT_FIELD] = $sortfield;
        }
        if (null != $sorttype) {
            $postParams[DepositParams::PURCHASES_SORT_TYPE] = $sorttype;
        }
        if (null != $dateformat) {
            $postParams[DepositParams::PURCHASES_DATETIME_FORMAT] = $dateformat;
        }

        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * This method creates a subaccount of reseller's account, which made the purchase.
     *
     * NOTE: This method require authentication before calling.
     *
     * @param   string $email
     * @param   string $firstName
     * @param   string $lastName
     * @param   string $company
     * @return  integer
     */
    public function createSubaccount($sessionId, $subUsername, $subPass, $email, $firstName, $lastName, $company = null)
    {
        //$this->sessionId    = $sessionId;

        $this->checkLoggedIn();
        
        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::CREATE_SUBACCOUNT_CMD,
            DepositParams::SESSION_ID   => $sessionId,
            DepositParams::SUBACC_USERNAME => $subUsername,
            DepositParams::SUBACC_PASSWORD => $subPass,
            DepositParams::SUBACC_EMAIL => $email,
            DepositParams::SUBACC_FNAME => $firstName,
            DepositParams::SUBACC_LNAME => $lastName,
            DepositParams::SUBACC_SEND_MAIL => 0);

        if (null != $company) {
            $postParams[DepositParams::SUBACC_COMPANY] = $company;
        }

        return $this->checkResponse($this->post($this->apiUrl, $postParams)); //->subaccountId;
    }

    /**
     * This method deletes subaccount, created by method createSubaccount.
     *
     * NOTE: This method require authentication before calling.
     * 
     * @param   integer $subaccountId 
     */
    public function deleteSubaccount($subaccountId)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::DELETE_SUBACCOUNT_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::SUBACC_ID    => $subaccountId);

        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * This method updates subaccount, created by method createSubaccount.
     *
     * NOTE: This method require authentication before calling.
     * 
     * @param   integer $subaccountId
     * @param   string $email
     * @param   string $firstName
     * @param   string $lastName
     * @param   string $company 
     */
    public function updateSubaccount($subaccountId, $email, $firstName, $lastName, $company = null)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::UPDATE_SUBACCOUNT_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::SUBACC_ID    => $subaccountId,
            DepositParams::SUBACC_EMAIL => $email,
            DepositParams::SUBACC_FNAME => $firstName,
            DepositParams::SUBACC_LNAME => $lastName);

        if (null != $company) {
            $postParams[DepositParams::SUBACC_COMPANY] = $company;
        }
        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * This method returns the subaccounts id's, created by reseller.
     * 
     * NOTE: This method require authentication before calling.
     *
     * @param   integer $limit
     * @param   integer $offset
     * @return  array   of total subaccounts count and requested range of
     *                  subaccounts id's.
     */
    public function getSubaccounts($limit = null, $offset = null)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::GET_SUBACCOUNTS_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::SUBACC_LIMIT => 10,
            DepositParams::SUBACC_OFFSET => 0);
        /*
        if (null !== $limit) {
            $postParams[DepositParams::SUBACC_LIMIT] = $limit;
        }

        if (null !== $offset) {
            $postParams[DepositParams::SUBACC_OFFSET] = $offset;
        }
        */
        /* @var $result HttpRpcSubaccounts */
        $result = $this->checkResponse($this->post($this->apiUrl, $postParams));
        return $result;
        //return array($result->count, $result->subaccounts);
    }

    /**
     * This method returns the specified subaccount data.
     * 
     * NOTE: This method require authentication before calling.
     *
     * @param   integer $subaccountId
     * @return  stdClass
     */
    public function getSubaccountData($subaccountId)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::GET_SUBACCOUNT_DATA_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::SUBACC_ID    => $subaccountId);

        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * This method returns all data about subaccount purchases.
     *
     * NOTE: This method require authentication before calling.
     *
     * @param   integer $subaccountId
     * @return  array   of total purchases count and purchases data
     */
    public function getSubaccountPurchases($subaccountId, $limit = null, $offset = null)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::GET_SUBACCOUNT_PURCHASES_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::SUBACC_ID    => $subaccountId);

        if (null !== $limit) {
            $postParams[DepositParams::SUBACC_LIMIT] = $limit;
        }

        if (null !== $offset) {
            $postParams[DepositParams::SUBACC_OFFSET] = $offset;
        }

        $result = $this->checkResponse($this->post($this->apiUrl, $postParams));
        
        //return array($result->count, $result->purchases);
        return $result;
    }

    /**
     * This method returns text of proof of purchase.
     *
     * NOTE: This method require authentication before calling.
     *
     * @param   integer $subaccountId
     * @param   integer $licenseId
     * @return  string 
     */
    public function getLicense($subaccountId, $licenseId)
    {
        $this->checkLoggedIn();

        $postParams = array(
            DepositParams::APIKEY       => $this->apiKey,
            DepositParams::COMMAND      => DepositParams::GET_LICENSE_CMD,
            DepositParams::SESSION_ID   => $this->getSessionId(),
            DepositParams::SUBACC_ID    => $subaccountId,
            DepositParams::SUBACC_LICENSE_ID => $licenseId);

        return $this->checkResponse($this->post($this->apiUrl, $postParams))->text;
    }
    
    /**
     * Sets the API session id.
     */
    public function setSessionId($id)
    {
        $this->sessionId = $id;
    }

    /**
     * Returns the API session id.
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * This method returns count of stock photos, new photos by week and authors.
     *
     * Response format:
     * <code>
     * stdClass(
     *  totalFiles -> 'count of stock photos',
     *  totalWeekFiles -> 'count of new files by week',
     *  totalAuthors -> 'count of photographers'
     * )
     * </code>
     *
     * @return  stdClass
     */
    public function getInfo()
    {
        $postParams = array(
            DepositParams::APIKEY   => $this->apiKey,
            DepositParams::COMMAND  => DepositParams::GET_INFO_CMD);

        return $this->checkResponse($this->post($this->apiUrl, $postParams));
    }

    /**
     * Check whether the response is success.
     *
     * @param   string $response
     * @return  strClass
     * @throws  EResponseFail
     */
    protected function checkResponse($response)
    {
        try {
            $result = $this->decodeResponse($response);

            if ('failure' == $result->type) {
                //throw new EDepositApiCall($result->error->errormsg);
                show_error($result->error->errormsg);
            }

            if (!is_object($result)) {
                throw new EResponseFail('The rpc response is empty, or have invalid format');
            }
           if (isset($result->result)) {
                if (empty($result->result)){}
                    //show_error('Empty search');
                    //throw new EServiceUnavailable('Empty search');
            }
            
        }
        catch (EResponseFail $e){
            echo $e->errorMessage();
            return false;           
        }
        catch (EServiceUnavailable $e){
            echo $e->errorMessage();
            return false;
        }
        catch (EDepositApiCall $e){
            echo $e->errorMessage();
            return false;
        }      


        return $result;
    }

    /**
     * Decodes response from JSON format.
     *
     * @param   string $response
     * @return  array|stdClass
     */
    protected function decodeResponse($response)
    {
        return json_decode($response);
    }

    /**
     * Sends the POST request to the API service.
     * 
     * This method uses the cURL extension.
     *
     * @param   array $url
     * @param   array $parameters
     * @return  string
     */
    protected function post($url, $parameters)
    {
        $this->fullurl = $url . "?". http_build_query($parameters);
        return $this->createHttpClient()->post($url, $parameters);
    }

    /**
     * For testing purposes.
     *
     * @return CurlHttpClient 
     */
    protected function createHttpClient()
    {
        return new DepCurlHttpClient();
    }

    /**
     * Check whether the session id is not empty.
     * 
     * @throws  EAuthenticationRequired
     */
    protected function checkLoggedIn()
    {
        if (null === $this->getSessionId()) {
            //throw new EAuthenticationRequired('The called method requires authentication');
            show_error('The called method requires authentication');
        }
    }
}


/**
 * Simple HTTP client based on cURL extension.
 *
 * You may use another HTTP client with one requared method {@link post}
 */
class DepCurlHttpClient
{
    /**
     * The cURL resource handle.
     *
     * @var     resource
     */
    protected $ch;

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->ch = curl_init();
    }

    /**
     * Sends the HTTP POST request to specified URL with given parameters.
     *
     * @param   string $url         the URL to request
     * @param   array $parameters   the POST parameters to include to request
     * @return  string              the server response
     */
    public function post($url, $parameters)
    {
        if (false === curl_setopt_array($this->ch, array(
            CURLOPT_POST            => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $url,
            CURLOPT_POSTFIELDS      => $parameters
        ))) {
            //throw new ECurlFail('Error at setting cURL options, reason: '.curl_error($this->ch), curl_errno($this->ch));
            show_error('Error at setting cURL options, reason: '.curl_error($this->ch), curl_errno($this->ch));
        }

        if (false === $result = curl_exec($this->ch)) {
            //throw new ECurlFail('Error at execute cURL request, reason: '.curl_error($this->ch), curl_errno($this->ch));
            show_error('Error at execute cURL request, reason: '.curl_error($this->ch), curl_errno($this->ch));
        }

        elseif (200 != $curlgetinfo = curl_getinfo($this->ch, CURLINFO_HTTP_CODE)) {
            //throw new EServiceUnavailable('The API servise is unavailable');
            show_error('The API servise is unavailable: '.$result);
        }
        //echo $curlgetinfo;
        return $result;
    }

    /**
     * Object destructor.
     */
    public function __destruct()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }
}
/*
class EDepositClient extends Exception {
    public function errorMessage() {
    //error message
    $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
    .': <b>'.$this->getMessage();
    return $errorMsg;
  }

};
class EResponseFail extends EDepositClient {};
class EDepositApiCall extends EDepositClient {};
class ECurlFail extends EDepositClient {};
class EServiceUnavailable extends EDepositClient {};
class EAuthenticationRequired extends EDepositClient {};
*/
?>
