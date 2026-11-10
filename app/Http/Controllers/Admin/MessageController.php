<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    const PER_PAGE = 15;

    public function index(Request $request)
    {
        $messages = DB::table('contacts')
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->q . '%')
                        ->orWhere('email', 'like', '%' . $request->q . '%')
                        ->orWhere('subject', 'like', '%' . $request->q . '%');
                });
            })
            ->latest('created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function destroy(Request $request, $id)
    {
        DB::table('contacts')->where('id', $id)->delete();

        return back()->with('success', 'Message deleted.');
    }
}