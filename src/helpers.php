<?php declare(strict_types=1);

use KboPublicSearch\KboPublicSearch;

if (! function_exists('kbo')) {
    function kbo(): KboPublicSearch
    {
        return app('kbo');
    }
}
