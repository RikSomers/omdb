<?php

namespace RikSomers\OMDB;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Illuminate\Config\Repository;
use RikSomers\OMDB\Exceptions\InvalidParameterException;
use RikSomers\OMDB\Exceptions\InvalidParameterValueException;

class OMDBClient
{
    /**
     * Array of filters that are allowed to be passed.
     *
     * @var array
     */
    protected $allowedParameters = [
        'i', 't', 'type', 'y', 'plot', 'v', 's',
    ];

    protected $allowedParametersValues = [
        'type' => ['movie', 'series'],
        'plot' => ['short', 'full'],
    ];

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Instance of the configuration repository.
     *
     * @var \Illuminate\Config\Repository
     */
    private $config;

    /**
     * ClientFactory constructor.
     *
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->client = $this->createBaseClient();
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function createBaseClient() : Client
    {
        return new Client([
            'base_uri' => $this->config->get('omdb.base_uri', 'http://www.omdbapi.com/'),
        ]);
    }

    /**
     * @param  array                                                     $parameters
     * @throws \RikSomers\OMDB\Exceptions\BadAPIResponseException
     * @throws \RikSomers\OMDB\Exceptions\InvalidJsonException
     * @throws \RikSomers\OMDB\Exceptions\InvalidParameterException
     * @throws \RikSomers\OMDB\Exceptions\InvalidParameterValueException
     * @throws \RikSomers\OMDB\Exceptions\NoResultsException
     * @throws \RikSomers\OMDB\Exceptions\UnauthorizedException
     * @return \RikSomers\OMDB\ResultFactory
     */
    public function query(array $parameters) : ResultFactory
    {
        $this->validateParameters($parameters);

        $parameters = $this->addRequiredParameters($parameters);

        $queryString = '?' . http_build_query($parameters);

        $response = $this->client->get($queryString);

        return new ResultFactory($response, $this);
    }

    /**
     * @param $parameter
     * @return bool
     */
    protected function parameterIsAllowed($parameter) : bool
    {
        return in_array($parameter, $this->allowedParameters);
    }

    /**
     * @param $parameter
     * @param string $value
     * @return bool
     */
    protected function parameterValueIsAllowed(string $parameter, string $value) : bool
    {
        return in_array($value, $this->allowedParametersValues[$parameter]);
    }

    /**
     * @param $parameter
     * @return bool
     */
    protected function parameterHasOptions(string $parameter) : bool
    {
        return array_key_exists($parameter, $this->allowedParametersValues);
    }

    /**
     * @param  array                                                     $parameters
     * @throws \RikSomers\OMDB\Exceptions\InvalidParameterException
     * @throws \RikSomers\OMDB\Exceptions\InvalidParameterValueException
     * @return bool
     */
    protected function validateParameters(array $parameters) : bool
    {
        if (! isset($parameters['i']) && ! isset($parameters['t']) && ! isset($parameters['s'])) {
            throw new InvalidArgumentException('Ttid or title parameter is not set. Search not possible.');
        }

        foreach ($parameters as $parameter => $value) {
            if (! $this->parameterIsAllowed($parameter)) {
                throw new InvalidParameterException("The '${$parameter}' parameter is not allowed.");
            }

            if ($this->parameterHasOptions($parameter) && ! $this->parameterValueIsAllowed($parameter, $value)) {
                throw new InvalidParameterValueException("The value '${$value}'' is not allowed for parameter '${parameter}''");
            }
        }

        return true;
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function addRequiredParameters(array $parameters) : array
    {
        return array_merge([
            'apikey' => $this->config->get('omdb.key'),
            'r' => 'json',
        ], $parameters);
    }
}
