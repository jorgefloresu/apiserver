<?php
// http client making a request to github api

require PHP_BINDIR.'/vendor/autoload.php';
$loop = React\EventLoop\Factory::create();
$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);
$factory = new React\HttpClient\Factory();
$client = $factory->create($loop, $dnsResolver);
/*
$request = $client->request('GET', 'https://api.github.com/repos/reactphp/react/commits');
$request->on('response', function ($response) {
    $buffer = '';
    $response->on('data', function ($data) use (&$buffer) {
        $buffer .= $data;
        echo ".";
    });
    $response->on('end', function () use (&$buffer) {
        $decoded = json_decode($buffer, true);
        $latest = $decoded[0]['commit'];
        $author = $latest['author']['name'];
        $date = date('F j, Y', strtotime($latest['author']['date']));
        echo "\n";
        echo "Latest commit on react was done by {$author} on {$date}\n";
        echo "{$latest['message']}\n";
    });
});
*/
$url = array('PX' =>
'https://pixabay.com/api/?key=2586119-bf922ee9f4967e79b96baaf40&q='.$_GET['q'].'&image_type=photo&pretty=true',
'FS' =>
'http://www.unlistedimages.com/api/v1.0/image/search/?api_key=bc007da7e2d855f22829d27ec98daf72&phrase='.$_GET['q']
);
//foreach ($url as $value) {
    $request = $client->request('GET', $url[$_GET['pro']]);
    $output = '';
    $request->on('response', function ($response) {
        $buffer = '';
        $response->on('data', function ($data) use (&$buffer) {
            $buffer .= $data;
            //echo $data.'<br><br>';
        });

        $response->on('end', function () use (&$buffer) {
            /*$decoded = json_decode($buffer, true);
            $latest = $decoded['hits'];
            $author = $latest[0]['user_id'];
            $date = $latest[0]['tags'];
            $output  = "User ID is {$author} of {$date}";
            $output .= "{$latest[0]['pageURL']}";*/
            // JSON encode and wrap the output in the jsoncallback parameter
            //$output = $_GET['jsoncallback'] . '(' . json_encode(array('content' => $output)) . ')';
            $output = $_GET['jsoncallback'] . '(' .  $buffer . ')';
            //header('content-type: application/json; charset=utf-8');
            echo $output;
        });
    });

    $request->on('end', function ($error, $response) {
        echo $error;
    });
    $request->end();
//}
$loop->run();