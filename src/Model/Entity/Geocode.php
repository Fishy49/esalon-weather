<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Geocode extends Entity
{
    // This helper provides a convenient way to defind the logic for whether an ip address is geocoded
    public function isGeocoded()
    {
        return !is_null($this->lat) && !is_null($this->lng) && !$this->geocodeExpired();
    }

    // This is where we can defind any logic relating to an ip's geocoding expriation
    public function geocodeExpired()
    {
        // TODO: Move expiration time to environment/config variable
        return $this->updated->toUnixString() < strtotime('5 days ago');
    }

    // Checks presence and validity of any existing weather data
    public function hasWeatherData()
    {
        return !is_null($this->weather_data) && !$this->weatherDataExpired();
    }

    // Checksto make sure the weather data is fresh
    public function weatherDataExpired()
    {
        // TODO: Move expiration time to environment/config variable
        return is_null($this->weather_date) || $this->weather_date->toUnixString() < strtotime('30 minutes ago');
    }
}
