<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class UserFilter extends ApiFilter {
    protected $safeParms = [
        'name' => ['eq', 'like'],
        'type' => ['eq', 'ne', 'like'],
        'email' => ['eq', 'like'],
        'address' => ['eq', 'like'],
        'city' => ['eq', 'like'],
        'state' => ['eq', 'like'],
        'postalCode' => ['eq', 'gt', 'lt', 'like']
    ];

    protected $columnMap = [
        'postalCode' => 'postal_code'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=', //Add In, Like here
        'ne' => '!=',
        'like' => 'like' 
    ];


}