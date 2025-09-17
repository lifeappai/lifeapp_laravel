<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

trait MediaUpload {

    /**
     * @param mixed  $file
     *
     * @return mixed
     */
    public function upload($media)
    {
        $mediaName        = $media->getClientOriginalName();
        $mediaPath        = Storage::put('media', $media);

        return Media::create(
            [
                'name'    => $mediaName,
                'path'    => $mediaPath,
            ]
        );
    }

    public function getBaseUrl()
    {
        return "https://lifeappmedia.blr1.digitaloceanspaces.com/";
    }
}
