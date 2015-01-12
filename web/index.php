<?php

require_once __DIR__.'/../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

//$app['debug'] = true;
//ini_set('display_errors', 1);
//error_reporting(E_ALL);


// Keys that are allowed to access service. You can have multiple keys if you
// want, but you need at least one. If you need a random key, go here:
// https://www.random.org/strings/?num=5&len=20&digits=on&loweralpha=on&format=plain
$app['authKeys'] = [
];


// Commands that can be run.
$app['commands'] = [
  'hello' => 'echo -n "Hello world!"'
];


$app->get('/', function () {
  return 'run_it';
});

$app->get('/{commandName}', function($commandName) use ($app) {
  if (!$app['request']->isSecure() && !$app['debug'])
  {
    return new Response('SSL required. Or delete this line, if you\'re crazy.', 403);
  }

  if (!isAuthenticated($app))
  {
    $msg = 'Invalid authentication.' . (!count($app['authKeys']) ? ' You do not have any auth keys configured.'  : '');
    return new Response($msg , 401);
  }

  $commands = $app['commands'];

  if (!isset($commands[$commandName]))
  {
    return new Response('Command not found.' , 404);
  }

  $shell = new \MrRio\ShellWrap();
  $returnCode = 0;
  $stdErr = '';

  try
  {
    $shell->__call($commands[$commandName], []);
  }
  catch (\MrRio\ShellWrapException $e)
  {
    $returnCode = $e->getCode();
    $stdErr = $e->getMessage();
  }


  switch($app['request']->query->get('format', 'json'))
  {
    case 'json':
      $response = new Response();
      $response->setContent(json_encode([
        'success' => $returnCode === 0,
        'command' => \MrRio\ShellWrap::$execString,
        'output' => $shell->__toString(),
        'error_output' => $stdErr,
        'return_code' => $returnCode
      ], JSON_PRETTY_PRINT));
      $response->headers->set('Content-Type', 'application/json');
      $response->headers->set('Access-Control-Allow-Origin', '*');
      return $response;

    case 'pre':
      return new Response('<pre>' . htmlspecialchars($shell->__toString()) . '</pre>');

    case 'raw':
      return new Response($shell->__toString());

    default:
      return new Response('Invalid format', 500);
  }
});


$app->run();


function isAuthenticated($app)
{
  $authToken = $app['request']->headers->get('authorization') ?: $app['request']->query->get('auth_token');

  foreach($app['authKeys'] as $key)
  {
    if (compareStrings($key,$authToken))
    {
      return true;
    }
  }

  return false;
}


function compareStrings($str1, $str2) // constant-time string comparison
{
  $str1 = (string) $str1;
  $str2 = (string) $str2;
  $len1 = (extension_loaded('mbstring') && ini_get('mbstring.func_overload') & 2) ? mb_strlen($str1, '8bit') : strlen($str1);
  $len2 = (extension_loaded('mbstring') && ini_get('mbstring.func_overload') & 2) ? mb_strlen($str2l, '8bit') : strlen($str2);
  $len = min($len1, $len2);

  $result = 0;
  for ($i = 0; $i < $len; $i++)
  {
    $result |= ord($str1[$i]) ^ ord($str2[$i]);
  }
  $result |= $len1 ^ $len2;

  return ($result === 0);
}
