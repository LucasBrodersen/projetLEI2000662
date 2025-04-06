<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

/*
  $table->integer('customer_id');
    $table->float('price');
    $table->string('status');
    $table->dateTime('creation_date');
    $table->dateTime('paid_date')->nullable();
*/

class OrderFilter extends ApiFilter {
    protected $safeParms = [
        'customerId' => ['eq'],
        'price' => ['eq', 'gt', 'lt','lte', 'gte'],
        'status' => ['eq', 'ne'],
        'creationDate' => ['eq', 'gt', 'lt','lte', 'gte'],
        'paidDate' => ['eq', 'gt', 'lt','lte', 'gte', 'ne']
    ];

    protected $columnMap = [
        'customerId' => 'customer_id',
        'creationDate' => 'creation_date',
        'paidDate' => 'paid_date'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=', //Add In, Like here
        'ne' => '!='
    ];


}