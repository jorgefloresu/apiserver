<?php

/**
 * Constants for predefined Fotosearch API commands and parameters.
 * 
 * 
 */
class FotosearchParams
{
    const API_KEY            = 'api_key';

    const SEARCH_CMD         = '/api/v1.0/image/search/?';
    const SEARCH_QUERY       = 'phrase';
    const SEARCH_LIMIT       = 'limit';
    const SEARCH_OFFSET      = 'offset';
    const SEARCH_ORIENTATION = 'orientation';
    const SEARCH_COLOR       = 'color';
    const SEARCH_FILTERS     = 'filters';
    const SEARCH_LANGUAGE	 = 'language';
    const SEARCH_PAGE		 = 'page';
    const SEARCH_ASSET_TYPE	 = 'assettype';
    const SEARCH_USE_VIDEO	 = 'usevideo';
    const SEARCH_SUBSCRIPTION= 'subscription_only';
    const RESULT_NUM_ROWS    = '';

    const MEDIA_DATA_CMD     = '/api/v1.0/image/';
    const MEDIA_IMAGE_CODE   = 'imagecode';

    const ASSET_PREVIEW_CMD  = '/api/v1.0/image/';

    const GET_DOWNLOAD_URL   = '/api/v1.0/image/get_download_url/';
    const USAGE              = 'usage';
    const SAVE_AS            = 'saveas';

    const DOWNLOAD_CMD       = '/api/v1.0/image/download/';

}

?>