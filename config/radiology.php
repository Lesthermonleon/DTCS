<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RIS File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Maximum upload size per file in kilobytes (KB).
    | Default: 20480 KB (20 MB).
    |
    */
    'max_upload_size_kb' => env('RIS_MAX_UPLOAD_SIZE_KB', 20480),

    /*
    | Maximum number of files per upload batch.
    */
    'max_files_per_upload' => env('RIS_MAX_FILES_PER_UPLOAD', 10),

    /*
    | Supported imaging extensions.
    */
    'imaging_extensions' => ['dcm', 'dicom', 'jpeg', 'jpg', 'png', 'webp', 'gif'],

    /*
    | Supported supporting document extensions.
    */
    'document_extensions' => ['pdf'],

];
