<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class FileDataHandler implements DataHandlerInterface
{
    /**
     * @param string $file
     * @param Subscriber $subscriber
     */
    public function saveSubscription($file, $subscriber)
    {
        $content = $this->getContent($file);
        $filesystem = new Filesystem();

        $newItem = [
            'email' => $subscriber->getEmail(), 
            'name' => $subscriber->getName(), 
            'categories' => $subscriber->getCategories(),
            'updated_at' => $subscriber->getUpdatedAt(),
        ];

        if ($content) {
            $newContent = [];
            foreach ($content as $item) {
                if ($item->email !== $subscriber->getEmail()) {
                    array_push($newContent, $item);
                }
            }
            array_push($newContent, $newItem);

            $filesystem->dumpFile(__DIR__ . '/../Data/' . $file, json_encode($newContent));
        } else {
            $filesystem->dumpFile(__DIR__ . '/../Data/' . $file, json_encode([$newItem]));
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
