<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
// use Stancl\Tenancy\Tenant;

use App\Notifications\UserApprovedNotification;
use Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager;



class UserApprovalController extends Controller
{
    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        return ResponseHelper::withSuccess('Pending users fetched.', $pendingUsers);
    }



    public function approveUser(Request $request, $id)
    {

        $user = User::findOrFail($id);


        if ($user->status === 'approved') {
            return ResponseHelper::withError('User already approved.');
        }

        // Update the user's approval status
        $user->update([
            'status' => 'approved',
            'approved_by' => auth('admin')->id(),
            'approved_at' => now(),
        ]);

        // Create tenant with path and user information
        $tenantId = Str::slug($user->name);
        $path = Str::slug($user->name);

        // Create the tenant

        $tenant = Tenant::create([
            'id' => $tenantId,
            'user_id' => $user->id,
            'path' => $path,
            'approved_by' =>  auth('admin')->id(),
            'approved_at' => now(),
        ]);



        // Initialize the tenant for multi-tenancy
        tenancy()->initialize($tenant);

        // Set the database connection for the manager and create the tenant's database
        $manager = app(MySQLDatabaseManager::class);
        $manager->setConnection('tenant');  // Set the tenant connection
        $manager->createDatabase($tenant);  // Create the database for the tenant

        // Send approval email (if needed)
        $user->notify(new UserApprovedNotification());

        // Return success response
        return ResponseHelper::withSuccess('User approved and tenant created.', [
            'user' => $user,
            'tenant_id' => $tenantId,
            'tenant_path' => $path,
        ]);
    }
}
