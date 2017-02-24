<?php  

require_once('IngimagesClientConfig.php');
require_once('IngimagesParams.php');

/**
 * Interface to the Ingimages API client.
 * 
 * 
 */
class Ingimages_Api
{
    const VERSION = '0.1';
    
    /**
     * Ingimages uri.
     *
     * @var     string
     */
    protected $apiUrl;

    /**
     * Ingimages API key.
     *
     * @var     string
     */
    protected $apiKey;

    /**
     * Ingimages API password.
     *
     * @var     string
     */
    protected $apiPwd;

    /**
     * Ingimages API session id.
     *
     * @var     string
     */
    protected $sessionId;

    /**
     * Ingimages API session name.
     *
     * @var     string
     */
    protected $sessionName;


    /**
     * Constructor
     *
     * @param   string $apiUrl  Ingimages RPC uri
     * @param   string $apiKey  Ingimages API key
     */
    public function  __construct()
    {
        $this->apiUrl = INGIMAGES_API_URL;
        $this->apiKey = INGIMAGES_API_KEY;
        $this->apiPwd = INGIMAGES_API_PASSWORD;
    }

    public function getApiKey()
    {
        echo "Api Key:".$this->apiKey."<br/>";
        echo "Api Url:".$this->apiUrl."<br/>";
        echo "Api Pwd:".$this->apiPwd;
    }
    /**
     * This method makes possible to search media in the Ingimages image bank.
     * 
     * The $criteria array must conform to the following format:
     * <code>
     * array(
     *  // only IngimagesParams::SEARCH_QUERY is mandatory, the rest are optional
     *  IngimagesParams::SEARCH_QUERY  => 'The keywords to search for'. Examples: 
     *                                      - woman 
     *                                      - woman+AND +sun
     *                                      - woman+NOT+animal',
     *  IngimagesParams::SEARCH_LIMIT  => 'Number of images per page. Default value is 20',
     *  IngimagesParams::SEARCH_OFFSET => 'Can be used as an alternative to Pagesize. Using Offset, you can
     *                                      have the results to starting listing at any desired position. Note
     *                                      that this parameter is one-base. To start listing at position 1,
     *                                      enter Offset=1, not offset=0',
     *  IngimagesParams::SEARCH_COLOR  => 'Filter for colors. Possible values:
     *                                      - 1 for only color images
     *                                      - 0 for only black & white images
     *                                      (omit this parameter to search for all images without filtering
     *                                      for colors)',
     *  IngimagesParams::SEARCH_ORIENTATION=> 'Filter for orientation. Possible values:
     *                                          - 1 portrait images only
     *                                          - 2 landscape images only
     *                                          - 3 Square images only
     *                                          (omit this parameter to search for all images without filtering
     *                                          for orientation)',
     *  IngimagesParams::SEARCH_ASSET_TYPE  => 'Filter for type of content. Possible values:
     *                                          - ‘photos’ for photos only
     *                                          - ‘font’ for fonts only
     *                                          - ‘vectors’ for vectors only
     *                                          - ‘video’ for video only
     *                                          (omit this parameter to search for all content without filtering
     *                                          for content type)',
     *  IngimagesParams::SEARCH_USE_VIDEO => 'Include video in the search. Default value is 0. Possible values:
     *                                          - 0 to exclude video from search
     *                                          - 1 to include video in search',
     *  IngimagesParams::SEARCH_LANGUAGE  => 'Language of the keywords. Can be omitted for English. Possible
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
            IngimagesParams::API_KEY => $this->apiKey,
            IngimagesParams::API_PASSWORD => $this->apiPwd,
            IngimagesParams::SEARCH_LIMIT => $limit,
            IngimagesParams::SEARCH_QUERY => $query,
            IngimagesParams::SEARCH_OFFSET => $offset,
            IngimagesParams::SEARCH_COLOR => '',
            IngimagesParams::SEARCH_ORIENTATION => ''
            );

        //$postParams = array_merge($postParams, $criteria);
        $url = $this->getFullURI(IngimagesParams::SEARCH_CMD, $postParams);
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
            IngimagesParams::API_KEY => $this->apiKey,
            IngimagesParams::API_PASSWORD => $this->apiPwd,
            IngimagesParams::MEDIA_IMAGE_CODE => $mediaId
           );
        $url = $this->getFullURI(IngimagesParams::MEDIA_DATA_CMD, $postParams);
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
            IngimagesParams::MEDIA_IMAGE_CODE => $mediaId,
            'size' => $mediaSize
           );
        $url = $this->getFullURI(IngimagesParams::ASSET_PREVIEW_CMD, $postParams);
        return $url;              
    }

    public function ReportSales($plan, $media, $type, $orderId, $userId, $price)
    {
         $postParams = array(
            IngimagesParams::SALES_CURRENCY => 'USD',
            IngimagesParams::SALES_TYPE => $plan,
            IngimagesParams::MEDIA_IMAGE_CODE => $media,
            IngimagesParams::DOWNLOAD_FORMAT => $type,
            IngimagesParams::SALES_ORDER_ID => date("Ymd").str_pad($orderId, 4, "0", STR_PAD_LEFT),
            IngimagesParams::SALES_USER_ID => $userId,
            IngimagesParams::SALES_PRICE => $price,
            IngimagesParams::API_KEY => $this->apiKey,
            IngimagesParams::API_PASSWORD => $this->apiPwd
           );

        $url = $this->getFullURI(IngimagesParams::SALES_REPORT_CMD, $postParams);
        return $this->checkResponse($this->post($url, $postParams));
    }

    public function getDownloadToken($media, $type, $downloadRef)
    {
         $postParams = array(
            IngimagesParams::DOWNLOAD_GET_TOKEN => '1',
            IngimagesParams::MEDIA_IMAGE_CODE => $media,
            IngimagesParams::DOWNLOAD_FORMAT => $type,
            IngimagesParams::DOWNLOAD_REF => $downloadRef,
            IngimagesParams::API_KEY => $this->apiKey,
            IngimagesParams::API_PASSWORD => $this->apiPwd
           );

        $url = $this->getFullURI(IngimagesParams::DOWNLOAD_ASSET, $postParams);
        //return $url;
        return $this->post($url, $postParams);
    }

    public function goDownload($media, $downloadRef, $token)
    {
        $postParams = array(
            IngimagesParams::MEDIA_IMAGE_CODE => $media,
            IngimagesParams::DOWNLOAD_REF => $downloadRef,
            IngimagesParams::DOWNLOAD_TOKEN => $token
            );

         $url = $this->getFullURI(IngimagesParams::DOWNLOAD_ASSET, $postParams);
         return $url;
         //return $this->checkResponse($this->post($url, $criteria));
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
        if ( $method === IngimagesParams::ASSET_PREVIEW_CMD )
            $uri .= "/" . $query['size'] . "/" . $query[IngimagesParams::MEDIA_IMAGE_CODE];
        else
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
        if ($this->is_valid_xml($response)) {
            $result = $this->decodeResponse($response);

            if (!is_object($result)) {
                //throw new EResponseFail('The rpc response is empty, or have invalid format');
                show_error('The rpc response is empty, or have invalid format');
            }

            if ('failure' == $result->type) {
                //throw new EDepositApiCall($result->errormsg, $result->errorcode);
                show_error($result->errormsg);
            }
            elseif ($result->error) 
                 show_error('Provided token is invalid or expired!');
            
        } 
        else $result = $response;
        //if (isset($result)) {
        //    if (!isset($result->results['total']) || !isset($result->code))
                //print_r($result); echo "entro aqui";
        //        return false;
        //}
        
        //$res = (int)$result->results['total'];
        //if ( (int)$result->results['total'] === 0 )
        //    return false;
            //show_error('No results');
        
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
        $xml = simplexml_load_string($response, NULL, LIBXML_NOCDATA);
        if ($xml)
            return $xml;
        else
            return $response;

    }

    protected function is_valid_xml ( $xml ) {
        libxml_use_internal_errors( true );
         
        $doc = new DOMDocument('1.0', 'utf-8');
         
        $doc->loadXML( $xml );
         
        $errors = libxml_get_errors();
         
        return empty( $errors );
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
        return new IngCurlHttpClient();
    }

}

/**
 * Simple HTTP client based on cURL extension.
 *
 * You may use another HTTP client with one requared method {@link post}
 */

class IngCurlHttpClient
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

/*
class EDepositClient extends Exception {};
class EResponseFail extends EDepositClient {};
class EDepositApiCall extends EDepositClient {};
class ECurlFail extends EDepositClient {};
class EServiceUnavailable extends EDepositClient {};
class EAuthenticationRequired extends EDepositClient {};
*/
?>
