<?php
declare(strict_types=1);

namespace App\Controller;

use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Geocoder\Query\GeocodeQuery;
use Cake\Http\Client;

/**
 * Geocodes Controller
 *
 * @method \App\Model\Entity\Geocode[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GeocodesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Grab the clients IP address from the request
        $client_ip = $this->request->clientIp();

        // Let's look in the DB to see if we've already geocoded this ip
        $geocodes = $this->getTableLocator()->get('Geocodes');
        $geocode = $geocodes->findByIpAddress($client_ip)->first();

        if (is_null($geocode)) {
          // Looks like this is a new IP address, we'll setup a new entity
          $geocode = $geocodes->newEntity(['ip_address' => $client_ip]);
        } else {
          // Score! We've seen this user's IP before, let's redirect them to the weather page
          return $this->redirect(['action' => 'view', $geocode->id]);
        }

        $this->set(compact('geocode'));
    }

    /**
     * View method
     *
     * @param string|null $id Geocode id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $geocodes = $this->getTableLocator()->get('Geocodes');
        $geocode = $geocodes->get($id);

        // To keep things simple, we'll check for (and maybe trigger) a Geocode when a user attempts to view the created record.
        // This might be better off in a standalone action.
        if (!$geocode->isGeocoded()) {
          // Perform the geocode query and save the results if any were retrieved
          // TODO: Check for and handle errors
          $http = new \Http\Adapter\Guzzle6\Client();
          $provider = new FreeGeoIp($http);
          $results = $provider->geocodeQuery(GeocodeQuery::create($geocode->ip_address));
          $result = $results->first();

          if(!is_null($result->getCoordinates())){
            $geocode->lat = $result->getCoordinates()->getLatitude();
            $geocode->lng = $result->getCoordinates()->getLongitude();

            $geocodes->save($geocode);
          }
        }

        // Likewise we'll go ahead and grab the data. This, again, might be better off in a separate action.
        if (!$geocode->hasWeatherData() && $geocode->isGeocoded()) {
          $location_url = sprintf("https://www.metaweather.com/api/location/search/?lattlong=%01.3f,%01.3f", $geocode->lat, $geocode->lng);
          
          // TODO: Check for and handle errors
          $http = new Client();
          $location_result = $http->get($location_url);
          $woeid = $location_result->getJson()[0]['woeid'];

          $weather_url = sprintf("https://www.metaweather.com/api/location/%d/", $woeid);

          $geocode->weather_data = json_encode($http->get($weather_url)->getJson());
          $geocode->weather_date = time();

          $geocodes->save($geocode);
        }

        $this->set(compact('geocode'));

        // Decode the weather data for convenince in the view
        $weather = json_decode($geocode->weather_data);
        $this->set(compact('weather'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $geocodes = $this->getTableLocator()->get('Geocodes');
        $geocode = $geocodes->findByIpAddress($this->request->getData()['ip_address'])->first();
        if(!is_null($geocode)){
          // This simply redirects a user if this IP already exists
          return $this->redirect(['action' => 'view', $geocode->id]);
        }

        $geocode = $geocodes->newEmptyEntity();
        if ($this->request->is('post')) {
            $geocode = $geocodes->patchEntity($geocode, $this->request->getData());
            if ($geocodes->save($geocode)) {
                $this->Flash->success(__('Fantastic Geocode!'));

                return $this->redirect(['action' => 'view', $geocode->id]);
            }
            $this->Flash->error(__('The geocode could not be saved. Please, try again.'));
        }
        $this->set(compact('geocode'));
    }
}
