<?php

namespace App\Service;

interface DataHandlerInterface
{
    /**
     * @param string|null $file
     * @param array $request
     * @return void
     * Writes to data source
     */
    public function saveSubscription($file, $request);

    /**
     * @param string|null $file
     * @return object
     */
    public function getContent($file);

    /**
     * @param string|null $file
     * @param string $id
     * @return void
     * Deletes selected record
     */
    public function delete($file, $id);

    /**
     * @param string|null $file
     * @param string $id
     * @return array
     * Gets selected item by unique id
     */
    public function getItem($file, $id);
}
