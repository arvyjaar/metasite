<?php

namespace App\Jaar;

class DataFile
{
    /**
     * @param string $file
     * @param array $request
     */
    public function saveSubscription($file, $request)
    {
        $jsonObj = $this->read($file);

        if ($jsonObj) {
            $newJson = [];
            foreach ($jsonObj as $item) {
                if ($item->email != $request['email']) {
                    array_push($newJson, $item);
                }
            }
            array_push($newJson, [
                'email' => $request['email'],
                'name' => $request['name'],
                'categories' => $request['categories'],
                'updated_at' => date('Y-m-d H:m:s')
            ]);

            file_put_contents(__DIR__ . '/../Data/' . $file, json_encode($newJson));
        } else {
            file_put_contents(__DIR__ . '/../Data/' . $file, json_encode([
                [
                    'email' => $request['email'],
                    'name' => $request['name'],
                    'categories' => $request['categories'],
                    'updated_at' => date('Y-m-d H:m:s')
                ]]
            ));
        }
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $content = $this->read('categories.json');
        $categories = $content->categories;

        return $categories;
    }

    /**
     * @param string $file
     * @return object
     */
    private function read($file)
    {
        $content = file_get_contents(__DIR__ . '/../Data/' . $file);
        $jsonObj = json_decode($content);

        return $jsonObj;
    }
}