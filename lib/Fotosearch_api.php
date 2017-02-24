<?php  

require_once('FotosearchClientConfig.php');
require_once('FotosearchParams.php');

/**
 * Interface to the Fotosearch API client.
 * 
 * 
 */
class Fotosearch_Api
{
    const VERSION = '0.1';
    
    /**
     * Fotosearch uri.
     *
     * @var     string
     */
    protected $apiUrl;

    /**
     * Fotosearch API key.
     *
     * @var     string
     */
    protected $apiKey;

    /**
     * Fotosearch API password.
     *
     * @var     string
     */
    protected $apiPwd;

    /**
     * Fotosearch API session id.
     *
     * @var     string
     */
    protected $sessionId;

    /**
     * Fotosearch API session name.
     *
     * @var     string
     */
    protected $sessionName;


    /**
     * Constructor
     *
     * @param   string $apiUrl  Fotosearch RPC uri
     * @param   string $apiKey  Fotosearch API key
     */
    public function  __construct()
    {
        $this->apiUrl = FOTOSEARCH_API_URL;
        $this->apiKey = FOTOSEARCH_API_KEY;
    }

    public function getApiKey()
    {
        echo "Api Key:".$this->apiKey."<br/>";
        echo "Api Url:".$this->apiUrl."<br/>";
    }
    /**
     * This method makes possible to search media in the Fotosearch image bank.
     * 
     * The $criteria array must conform to the following format:
     * <code>
     * array(
     *  // only FotosearchParams::SEARCH_QUERY is mandatory, the rest are optional
     *  FotosearchParams::SEARCH_QUERY  => 'The keywords to search for'. Examples: 
     *                                      - woman 
     *                                      - woman+AND +sun
     *                                      - woman+NOT+animal',
     *  FotosearchParams::SEARCH_LIMIT  => 'Number of images per page. Default value is 20',
     *  FotosearchParams::SEARCH_OFFSET => 'Can be used as an alternative to Pagesize. Using Offset, you can
     *                                      have the results to starting listing at any desired position. Note
     *                                      that this parameter is one-base. To start listing at position 1,
     *                                      enter Offset=1, not offset=0',
     *  FotosearchParams::SEARCH_COLOR  => 'Filter for colors. Possible values:
     *                                      - 1 for only color images
     *                                      - 0 for only black & white images
     *                                      (omit this parameter to search for all images without filtering
     *                                      for colors)',
     *  FotosearchParams::SEARCH_ORIENTATION=> 'Filter for orientation. Possible values:
     *                                          - 1 portrait images only
     *                                          - 2 landscape images only
     *                                          - 3 Square images only
     *                                          (omit this parameter to search for all images without filtering
     *                                          for orientation)',
     *  FotosearchParams::SEARCH_ASSET_TYPE  => 'Filter for type of content. Possible values:
     *                                          - ‘photos’ for photos only
     *                                          - ‘font’ for fonts only
     *                                          - ‘vectors’ for vectors only
     *                                          - ‘video’ for video only
     *                                          (omit this parameter to search for all content without filtering
     *                                          for content type)',
     *  FotosearchParams::SEARCH_USE_VIDEO => 'Include video in the search. Default value is 0. Possible values:
     *                                          - 0 to exclude video from search
     *                                          - 1 to include video in search',
     *  FotosearchParams::SEARCH_LANGUAGE  => 'Language of the keywords. Can be omitted for English. Possible
     *                                          values are:
     *                                          - en
     *                                          - de
     *                                          - it
     *                                          - fr
     *                                          - es
     *                                          - nl'
     * )
     * </code>
     *
     * Response format: 
     * <code>
     *
     * <images>
     *      <image code="media id">
     *          <imgcaption>
     *              <![CDATA[media caption]]>
     *          </imgcaption>
     *          <thumburl>
     *              <![CDATA[thumbnail url]]>
     *          </thumburl>
     *      </image>
     *      <results total="number of rows found"/>
     * </images>
     *
     * </code>
     *
     * @param   GET variables string request
     * @return  XML 
     */
    //public function search($criteria = array())
    public function search($query, $limit, $offset)
    {
        $postParams = array(
            FotosearchParams::SEARCH_QUERY => $query,
            FotosearchParams::SEARCH_LIMIT => $limit,
            FotosearchParams::SEARCH_OFFSET => $offset,
            FotosearchParams::API_KEY => $this->apiKey,
            FotosearchParams::SEARCH_SUBSCRIPTION => 'true'
            );

        //$postParams = array_merge($postParams, $criteria);
        $url = $this->getFullURI(FotosearchParams::SEARCH_CMD, $postParams);
        
        return $url;  
        //return $this->checkResponse($this->post($url, $postParams));
    }

    /**
     * This method return all information about a media.
     * 
     * Response format:
     * <code>
     * <image>
     *  <Code>
     *      The code of asset
     *  </Code>
     *  <Title>
     *      The caption of the asset
     *  </Title>
     *  <contributorId>
     *      ID the contributor if available. Can be ignored. This is only in for
     *      compatibility purposes with previous versions of the API.
     *  </contributorId>
     *  <colorType>
     *      Color of the asset. ‘True’ for color images, ‘false’ for black & white
     *  </colorType>
     *  <orientation>
     *      Orientation of the asset. 1 for Portrait, 2 for Landscape, 3 for square
     *  </orientation>
     *  <licenceType>
     *      Always 1. Can be ignored. This is only in for compatibility purposes with
     *      previous versions of the API.
     *  </licenceType>
     *  <modelRelease>
     *      Model release of the asset. ‘True’ if model release is in place, ‘false’ if not
     *  </modelRelease>
     *  <propertyRelease>
     *      Property release of the asset. ‘True’ if property release is in place, ‘false’ if not
     *  </propertyRelease>
     * </image>
     * </code>
     *
     * @param   integer $mediaId
     * @return  stdClass
     */
    public function getMediaData($mediaId)
    {
        $postParams = array(
            FotosearchParams::API_KEY => $this->apiKey,
            FotosearchParams::MEDIA_IMAGE_CODE => $mediaId
           );
        $url = $this->getFullURI(FotosearchParams::MEDIA_DATA_CMD, $postParams);
        return $this->checkResponse($this->post($url, $postParams));
    }

    /**
    *  This method return the URL of an image preview
    *  @param string $mediaId
    *  @param string $mediaSize
    *  @return string
    */
    public function getMediaPreview($mediaId, $mediaSize=DEF_MEDIA_SIZE)
    {
         $postParams = array(
            FotosearchParams::API_KEY => $this->apiKey,
            FotosearchParams::MEDIA_IMAGE_CODE => $mediaId
           );
        $url = $this->getFullURI(FotosearchParams::ASSET_PREVIEW_CMD, $postParams);
        return $url;              
    }

    /**
    *  This method return an Id for download an image as resolution is indicated
    *  @param string $usage
    *  @param string $saveAs
    *  @param string $resolution
    *  @param string $mediaId
    *  @return stdClass
    */
    public function getDownloadURL($usage, $saveAs, $resolution, $mediaId)
    {
         $postParams = array(
            FotosearchParams::API_KEY => $this->apiKey,
            FotosearchParams::USAGE => $usage,
            FotosearchParams::SAVE_AS => $saveAs,
            'resolution' => $resolution,
            'mediacode' => $mediaId
           );
        $url = $this->getFullURI(FotosearchParams::GET_DOWNLOAD_URL, $postParams);
        return $this->checkResponse($this->post($url, $postParams));            
    }

    /**
    *  This method makes the image download
    * @param   string $downloadId
    * @return  string
    */
    public function goDownload($downloadId)
    {
        $postParams = array(
            FotosearchParams::API_KEY => $this->apiKey,
            'downloadId' => $downloadId
           );
        $url = $this->getFullURI(FotosearchParams::DOWNLOAD_CMD, $postParams);
        return $url;
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
        $uri = $this->apiUrl . $method;
        if ( $method === FotosearchParams::MEDIA_DATA_CMD )
            $uri .= array_pop($query) . "/?";
        elseif ( $method === FotosearchParams::GET_DOWNLOAD_URL ) {
            $uri .= array_pop($query) . "/";
            $uri .= array_pop($query) . "/?";
        }
        elseif ( $method === FotosearchParams::DOWNLOAD_CMD ) {
            $uri .= array_pop($query) . "/?";
        }

        $uri .= http_build_query($query);

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
                throw new EServiceUnavailable('Empty search');
                
            $result = $this->decodeResponse($response); 

            if (!is_object($result)) {
                throw new EResponseFail('The rpc response is empty, or have invalid format');
            }
            
            //if ('failure' == $result->type) {
            //    throw new EDepositApiCall($result->errormsg, $result->errorcode);
            //}
        }
        catch (EResponseFail $e){
            echo $e->errorMessage();
            return false;           
        }
        catch (EServiceUnavailable $e){
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
        return $this->createHttpClient()->post($url, $parameters);
    }

    /**
     * For testing purposes.
     *
     * @return CurlHttpClient 
     */
    protected function createHttpClient()
    {
        return new CurlHttpClient();
    }

}

/**
 * Simple HTTP client based on cURL extension.
 *
 * You may use another HTTP client with one requared method {@link post}
 */
class CurlHttpClient
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
        try {
            if (false === curl_setopt_array($this->ch, array(
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_URL             => $url
            ))) {
                throw new ECurlFail('Error at setting cURL options, reason: '.curl_error($this->ch), curl_errno($this->ch));
            }

            if (false === $result = curl_exec($this->ch)) {
                throw new ECurlFail('Error at execute cURL request, reason: '.curl_error($this->ch), curl_errno($this->ch));
            }

            if ('No results' === $result)
                $result = false;
            
            elseif (200 != curl_getinfo($this->ch, CURLINFO_HTTP_CODE)) {
                throw new EServiceUnavailable('The API service is unavailable');
            }
        }
        catch (EServiceUnavailable $e) {
            echo $e->errorMessage();
            //show_error($e->errorMessage());
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

?>
