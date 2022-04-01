<?php


namespace Tests\Feature\Request;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Request\Traits\ActingAsRole;
use Tests\TestCase;

class RequestAssigningTest extends TestCase
{
    use DatabaseMigrations;
    use ActingAsRole;

    public function test_that_entry_point_available()
    {

    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_only_employee_can_assigned()
    {

    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_assigning_only_active_requests()
    {

    }
}
