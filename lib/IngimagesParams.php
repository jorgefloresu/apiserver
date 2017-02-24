<?php

/**
 * Constants for predefined Ingimages API commands and parameters.
 * 
 * 
 */
class IngimagesParams
{
    const API_KEY            = 'apikey';
    const API_PASSWORD       = 'apipwd';

    const SEARCH_CMD         = 'assetSearch.do?';
    const SEARCH_QUERY       = 'keywords';
    const SEARCH_LIMIT       = 'pagesize';
    const SEARCH_OFFSET      = 'offset';
    const SEARCH_ORIENTATION = 'orientation';
    const SEARCH_COLOR       = 'color';
    const SEARCH_FILTERS     = 'filters';
    const SEARCH_LANGUAGE	 = 'language';
    const SEARCH_PAGE		 = 'page';
    const SEARCH_ASSET_TYPE	 = 'assettype';
    const SEARCH_USE_VIDEO	 = 'usevideo';
    const RESULT_NUM_ROWS    = '';

    const MEDIA_DATA_CMD     = 'assetDetails.do?';
    const MEDIA_IMAGE_CODE   = 'imagecode';

    const ASSET_PREVIEW_CMD  = 'assetPreview';
    const PREVIEW_TEXT       = 'T';
    const PREVIEW_WATERMARK  = 'As';

    const SEARCH_COLOR_OPT   = array(
                                  '1'  => 'Color',
                                  '0'  => 'Blanco y Negro'
                                );

    const SALES_REPORT_CMD   = 'assetSale.do?';
    const SALES_TYPE         = 'type';
    const SALES_ORDER_ID     = 'orderid';
    const SALES_USER_ID      = 'userid';
    const SALES_PRICE        = 'price';
    const SALES_CURRENCY     = "currencyISO";   

    const DOWNLOAD_ASSET     = 'assetDownload.do?';
    const DOWNLOAD_REF       = 'downloadreference';
    const DOWNLOAD_GET_TOKEN = 'gettoken';
    const DOWNLOAD_FORMAT    = 'format';
    const DOWNLOAD_TOKEN     = 'token';
}

?>