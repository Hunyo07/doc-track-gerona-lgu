<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignatureController extends Controller
{
    /**
     * List signatures with optional filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Signature::query()
            ->with([
                'user:id,name,department_id',
                'user.department:id,name',
                'document:id,document_number,title',
            ]);

        // Restrict visibility for non-admin users to their own signatures
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // Filters
        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('signature_type')) {
            $query->where('signature_type', $request->signature_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('signed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('signed_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_serial', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('document', function ($dq) use ($search) {
                      $dq->where('document_number', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int)($request->get('per_page', 15));
        $signatures = $query->orderBy('signed_at', 'desc')->paginate($perPage);

        return ApiResponse::paginated($signatures, 'Signatures retrieved successfully');
    }
}