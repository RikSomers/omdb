<?php

namespace RikSomers\OMDB;

use Psr\Http\Message\ResponseInterface;
use RikSomers\OMDB\Entities\Movie;
use RikSomers\OMDB\Entities\Series;
use RikSomers\OMDB\Exceptions\BadAPIResponseException;
use RikSomers\OMDB\Exceptions\InvalidJsonException;
use RikSomers\OMDB\Exceptions\NoResultsException;
use RikSomers\OMDB\Exceptions\TooManyResultsException;
use RikSomers\OMDB\Exceptions\UnauthorizedException;
use RikSomers\OMDB\Exceptions\UnknownEntityException;

class ResultFactory
{
    /**
     * @var string
     */
    private $rawResponse;
    /**
     * @var array
     */
    private $decodedResponse;
    /**
     * @var \RikSomers\OMDB\OMDBClient
     */
    private $client;

    /**
     * Result constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $rawResponse
     * @param \RikSomers\OMDB\OMDBClient          $client
     * @throws \RikSomers\OMDB\Exceptions\BadAPIResponseException
     * @throws \RikSomers\OMDB\Exceptions\InvalidJsonException
     * @throws \RikSomers\OMDB\Exceptions\NoResultsException
     * @throws \RikSomers\OMDB\Exceptions\UnauthorizedException
     */
    public function __construct(ResponseInterface $rawResponse, OMDBClient $client)
    {
        $this->rawResponse = $rawResponse;
        $this->client = $client;

        $this->handleResponse();
    }

    /**
     * @return string
     */
    public function getRaw() : string
    {
        return $this->rawResponse;
    }

    /**
     * @return array
     */
    public function getDecodedResponse(): array
    {
        return $this->decodedResponse;
    }

    /**
     * @return
     * @throws \RikSomers\OMDB\Exceptions\UnknownEntityException
     */
    public function toEntity()
    {
        try {
            $entity = ucfirst(strtolower($this->getDecodedResponse()['Type']));
        } catch(\Exception $e) {
            dd($this->getDecodedResponse());

            throw $e;
        }
        $method = 'to' . $entity;

        if (! method_exists($this, $method)) {
            throw new UnknownEntityException("Entity with the name of '${$entity}' is unknown and could not be converted to an Entity object.");
        }

        return $this->$method();
    }

    /**
     * @return \RikSomers\OMDB\Entities\Movie
     */
    public function toMovie()
    {
        return new Movie($this->getDecodedResponse());
    }

    /**
     * @return \RikSomers\OMDB\Entities\Series
     */
    public function toSeries()
    {
        return new Series($this->getDecodedResponse());
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \RikSomers\OMDB\Exceptions\TooManyResultsException
     */
    public function toCollection()
    {
        if ($this->getDecodedResponse()['totalResults'] > 10) {
            throw new TooManyResultsException("Too many results " . ($this->getDecodedResponse()['totalResults']) . " found. Please provide more filters.");
        }

        $movies = array_map(function($result) {
            $movie = $this->client->query(['i' => $result['imdbID'], 'type' => $result['Type']]);

            return $movie->toEntity();
        }, $this->getDecodedResponse()['Search']);

        return collect($movies);
    }

    /**
     *
     * @throws \RikSomers\OMDB\Exceptions\UnauthorizedException
     * @throws \RikSomers\OMDB\Exceptions\BadAPIResponseException
     * @throws \RikSomers\OMDB\Exceptions\InvalidJsonException
     * @throws \RikSomers\OMDB\Exceptions\NoResultsException
     */
    private function handleResponse()
    {
        $decodedResponse = json_decode((string)$this->rawResponse->getBody(), true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidJsonException();
        }

        if ($this->rawResponse->getStatusCode() == 401) {
            $this->handleUnauthorizedResponse($decodedResponse);
        }

        if ($this->rawResponse->getStatusCode() != 200) {
            $this->handleUnsuccessfulResponse($decodedResponse);
        }

        $this->handleSuccessfulResponse($decodedResponse);
    }

    /**
     * @param $decodedResponse
     * @throws \RikSomers\OMDB\Exceptions\UnauthorizedException
     */
    protected function handleUnauthorizedResponse($decodedResponse) : void
    {
        throw new UnauthorizedException($decodedResponse['Error']);
    }

    /**
     * @param $decodedResponse
     * @throws \RikSomers\OMDB\Exceptions\BadAPIResponseException
     */
    protected function handleUnsuccessfulResponse($decodedResponse) : void
    {
        throw new BadAPIResponseException($decodedResponse['Error']);
    }

    /**
     * @throws \RikSomers\OMDB\Exceptions\NoResultsException
     */
    protected function handleSuccessfulResponse($decodedResponse)
    {
        if ($decodedResponse['Response'] == 'False') {
            throw new NoResultsException($decodedResponse['Error']);
        }

        $this->decodedResponse = $decodedResponse;
    }
}
