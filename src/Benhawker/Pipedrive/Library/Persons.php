<?php namespace Benhawker\Pipedrive\Library;

use Benhawker\Pipedrive\Exceptions\PipedriveMissingFieldError;

/**
 * Pipedrive Persons Methods
 *
 * Persons are your contacts, the customers you are doing Deals with.
 * Each Person can belong to an Organization.
 * Persons should not be confused with Users.
 *
 */
class Persons
{
    /**
     * Hold the pipedrive cURL session
     * @var Curl Object
     */
    protected $curl;

    /**
     * Hold the data of the response
     */
    private $response = array();

    /**
     * Initialise the object load master class
     */
    public function __construct(\Benhawker\Pipedrive\Pipedrive $master)
    {
        //associate curl class
        $this->curl = $master->curl();
    }

    /**
     * Returns a person
     *
     * @param  int   $id pipedrive persons id
     * @return array returns detials of a person
     */
    public function getById($id)
    {
        return $this->curl->get('persons/' . $id);
    }

    /**
     * Returns a person / people
     *
     * @param  string $name pipedrive persons name
     * @param  array  $data (org_id, start, limit, search_by_email)
     * @return array  returns detials of a person
     */
    public function getByName($name, array $data = array())
    {
        if (isset($data['pagination']) && $data['pagination'] == false) {
          return $this->getByNameNoPagination($name, $data);
        }

        $data['term'] = $name;

        return $this->curl->get('persons/find', $data);
    }

    /**
     * Returns a person / people without pagination
     *
     * @param  string $name pipedrive persons name
     * @param  array  $data (org_id, start, limit, search_by_email)
     * @return array  returns detials of a person
     */
    private function getByNameNoPagination($name, array $data = array())
    {
        $data['term'] = $name;

        $response = $this->curl->get('persons/find', $data);

        if ($response['success'] && $response['data']) {
            array_push($this->response, $response['data']);

            $pagination = $response['additional_data']['pagination'];

            if (!isset($data['limit']) && $pagination['more_items_in_collection']) {
                $data['start'] = $pagination['start'] + $pagination['limit'];
                return $this->getByNameNoPagination($name, $data);
            }
        }

        $output['data'] = count($this->response) ? $this->response[0] : $this->response;

        return $output;
    }

    /**
     * Returns all persons
     *
     * @param  array $data (filter_id, start, limit, sort_by, sort_mode)
     * @return array returns detials of all products
     */
    public function getAll(array $data = array())
    {
        if (isset($data['pagination']) && $data['pagination'] == false) {
          return $this->getAllNoPagination($data);
        }

        return $this->curl->get('persons/', $data);
    }

    /**
     * Returns all persons without pagination
     *
     * @param  array $data (filter_id, start, limit, sort_by, sort_mode)
     * @return array returns detials of all products
     */
    private function getAllNoPagination(array $data = array())
    {
        $response = $this->curl->get('persons/', $data);

        if ($response['success'] && $response['data']) {
            array_push($this->response, $response['data']);

            $pagination = $response['additional_data']['pagination'];

            if (!isset($data['limit']) && $pagination['more_items_in_collection']) {
                $data['start'] = $pagination['start'] + $pagination['limit'];
                return $this->getAllNoPagination($data);
            }
        }

        $output['data'] = count($this->response) ? $this->response[0] : $this->response;

        return $output;
    }

    /**
     * Lists deals associated with a person.
     *
     * @param  array $data (id, start, limit)
     * @return array deals
     */
    public function deals(array $data)
    {
        //if there is no name set throw error as it is a required field
        if (!isset($data['id'])) {
            throw new PipedriveMissingFieldError('You must include the "id" of the person when getting deals');
        }

        return $this->curl->get('persons/' . $data['id'] . '/deals');
    }

    /**
     * Updates a person
     *
     * @param  int   $personId pipedrives person Id
     * @param  array $data     new detials of person
     * @return array returns detials of a person
     */
    public function update($personId, array $data = array())
    {
        return $this->curl->put('persons/' . $personId, $data);
    }

    /**
     * Adds a person
     *
     * @param  array $data persons detials
     * @return array returns detials of a person
     */
    public function add(array $data)
    {
        //if there is no name set throw error as it is a required field
        if (!isset($data['name'])) {
            throw new PipedriveMissingFieldError('You must include a "name" field when inserting a person');
        }

        return $this->curl->post('persons', $data);
    }
}
