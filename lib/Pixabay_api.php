<?php  

require_once('PixabayClientConfig.php');
require_once('PixabayParams.php');

/**
 * Interface to the Pixabay API client.
 * 
 * 
 */
class Pixabay_Api
{
    const VERSION = '0.1';
    
    /**
     * Pixabay uri.
     *
     * @var     string
     */
    protected $apiUrl;

    /**
     * Pixabay API key.
     *
     * @var     string
     */
    protected $apiKey;


    /**
     * Constructor
     *
     * @param   string $apiUrl  Pixabay RPC uri
     * @param   string $apiKey  Pixabay API key
     */

    public $fullurl;


    public function  __construct()
    {
        $this->apiUrl = PIXABAY_API_URL;
        $this->apiKey = PIXABAY_API_KEY;
        $this->fullurl = '';

    }

    public function getApiKey()
    {
        echo "Api Key:".$this->apiKey."<br/>";
        echo "Api Url:".$this->apiUrl;

    }

   //function search($criteria=array(), $id=null)
    function search($query, $limit, $offset)
   {
      $post_data = array(
        PixabayParams::API_KEY => $this->apiKey,
        PixabayParams::SEARCH_QUERY => $query,
        PixabayParams::SEARCH_LIMIT => $limit, 
        PixabayParams::SEARCH_PAGE => $offset
        );

      //if ($id !== null) $post_data[PixabayParams::MEDIA_ID] = $id;

      //$post_data = array_merge($post_data, $criteria);
                  
      $url = $this->getFullURI(PixabayParams::SEARCH_QUERY, $post_data);

      return $url;
      //return $this->checkResponse($this->post($url, $post_data));
   }

    /**
     * Generate the full URI to use for API calls
     *
     * @param  string $method
     * @param  array  $query
     * @return string
     */
    private function getFullURI($method, array $query = NULL)
    {
        $uri = $this->apiUrl . http_build_query($query);

        return $uri;
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
            if ($response === false)
                show_error('Empty search');
                
            $result = $this->decodeResponse($response); 

            if (!is_object($result)) {
                show_error('The rpc response is empty, or have invalid format');
            }
            
            //if ('failure' == $result->type) {
            //    throw new EDepositApiCall($result->errormsg, $result->errorcode);
            //}
        }
        catch (PixabayClient $e){
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
        $this->fullurl = $url . http_build_query($parameters);
        return $this->createHttpClient()->post($url, $parameters);
    }

    /**
     * For testing purposes.
     *
     * @return CurlHttpClient 
     */
    protected function createHttpClient()
    {
        return new PixCurlHttpClient();
    }
}

/**
 * Simple HTTP client based on cURL extension.
 *
 * You may use another HTTP client with one requared method {@link post}
 */

class PixCurlHttpClient
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
        $curlgetinfo = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if (200 != $curlgetinfo) {
            if (302 != $curlgetinfo)
            //throw new EServiceUnavailable('The API servise is unavailable');
                show_error($curlgetinfo.'-'.curl_error($this->ch), curl_errno($this->ch));
        }

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

class PixabayClient extends Exception {
    public function errorMessage() {
    //error message
    $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
    .': <b>'.$this->getMessage();
    return $errorMsg;
  }

};