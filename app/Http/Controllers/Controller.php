<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SUCCESS = 'success';
    const ERROR = 'error';

    const ERR_INVALID_UNKNOWN = 'ERR_INVALID_UNKNOWN';
    const ERR_INVALID_USER_EMAIL = 'ERR_INVALID_USER_EMAIL';
    const ERR_INVALID_PASSWORD = 'ERR_INVALID_PASSWORD';
    const ERR_INVALID_USER = 'ERR_INVALID_USER';
}
