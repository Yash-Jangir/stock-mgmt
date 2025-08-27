<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $masters = [
            (object) [
                'title' => 'Color',
                'route' => route('admin.colors.index'),
            ],
            (object) [
                'title' => 'Size',
                'route' => route('admin.sizes.index'),
            ],
            (object) [
                'title' => 'Category',
                'route' => route('admin.categories.index'),
            ],
        ];
        return view('admin.masters', compact('masters'));
    }
}
