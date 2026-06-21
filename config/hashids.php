<?php

/**
 * for hashids package
 */

return [
    'salt'   => env('HASHIDS_SALT', ''),
    'length' => env('HASHIDS_LENGTH', 6),
];