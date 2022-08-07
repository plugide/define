<?php

namespace Plugide\Define\Support;

use Illuminate\Support\Facades\File;
use Plugide\Define\Plugin;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Assets
{
    /**
     * Serve the requested assets.
     *
     * @param Plugin $plugin
     * @param string $path
     * @return BinaryFileResponse
     */
    public function __invoke(Plugin $plugin, string $path)
    {
        $realPath = $plugin->path('public/'.$path);

        if (! realpath($realPath)) {
            abort(404);
        }

        $mime = MimeTypes::getMimeType(File::extension($realPath));

        $headers = [
            'Content-Type'  => $mime ?? 'text/plain',
            'Cache-Control' => 'public, max-age=31536000',
        ];

        return response()->file($realPath, $headers);
    }
}
