<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStat;
use Illuminate\Http\Request;

class ApplicationStatController extends Controller
{
    public function incrementDownload()
    {
        $stat = ApplicationStat::first();
        if (!$stat) {
            $stat = ApplicationStat::create(['downloads' => 0]);
        }
        
        $stat->increment('downloads');
        
        return response()->json(['success' => true, 'downloads' => $stat->downloads]);
    }
}
