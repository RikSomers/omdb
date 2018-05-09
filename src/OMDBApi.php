<?php

namespace RikSomers\OMDB;

use RikSomers\OMDB\Exceptions\DuplicateFilterException;

class OMDBApi
{
    /**
     * @var \RikSomers\OMDB\OMDBClient
     */
    private $client;

    /**
     * OMDBApi constructor.
     *
     * @param \RikSomers\OMDB\OMDBClient $client
     */
    public function __construct(OMDBClient $client)
    {
        $this->client = $client;
    }

    /**
     * The filters for the search.
     *
     * @var array
     */
    protected $searchFilters = [];

    /**
     * @param  array                                               $parameters
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return $this
     */
    public function filterBy(array $parameters) : self
    {
        foreach ($parameters as $parameter => $value) {
            if (array_key_exists($parameter, $this->searchFilters)) {
                throw new DuplicateFilterException("The '${$parameter}' filter parameter has already been set.");
            }

            $this->searchFilters[$parameter] = $value;
        }

        return $this;
    }

    /**
     * @param  int                                                 $ttid
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return $this
     */
    public function ttid($ttid) : self
    {
        $this->filterBy(['i' => $ttid]);

        return $this;
    }

    /**
     * @param  string                                              $title
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return \RikSomers\OMDB\OMDBApi
     */
    public function title(string $title) : self
    {
        $this->filterBy(['t' => $title]);

        return $this;
    }

    /**
     * @param  string                                              $type
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return \RikSomers\OMDB\OMDBApi
     */
    public function type(string $type) : self
    {
        $this->filterBy(['type' => $type]);

        return $this;
    }

    /**
     * @param  int                                                 $year
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return \RikSomers\OMDB\OMDBApi
     */
    public function year(int $year) : self
    {
        $this->filterBy(['y' => $year]);

        return $this;
    }

    /**
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return \RikSomers\OMDB\OMDBApi
     */
    public function shortPlot() : self
    {
        $this->plot('short');

        return $this;
    }

    /**
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return \RikSomers\OMDB\OMDBApi
     */
    public function fullPlot() : self
    {
        $this->plot('full');

        return $this;
    }

    /**
     * @param  string                                              $type
     * @throws \RikSomers\OMDB\Exceptions\DuplicateFilterException
     * @return \RikSomers\OMDB\OMDBApi
     */
    public function plot(string $type) : self
    {
        $this->filterBy(['plot' => $type]);

        return $this;
    }

    /**
     * Get the first exact match for the given filters.
     *
     * @return \RikSomers\OMDB\OMDBApi
     * @throws \Exception
     */
    public function first()
    {
        if (in_array('s', array_keys($this->searchFilters))) {
            return $this->all()->first();
        }
        
        $result = $this->client->query($this->searchFilters);

        return $result->toEntity();
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function all()
    {
        $result = $this->client->query($this->searchFilters);

        if (in_array('s', array_keys($this->searchFilters)) && isset($result->getDecodedResponse()['totalResults'])) {
            return $result->toCollection();
        }

        return collect($result->toEntity());
    }
}
