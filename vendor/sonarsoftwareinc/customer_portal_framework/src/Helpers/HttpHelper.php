<?php

namespace SonarSoftware\CustomerPortalFramework\Helpers;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use SonarSoftware\CustomerPortalFramework\Exceptions\ApiException;

class HttpHelper
{
    private $guzzle;

    /**
     * HttpHelper constructor.
     */
    public function __construct()
    {
        $this->guzzle = new Client();
        //This is to maintain backwards compatibility with the old file location.
        if (file_exists(__DIR__ . '/../../../../../.env')) {
            $dotenv = new Dotenv(__DIR__ . '/../../../../../');
        } else if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = new Dotenv(__DIR__ . '/../');
        } else {
            //Just skip loading if no .env is present.
            return;
        }

        $dotenv->load();
        $dotenv->required([
            'API_USERNAME',
            'API_PASSWORD',
            'SONAR_URL',
        ])->notEmpty();
    }

    /**
     * @param $endpoint
     * @param $array - Array of data to be JSON encoded
     * @return mixed
     * @throws ApiException
     */
    public function post($endpoint, $array)
    {
        try {
            $response = $this->guzzle->post($this->cleanEndpoint($endpoint), [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'timeout' => 20,
                ],
                'auth' => [
                    getenv("API_USERNAME"),
                    getenv("API_PASSWORD"),
                ],
                'json' => $array
            ]);
        }
        catch (ClientException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }
        catch (TransferException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }

        $body = json_decode($response->getBody());

        return $body->data;
    }

    /**
     * @param $endpoint
     * @param int $page
     * @return mixed
     * @throws ApiException
     */
    public function get($endpoint, $page = 1)
    {
        $page = intval($page);
        try {
            $response = $this->guzzle->get($this->cleanEndpoint($endpoint) . "?page=$page", [
                'headers' => [
                    'timeout' => 20,
                ],
                'auth' => [
                    getenv("API_USERNAME"),
                    getenv("API_PASSWORD"),
                ],
            ]);
        }
        catch (ClientException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }
        catch (TransferException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }

        $body = json_decode($response->getBody());

        return $body->data;
    }

    /**
     * @param $endpoint
     * @param $array - Array of data to be JSON encoded
     * @return mixed
     * @throws ApiException
     */
    public function patch($endpoint, $array)
    {
        try {
            $response = $this->guzzle->patch($this->cleanEndpoint($endpoint), [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'timeout' => 20,
                ],
                'auth' => [
                    getenv("API_USERNAME"),
                    getenv("API_PASSWORD"),
                ],
                'json' => $array
            ]);
        }
        catch (ClientException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }
        catch (TransferException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }

        $body = json_decode($response->getBody());

        return $body->data;
    }

    /**
     * @param $endpoint
     * @return mixed
     * @throws ApiException
     */
    public function delete($endpoint)
    {
        try {
            $response = $this->guzzle->delete($this->cleanEndpoint($endpoint), [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'timeout' => 20,
                ],
                'auth' => [
                    getenv("API_USERNAME"),
                    getenv("API_PASSWORD"),
                ],
            ]);
        }
        catch (ClientException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }
        catch (TransferException $e)
        {
            $textResponse = json_decode($e->getResponse()->getBody());
            throw new ApiException($this->transformErrorToMessage($textResponse));
        }

        $body = json_decode($response->getBody());

        return $body->data;
    }

    /**
     * Remove leading or trailing forward slashes from the endpoint.
     * @param $endpoint
     * @return string
     */
    private function cleanEndpoint($endpoint)
    {
        $endpoint = ltrim($endpoint,"/");
        $endpoint = rtrim($endpoint,"/");
        return getenv('SONAR_URL') . "/api/v1/" . $endpoint;
    }

    /**
     * Transform a JSON error response into a string message
     * @param $jsonDecodedObject
     * @return null|string
     */
    private function transformErrorToMessage($jsonDecodedObject)
    {
        if (property_exists($jsonDecodedObject,"error"))
        {
            $message = $jsonDecodedObject->error->message;
            if (!is_object($message))
            {
                return $message;
            }
            else
            {
                $messageArray = [];
                foreach ($message as $key => $value)
                {
                    array_push($messageArray,$value);
                }
                return implode(", ",$messageArray);
            }
        }
        return null;
    }
}
