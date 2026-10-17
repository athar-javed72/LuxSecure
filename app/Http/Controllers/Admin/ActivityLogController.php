<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('activity_log.view'), 403);

        $query = Activity::query()->with('causer')->latest();

        if ($request->filled('search')) {
            $q = $request->string('search')->toString();
            $query->where(function ($qry) use ($q) {
                $qry->where('description', 'like', "%{$q}%")
                    ->orWhere('log_name', 'like', "%{$q}%")
                    ->orWhere('subject_type', 'like', "%{$q}%");
            });
        }

        return view('admin.activity.index', [
            'activities' => $query->paginate(40)->withQueryString(),
        ]);
    }
}
