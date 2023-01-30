<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2021/2/24
 * Time: 13:06
 */

namespace app\util;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait HasHttpRequest.
 *
 * @property string baseUri        base_uri
 * @property string timeout        timeout
 * @property string connectTimeout connect_timeout
 */
trait HasHttpRequest
{
    /**
     * Http client.
     *
     * @var null|Client
     */
    protected $httpClient = null;
    /**
     * Http client options.
     *
     * @var array
     */
    protected $httpOptions = [];

    /**
     * Http client options.
     *
     * @var int
     */
    private $concurrency    = 10;

    /**
     * Send a GET request.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @param string $endpoint
     * @param array  $query
     * @param array  $headers
     *
     * @return array|string
     */
    public function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query'   => $query,
        ]);
    }
    /**
     * Send a POST request.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @param string       $endpoint
     * @param string|array $data
     * @param array        $options
     *
     * @return array|string
     */
    public function post($endpoint, $data, $options = [])
    {
        if (!is_array($data)) {
            $options['body'] = $data;
        } else {
            $options['form_params'] = $data;
        }
        return $this->request('post', $endpoint, $options);
    }

    /**
     * Send a POOL request.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @param string       $requests
     * @param array $options
     *
     * @return null
     */
    public function pool($requests, array $options)
    {

        $pool = new Pool(
            $this->getHttpClient(),
            $requests(),
            array_merge(['concurrency' => $this->concurrency], $options)
        );

        $promise = $pool->promise();

        $promise->wait();
    }

    /**
     * Send request.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $options
     *
     * @return array|string
     */
    public function request($method, $endpoint, $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient()->{$method}($endpoint, $options));
    }

    /**
    * @param array $options
    *
    * @return array
    */
   protected function fixJsonIssue(array $options): array
   {
       if (isset($options['json']) && is_array($options['json'])) {
           $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

           if (empty($options['json'])) {
               $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
           } else {
               $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
           }

           unset($options['json']);
       }

       return $options;
   }
    /**
     * Set http client.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @param Client $client
     *
     * @return $this
     */
    public function setHttpClient(Client $client)
    {
        $this->httpClient = $client;
        return $this;
    }
    /**
     * Get default options.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            'base_uri'        => property_exists($this, 'baseUri') ? $this->baseUri : '',
            'timeout'         => property_exists($this, 'timeout') ? $this->timeout : 5.0,
            'connect_timeout' => property_exists($this, 'connectTimeout') ? $this->connectTimeout : 5.0,
            'verify' => false
        ], $this->httpOptions);
    }
    /**
     * Return http client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = $this->getDefaultHttpClient();
        }
        return $this->httpClient;
    }

    public function resetHttpClient()
    {
        $this->setHttpClient($this->getDefaultHttpClient());
        return $this;
    }

    /**
     * Get default http client.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @return Client
     */
    protected function getDefaultHttpClient()
    {
        return new Client($this->getOptions());
    }
    /**
     * Convert response.
     *
     * @author brooke <overbob@yeah.net>
     *
     * @param ResponseInterface $response
     *
     * @return array|string
     */
    public function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();

        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
        }else if(is_array(json_decode($contents, true))){
            return json_decode($contents, true);
        }

        return $contents;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getHttpClient(), $method], $args);
    }
}
