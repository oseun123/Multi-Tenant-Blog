<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use Stancl\Tenancy\Database\Models\Tenant as TenancyTenant;

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

        // Update the user's status to approved
        $user->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Create a tenant
        $tenantId = 'tenant_' . Str::random(10); // Create a unique tenant ID
        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'path' => '/tenant/' . Str::slug($user->name),
            ],
        ]);

        return ResponseHelper::withSuccess('User approved and tenant created.', [
            'user' => $user,
            'tenant_id' => $tenantId,
            'tenant_data' => $tenant->data,  // Returning the tenant's data, including the path
        ]);
    }
}
