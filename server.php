<?php


require '../vendor/autoload.php';
require_once('./lib/Fotosearch_api.php');
require_once('./lib/Pixabay_api.php');
require_once('./lib/Deposit_api.php');
require_once('./lib/Ingimages_api.php');

$i = 0;

$providers = array(    
    'FS' => new Fotosearch_api,
    'PX' => new Pixabay_api,
    'DP' => new Deposit_api,
    'IN' => new Ingimages_api
);


$app = function ($request, $response) use (&$i, &$query) {
    $i++;

    //$text = "This is request number $i.\n";
    $query = $request->getQuery();
    if ($query['pro'])
    	$text = loadinfo($query);
    else
    	$text = "Nothing to display";
    $headers = array('Content-Type' => 'text/plain');

    $response->writeHead(200, $headers);
    $response->end($text);
    //echo $text;
};

$providerUrl = function($query) use ($providers) {
    $offset = ($query['pro'] == 'PX') ? 1 : 0;
    return $providers[ $query['pro'] ]->search($query['q'], 25, $offset);
};

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$http->on('request', $app);

$address = 'localhost';

echo "Server listening on " . $address . "\n\r";

$socket->listen(8000);
$loop->run();


function loadinfo($query) {

    global $providerUrl;

	$loop = React\EventLoop\Factory::create();

	$dnsResolverFactory = new React\Dns\Resolver\Factory();
	$dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);
	$factory = new React\HttpClient\Factory();
	$client = $factory->create($loop, $dnsResolver);

    $output = "";

    $request = $client->request('GET', $providerUrl($query));
    $request->on('response', function ($response) use (&$output,$query) {
        $buffer = '';
        $response->on('data', function ($data) use (&$buffer) {
            $buffer .= $data;
            //$output .= ".";
    	});

	    $response->on('end', function () use (&$buffer, &$output, $query) {
            $data = createStructure($query['pro'], $buffer);
	        //$latest = ($query['pro']=='PX') ? $decoded['total'] : $decoded['meta']['total_count'];
	        /*$author = $latest['author']['name'];
	        $date = date('F j, Y', strtotime($latest['author']['date']));
	        $output .= "Latest commit on react was done by {$author} on {$date}\n\r";
	        $output .= "{$latest['message']}\n\r";*/
            $output = $query['callback'] . '(' . $data . ')';
	    });
	});

    $request->on('end', function ($error, $response) use (&$output) {
        $output = $error;
    });

    $request->end();
	$loop->run();

	return $output;
}

function createStructure($provider, $buffer) {
    $data = array();
    switch ($provider) {
        case 'PX':
            $decoded = json_decode($buffer, true);
            $latest = $decoded['total'];
            break;
        case 'FS':
            $decoded = json_decode($buffer, true);
            $latest = $decoded['meta']['total_count'];
           foreach ($decoded['objects'] as $value) {
                $data[] = [
                        'code'     => $value['id'],
                        'caption'  => $value['title'],
                        'thumburl' => $value['thumbnail_url']
                    ];
            };
            break;
        case 'DP':
            $decoded = json_decode($buffer, true);
            $latest = $decoded['count'];
            foreach ($decoded['result'] as $value) {
                $data[] = [
                    'code'     => $value['id'],
                    'caption'  => $value['title'],
                    'thumburl' => $value['medium_thumbnail']
                ];
            };
            break;                
        case 'IN':
            $decoded = simplexml_load_string($buffer, NULL, LIBXML_NOCDATA);
            $latest = (string)$decoded->results['total'];
            foreach ($decoded as $value) {
                $thumb = (string)$value->thumburl;
                if ($thumb != '') {
                    $data[] = [
                            'code'     => (string)$value['code'],
                            'caption'  => (string)$value->imgcaption,
                            'thumburl' => $thumb
                        ];
                }
            };
            break;                
    }
    return json_encode(array('pro'=>$provider, 'total'=>$latest, 'images'=>$data));

}