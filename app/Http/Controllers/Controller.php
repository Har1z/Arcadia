<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Gives every controller $this->authorize() for policy checks
    // (ownership rules on maintenance logs and income records).
    use AuthorizesRequests;
}
