<?php namespace Benhawker\Pipedrive\Library;

use Benhawker\Pipedrive\Exceptions\PipedriveMissingFieldError;

/**
 * Pipedrive Files Methods
 *
 * Files can be attached to Deals, Persons, Organizations, Products,
 * Activities and Notes. Files are usually displayed in the UI in a
 * chronological order – newest first – and in context with other
 * updates regarding the item they are attached to.
 *
 */
class Files
{
    /**
     * Hold the pipedrive cURL session
     * @var Curl Object
     */
    protected $curl;

    /**
     * Initialise the object load master class
     */
    public function __construct(\Benhawker\Pipedrive\Pipedrive $master)
    {
        //associate curl class
        $this->curl = $master->curl();
    }

    /**
     * Returns a file
     *
     * @param  int   $id pipedrive files id
     * @return array returns detials of a file
     */
    public function getById($id)
    {
        return $this->curl->get('files/' . $id);
    }

    /**
     * Adds a file
     *
     * @param  string $path absolute path of file to be added
     * @param  array $data file detials, deal_id, org_id, person_id, product_id, activity_id, note_id
     * @return array returns detials of the file
     */
    public function add(string $path, array $data = array())
    {
        // if there is not file path specified or file at that path doesn't exist
        // throw error
        if (!isset($data['file']) || !file_exists($data['file'])) {
            throw new PipedriveMissingFieldError('File should exist to be added');
        }

        $data['file'] = '@'.realpath($data['file']);

        return $this->curl->post('files', $data);
    }

}