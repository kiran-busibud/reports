<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class TenancyController extends Controller
{
    public function changeTenancy(Request $request)
    {
        $tenant = Tenant::find('222');

        tenancy()->initialize($tenant);

        // $databaseName = DB::connection()->getDatabaseName();

        // echo $databaseName;

        // tenancy()->end();

        // echo $databaseName;

        // DB::table('users')->insert([]);



        // Tenancy::tenant(2, function () {
        //     DB::table('users')->insert([]);
        // });

        // $databaseName = DB::connection()->getDatabaseName();
        //     echo $databaseName;

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            echo "Tenant ID: " . $tenant->id . "<br>";
}
    }
}