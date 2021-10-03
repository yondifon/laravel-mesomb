<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class)->in(__DIR__);

function withoutExceptionHandling()
{
    Config::set('mesomb.throw_exceptions', false);

    return test();
}
