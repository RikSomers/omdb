<?php

namespace RikSomers\OMDB\Entities;

use Carbon\Carbon;
use RikSomers\OMDB\Entities\Traits\ParsesResults;

class Series
{
    use ParsesResults;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var integer
     */
    protected $year;

    /**
     * @var string
     */
    protected $rating;

    /**
     * @var \Carbon\Carbon
     */
    protected $releaseDate;

    /**
     * @var integer
     */
    protected $runtime;

    /**
     * @var array
     */
    protected $genres;

    /**
     * @var array
     */
    protected $directors;

    /**
     * @var array
     */
    protected $writers;

    /**
     * @var array
     */
    protected $actors;

    /**
     * @var string
     */
    protected $plot;

    /**
     * @var array
     */
    protected $languages;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $poster;

    /**
     * @var array
     */
    protected $resultSet;

    /**
     * @var int
     */
    protected $seasons;

    /**
     * @var string
     */
    protected $ttid;

    /**
     * Series constructor.
     *
     * @param array $resultSet
     */
    public function __construct(array $resultSet)
    {
        $this->resultSet = $resultSet;

        $this->title = $resultSet['Title'];
        $this->year = $resultSet['Year'];
        $this->rated = $resultSet['Rated'];
        $this->releaseDate = new Carbon($resultSet['Released']);
        $this->runtime = $this->parseRuntime($resultSet['Runtime']);
        $this->genres = $this->splitOnComma($resultSet['Genre']);
        $this->directors = $this->splitOnComma($resultSet['Director']);
        $this->writers = $this->splitOnComma($resultSet['Writer']);
        $this->actors = $this->splitOnComma($resultSet['Actors']);
        $this->plot = $resultSet['Plot'];
        $this->languages = $this->splitOnComma($resultSet['Language']);
        $this->country = $resultSet['Country'];
        $this->poster = $resultSet['Poster'];
        $this->ttid = $resultSet['imdbID'];
        $this->seasons = $resultSet['totalSeasons'];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return string
     */
    public function getRating(): string
    {
        return $this->rating;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getReleaseDate(): \Carbon\Carbon
    {
        return $this->releaseDate;
    }

    /**
     * @return int
     */
    public function getRuntime(): int
    {
        return $this->runtime;
    }

    /**
     * @return array
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @return array
     */
    public function getDirectors(): array
    {
        return $this->directors;
    }

    /**
     * @return array
     */
    public function getWriters(): array
    {
        return $this->writers;
    }

    /**
     * @return array
     */
    public function getActors(): array
    {
        return $this->actors;
    }

    /**
     * @return string
     */
    public function getPlot(): string
    {
        return $this->plot;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getPoster(): string
    {
        return $this->poster;
    }

    /**
     * @return int
     */
    public function getSeasons(): int
    {
        return $this->seasons;
    }

    /**
     * @return string
     */
    public function getTtid(): string
    {
        return $this->ttid;
    }
}
