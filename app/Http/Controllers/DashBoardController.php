<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Services\Auth\IAuthenticationService;
use App\Services\Campaign\ICampaignService;
use Carbon\Carbon;

class DashBoardController extends Controller
{
    protected $request;
    protected $authService;
    protected $campaignService;
    public function __construct(Request $request, IAuthenticationService $authService, ICampaignService $campaignService){
        $this->request = $request;
        $this->authService = $authService;
        $this->campaignService = $campaignService;
    }

    public function index() {
        try {
            $user = Auth::user();
            $users = $this->authService->getAllUserGroup2(null, false);

            // get timezone
            $timeZone = $this->campaignService->getTimeZone ();

            // currency year
            $now = Carbon::now();
            $year = $now->year;
            $month = $now->month;
            $day = $now->day;
            $totalDay = Carbon::now()->endOfMonth()->day;
            $dataMonth = config("constants.data_month");

            return view("admins.dashboard", [
                    'user' => $user,
                    'timezone' => $timeZone,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'totalDay' => $totalDay,
                    'dataMonth' => $dataMonth
            ]);
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
