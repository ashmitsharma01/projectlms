<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchoolPortalPlannerController extends Controller
{
    public $data = [];
   
    public function dailyPlanner(Request $request)
    {
        return view('schoolPortal.daily_planner');
    }
   
    public function chapterDetails(Request $request)
    {
        return view('schoolPortal.chapter_details');
    }
   
}
