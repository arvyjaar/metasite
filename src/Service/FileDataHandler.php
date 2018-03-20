<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class FileDataHandler implements DataHandlerInterface
{
    /**
     * @param string $file
     * @param array $request
     */
    public function saveSubscription($file, $request)
    {
        $content = $this->getContent($file);
        $filesystem = new Filesystem();

        if ($content) {
            $newContent = [];
            foreach ($content as $item) {
                if ($item->email != $request['email']) {
                    array_push($newContent, $item);
                }
            }
            array_push($newContent, $request);

            $filesystem->dumpFile(__DIR__ . '/../Data/' . $file, json_encode($newContent));
        } else {
            $filesystem->dumpFile(__DIR__ . '/../Data/' . $file, json_encode([$request]));
        }
    }

    /**
     * @param string $file
     * @return array
     */
    public function getContent($file)
    {
        $content = file_get_contents(__DIR__ . '/../Data/' . $file);
        $content = json_decode($content);

        return $content;
    }

    /**
     * @param string $file
     * @param string $id
     */
    public function delete($file, $id)
    {
        $content = $this->getContent($file);
        $newContent = [];
        foreach ($content as $item) {
            if ($item->email != $id) {
                array_push($newContent, $item);
            }
        }
        $filesystem = new Filesystem();
        $filesystem->dumpFile(__DIR__ . '/../Data/' . $file, json_encode($newContent));
    }

    /**
     * @param string $file
     * @param string $id
     * @return array
     */
    public function getItem($file, $id)
    {
        $result = [];
        $content = $this->getContent($file);
        foreach ($content as $item) {
            if ($item->email == $id) {
                $result = $item;
                break;
            }
        }

        return $result;
    }
}
