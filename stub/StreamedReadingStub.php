<?php

namespace League\Flysystem\Stub;

use League\Flysystem\Adapter\Polyfill\StreamedReadingTrait;

class StreamedReadingStub
{
    public function read($path)
    {
        if ($path === 'true.ext') {
            return array('contents' => $path);
        }

        return false;
    }

    /**
     * Get the contents of a file in a stream.
     *
     * @param string $path
     *
     * @return resource|false false when not found, or a resource
     */
    public function readStream($path)
    {
        if (! $data = $this->read($path)) {
            return false;
        }

        $stream = tmpfile();
        fwrite($stream, $data['contents']);
        rewind($stream);
        $data['stream'] = $stream;
        unset($data['contents']);

        return $data;
    }
}
