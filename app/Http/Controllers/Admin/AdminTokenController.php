<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingToken;
use Illuminate\Http\Request;

class AdminTokenController extends Controller
{
    const PER_PAGE = 15;

    public function index(Request $request)
    {
        $statuses = ['' => 'All statuses'] + BookingToken::STATUSES;

        $tokens = BookingToken::query()
            ->with('user')
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('token_ref', 'like', '%' . $request->q . '%')
                        ->orWhereHas('user', function ($userQuery) use ($request) {
                            $userQuery->where('name', 'like', '%' . $request->q . '%')
                                ->orWhere('email', 'like', '%' . $request->q . '%');
                        });
                });
            })
            ->latest('paid_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.tokens.index', compact('tokens', 'statuses'));
    }
}