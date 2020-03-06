<?php

return [
    'cacheStoreTime' => env('APP_CACHE_STORE_TIME', 5),  // in hours
    'fileSizeLimit'  => env('APP_FILE_SIZE_LIMIT', 5),   // in Mb
    'imageDimensionsLimit'  => env('APP_IMAGE_DIMENSIONS_LIMIT', 10000)   // in Pxls
];
