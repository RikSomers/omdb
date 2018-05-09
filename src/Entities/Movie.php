<?php

namespace RikSomers\OMDB\Entities;

use Carbon\Carbon;
use RikSomers\OMDB\Entities\Traits\ParsesResults;

class Movie
{
    use ParsesResults;

    /**
     * Title of the movie
     *
     * @var string
     */
    protected $title;

    /**
     * Release year of the movie.
     *
     * @var integer
     */
    protected $year;

    /**
     * Current IMDB rating for the movie
     *
     * @var string
     */
    protected $rating;

    /**
     * The date at which the movie was released worldwide.
     *
     * @var \Carbon\Carbon
     */
    protected $releaseDate;

    /**
     * The runtime of the movie in minutes
     *
     * @var integer
     */
    protected $runtime;

    /**
     * The movie genres.
     *
     * @var array
     */
    protected $genres;

    /**
     * The movie directors.
     * @var array
     */
    protected $directors;

    /**
     * The movie writers.
     *
     * @var array
     */
    protected $writers;

    /**
     * The movie actors.
     *
     * @var array
     */
    protected $actors;

    /**
     * The plot of the movie
     *
     * @var string
     */
    protected $plot;

    /**
     * The main languages used in the movie.
     *
     * @var array
     */
    protected $languages;

    /**
     * The country from which the movie was produced
     *
     * @var string
     */
    protected $country;

    /**
     * A URL to the movie poster
     *
     * @var string
     */
    protected $poster;

    /**
     * The IMDB TTID for the movie.
     *
     * @var string
     */
    protected $ttid;

    /**
     * The raw resultset for the movie.
     *
     * @var array
     */
    private $resultSet;

    /**
     * Movie constructor.
     *
     * @param array $resultSet
     */
    public function __construct(array $resultSet)
    {
        $this->resultSet = $resultSet;

        $this->title = $resultSet['Title'];
        $this->year = $resultSet['Year'];
        $this->rating = $resultSet['Rated'];
        $this->releaseDate = $this->parseReleaseDate($resultSet['Released']);
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
     * @return mixed
     */
    public function getTtid(): string
    {
        return $this->ttid;
    }

    /**
     * @param string $released
     * @return \Carbon\Carbon
     */
    protected function parseReleaseDate(string $released)
    {
        if (strtolower($released) == 'n/a') {
            return null;
        }

        return new Carbon($released);
    }
}
