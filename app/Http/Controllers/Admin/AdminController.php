<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BansResource;
use App\Http\Resources\LogsResource;
use App\Traits\responseTrait;
use App\Http\Resources\UserResource;
use App\Models\Apply;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Seeker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use function Clue\StreamFilter\fun;

class AdminController extends Controller
{
    use responseTrait;
    public function removeUser($id) {
        $user = User::where('id', $id)->first();
        if ($user) {
            if ($user->hasRole('owner')) {
                return $this->apiResponse(null, 'Are you serious? this account for admin', 403);
            }
            $user->delete();
            return $this->apiResponse(null, __('strings.user_removed_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function getUsers($type, Request $request) {
        if($type == 'AllUsers') {
            if ($request->startDate && $request->endDate) {
                $users = User::whereBetween('created_at', [$request->startDate, $request->endDate])->get();
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = User::max('created_at');
                $users = User::whereBetween('created_at', [$request->startDate, $dateMax])->get();
            }
            else {
                $users = User::all();
            }
            $users = $users->reject(function(User $user) {
                $roles = $user->roles_name;
                foreach ($roles as $value) {
                    if ($value === 'owner' || $value === 'employee') {
                        return true;
                    }
                }
                return $user->isBanned();
            });
            $result = UserResource::collection($users);
        }

        else if($type == 'JobSeekers') {
            if ($request->startDate && $request->endDate) {
                $seekers = User::Role('job_seeker')->whereBetween('created_at', [$request->startDate, $request->endDate])->get();
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = User::max('created_at');
                $seekers = User::Role('job_seeker')->whereBetween('created_at', [$request->startDate, $dateMax])->get();
            }
            else {
                $seekers = User::Role('job_seeker')->get();
            }

            $result = UserResource::collection($seekers);
        }
        else if($type == 'Companies') {
            if ($request->startDate && $request->endDate) {
                $companies = User::Role('company')->whereBetween('created_at', [$request->startDate, $request->endDate])->get();
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = User::max('created_at');
                $companies = User::Role('company')->whereBetween('created_at', [$request->startDate, $dateMax])->get();
            }
            else {
                $companies = User::Role('company')->get();
            }
            // $companies = $companies->reject(function (User $user){
            //     return !array_search('company', $user->roles_name);
            // });
            $result = UserResource::collection($companies);
        }
        else {
            return $this->apiResponse(null, 'Error user type', 400);
        }
        return $this->apiResponse($result , 'Success' , 200);
    }

    public function search($search){
        $users = User::where(function ($query) use ($search){
            $query->where('user_name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');

        })->orWhereHas('seeker', function ($query) use ($search) {
            $query->where('first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('last_name', 'LIKE', '%' . $search . '%');

        })->orWhereHas('company', function ($query) use ($search) {
            $query->where('company_name', 'LIKE', '%' . $search . '%');
        })->get();

        $users = $users->reject(function(User $user) {
            $roles = $user->roles_name;
            foreach ($roles as $value) {
                return $value === 'owner';
            }
        });

        if($users->isEmpty()){
            return $this->apiResponse(null,__('strings.not_found'),404);

        } else{
            $result = UserResource::collection($users);
        }
        return $this->apiResponse($result,'Found it',200);
    }

    public function banUser(Request $request, $id){
        $vaildate = Validator::make($request->all(), [
            'reason' => 'required',
            'type' => 'required'
        ]);
        if ($vaildate->fails()) {
            return $this->apiResponse(null, $vaildate->errors(), 400);
        }
        $user = User::find($id);
        $auth = User::find(Auth::user()->id);
        if ($user->isNotBanned()) {
            $comment = $request->reason;
            $type = $request->type;
            $expired_at = $request->expired_at;
            if ($type === 'forever') {
                $expired_at = null;
            } else if (!$expired_at) {
                return $this->apiResponse(null, 'Date is required', 400);
            }
            $user->tokens()->delete();
            $user->roles()->detach();
            $ban = $user->ban([
                'comment' => $comment,
                'expired_at' => $expired_at
            ]);
            if ($auth->hasRole('employee'))
                activity('User')->causedBy($auth)->event('block')->withProperties(['blocked_info' => $ban])->log('block user');
            return $this->apiResponse($ban, __('strings.banned_successfully'), 200);
        } else {
            return $this->apiResponse(null, __('strings.user_already_banned'), 403);
        }
    }

    public function unBanUser($id) {
        $user = User::find($id);
        $auth = User::find(Auth::user()->id);
        if ($user->isBanned()) {
            $user->syncRoles($user->roles_name);
            $user->unBan();
            if ($auth->hasRole('employee'))
                activity('User')->causedBy($auth)->event('unblock')->withProperties(['unblocked_info' => $user])->log('unblock user');
            return $this->apiResponse(null, __('strings.unbanned_successfully'), 200);
        } else {
            return $this->apiResponse(null, __('strings.user_already_not_banned'), 403);
        }
    }

    public function getBans($type, Request $request) {
        $bans = [];
        if ($type === 'all') {
            if ($request->startDate && $request->endDate) {
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $request->endDate])->get());
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = DB::table('bans')->max('created_at');
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $dateMax])->get());
            }
            else {
                $bans = BansResource::collection(DB::table('bans')->get());
            }
        } else if ($type === 'expired') {
            if ($request->startDate && $request->endDate) {
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $request->endDate])->where('deleted_at', '!=', null)->get());
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = DB::table('bans')->max('created_at');
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $dateMax])->where('deleted_at', '!=', null)->get());
            }
            else {
                $bans = BansResource::collection(DB::table('bans')->where('deleted_at', '!=', null)->get());
            }
        } else if ($type === 'active') {
            if ($request->startDate && $request->endDate) {
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $request->endDate])->where('deleted_at', null)->get());
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = DB::table('bans')->max('created_at');
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $dateMax])->where('deleted_at', null)->get());
            }
            else {
                $bans = BansResource::collection(DB::table('bans')->where('deleted_at', null)->get());
            }
        } else {
            return $this->apiResponse(null, 'Error type', 400);
        }
        return $this->apiResponse($bans, __('strings.all_users_banned'), 200);
    }


    public function barChartByDay($year, $month, $week) {
        $date = Carbon::create($year, $month);
        $date = $date->addWeeks($week);

        $startDate = Carbon::parse($date)->startOfWeek();
        $endDate = Carbon::parse($date)->endOfWeek();

        $dailyData = [];

        while ($startDate->lte($endDate)) {
            $dayStart = $startDate->copy()->startOfDay()->toDateTimeString();
            $dayEnd = $startDate->copy()->endOfDay()->toDateTimeString();

            $countPosts = Post::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $countApplies = Apply::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $countOpportunities = Opportunity::whereBetween('created_at', [$dayStart, $dayEnd])->count();

            $totalCount = $countPosts + $countApplies + $countOpportunities;

            $dayFormat = $startDate->copy()->startOfDay()->format('M-d');
            $dailyData[] = [
                'day' => $dayFormat,
                'countPosts' => $countPosts,
                'countApplies' => $countApplies,
                'countOpportunities' => $countOpportunities,
                'total' => $totalCount,
            ];

            $startDate->addDay();
        }

        return response()->json($dailyData);
    }

    public function barChartByWeek($year, $month) {
        $date = Carbon::create($year, $month);

        $startDate = Carbon::parse($date)->startOfMonth();
        $endDate = Carbon::parse($date)->endOfMonth();

        $weeklyData = [];

        while ($startDate->lte($endDate)) {
            $weekStart = $startDate->copy()->startOfWeek()->toDateTimeString();
            $weekEnd = $startDate->copy()->endOfWeek()->toDateTimeString();

            $countPosts = Post::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $countApplies = Apply::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $countOpportunities = Opportunity::whereBetween('created_at', [$weekStart, $weekEnd])->count();

            $totalCount = $countPosts + $countApplies + $countOpportunities;

            $weekFormat = $startDate->copy()->startOfWeek()->format('W');
            $weeklyData[] = [
                'week' => $weekFormat,
                'countPosts' => $countPosts,
                'countApplies' => $countApplies,
                'countOpportunities' => $countOpportunities,
                'total' => $totalCount,
            ];

            $startDate->addWeek();
        }

        return response()->json($weeklyData);
    }

    public function barChartByMonth($year) {
        $date = Carbon::create($year);

        $startDate = Carbon::parse($date)->startOfYear();
        $endDate = Carbon::parse($date)->endOfYear();
        $monthlyData = [];

        while ($startDate->lte($endDate)) {
            $monthStart = $startDate->copy()->startOfMonth()->toDateTimeString();
            $monthEnd = $startDate->copy()->endOfMonth()->toDateTimeString();

            $countPosts = Post::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $countApplies = Apply::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $countOpportunities = Opportunity::whereBetween('created_at', [$monthStart, $monthEnd])->count();

            $totalCount = $countPosts + $countApplies + $countOpportunities;

            $monthFormat = $startDate->copy()->startOfMonth()->format('M');
            $monthlyData[] = [
                'month' => $monthFormat,
                'countPosts' => $countPosts,
                'countApplies' => $countApplies,
                'countOpportunities' => $countOpportunities,
                'total' => $totalCount,
            ];

            $startDate->addMonth();
        }

        return response()->json($monthlyData);
    }

    public function barChartByYear() {
        $minDatePost = Post::min('created_at');
        $maxDatePost = Post::max('created_at');
        $minDateApply = Apply::min('created_at');
        $maxDateApply = Apply::max('created_at');
        $minDateOpportunity = Opportunity::min('created_at');
        $maxDateOpportunity = Opportunity::max('created_at');

        $minDate = min($minDatePost, $minDateApply, $minDateOpportunity);
        $maxDate = max($maxDatePost, $maxDateApply, $maxDateOpportunity);

        $startDate = Carbon::parse($minDate)->startOfYear();
        $endDate = Carbon::parse($maxDate)->endOfYear();

        $yearlyData = [];

        while ($startDate->lte($endDate)) {
            $yearStart = $startDate->copy()->startOfYear()->toDateTimeString();
            $yearEnd = $startDate->copy()->endOfYear()->toDateTimeString();

            $countPosts = Post::whereBetween('created_at', [$yearStart, $yearEnd])->count();
            $countApplies = Apply::whereBetween('created_at', [$yearStart, $yearEnd])->count();
            $countOpportunities = Opportunity::whereBetween('created_at', [$yearStart, $yearEnd])->count();

            $totalCount = $countPosts + $countApplies + $countOpportunities;

            $yearFormat = $startDate->copy()->startOfYear()->format('Y');
            $yearlyData[] = [
                'year' => $yearFormat,
                'countPosts' => $countPosts,
                'countApplies' => $countApplies,
                'countOpportunities' => $countOpportunities,
                'total' => $totalCount,
            ];

            $startDate->addYear();
        }

        return response()->json($yearlyData);
    }


    public function countUsers() {
        $users = User::count();
        $seekers = Seeker::count();
        $companies = Company::count();
        $employees = Employee::count();
        $counts = [
            'users' => $users,
            'seekers' => $seekers,
            'companies' => $companies,
            'employees' => $employees,
        ];
        return $this->apiResponse($counts, __('strings.count_users'), 200);
    }

    public function logs() {
        $logs = Activity::all();
        return LogsResource::collection($logs);
    }

    public function getLogsEmployees() {
        $employees = Employee::all()->pluck('user_id')->toArray();
        $logs = Activity::whereIn('causer_id', $employees)->get();
        return LogsResource::collection($logs);
    }

    public function lineChartByDay($year, $month, $week) {
        // $minDate = Activity::min('created_at');
        // $maxDate = Activity::max('created_at');
        $date = Carbon::create($year, $month);
        $date = $date->addWeeks($week);

        $startDate = Carbon::parse($date)->startOfWeek();
        $endDate = Carbon::parse($date)->endOfWeek();

        $dailyData = [];

        while ($startDate->lte($endDate)) {
            $dayStart = $startDate->copy()->startOfDay()->toDateTimeString();
            $dayEnd = $startDate->copy()->endOfDay()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $countAdd = Activity::whereBetween('created_at', [$dayStart, $dayEnd])->where('event', 'created')->count();
            $countDelete = Activity::whereBetween('created_at', [$dayStart, $dayEnd])->where('event', 'deleted')->count();

            $dayFormat = $startDate->copy()->startOfDay()->format('M-d');
            $dailyData[] = [
                'day' => $dayFormat,
                'count' => $count,
                'countAdd' => $countAdd,
                'countDelete' => $countDelete,
            ];

            $startDate->addDay();
        }
        return response()->json($dailyData);
    }

    public function lineChartByWeek($year, $month)
    {
        // $minDate = Activity::min('created_at');
        // $maxDate = Activity::max('created_at');

        $date = Carbon::create($year, $month);

        $startDate = Carbon::parse($date)->startOfMonth();
        $endDate = Carbon::parse($date)->endOfMonth();

        $weeklyData = [];

        while ($startDate->lte($endDate)) {
            $weekStart = $startDate->copy()->startOfWeek()->toDateTimeString();
            $weekEnd = $startDate->copy()->endOfWeek()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $countAdd = Activity::whereBetween('created_at', [$weekStart, $weekEnd])->where('event', 'created')->count();
            $countDelete = Activity::whereBetween('created_at', [$weekStart, $weekEnd])->where('event', 'deleted')->count();
            $weekNumber = $startDate->weekOfYear;
            $weeklyData[] = [
                'week' => $weekNumber,
                'count' => $count,
                'countAdd' => $countAdd,
                'countDelete' => $countDelete,
            ];

            $startDate->addWeek();
        }

        return response()->json($weeklyData);
    }
    public function lineChartByMonth($year)
    {

        $date = Carbon::create($year);
        $startDate = Carbon::parse($date)->startOfYear();
        $endDate = Carbon::parse($date)->endOfYear();

        $monthlyData = [];

        while ($startDate->lte($endDate)) {
            $monthStart = $startDate->copy()->startOfMonth()->toDateTimeString();
            $monthEnd = $startDate->copy()->endOfMonth()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $countAdd = Activity::whereBetween('created_at', [$monthStart, $monthEnd])->where('event', 'created')->count();
            $countDelete = Activity::whereBetween('created_at', [$monthStart, $monthEnd])->where('event', 'deleted')->count();
            $monthName = $startDate->copy()->format('F');
            $monthlyData[] = [
                'month' => $monthName,
                'count' => $count,
                'countAdd' => $countAdd,
                'countDelete' => $countDelete,
            ];

            $startDate->addMonth();
        }

        return response()->json($monthlyData);
    }

    public function lineChartByYear()
    {
        $minDate = Activity::min('created_at');
        $maxDate = Activity::max('created_at');

        $startDate = Carbon::parse($minDate)->startOfYear();
        $endDate = Carbon::parse($maxDate)->endOfYear();

        $yearlyData = [];

        while ($startDate->lte($endDate)) {
            $yearStart = $startDate->copy()->startOfYear()->toDateTimeString();
            $yearEnd = $startDate->copy()->endOfYear()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$yearStart, $yearEnd])->count();
            $countAdd = Activity::whereBetween('created_at', [$yearStart, $yearEnd])->where('event', 'created')->count();
            $countDelete = Activity::whereBetween('created_at', [$yearStart, $yearEnd])->where('event', 'deleted')->count();
            $yearFormat = $startDate->copy()->startOfYear()->format('Y');
            $yearlyData[] = [
                'year' => $yearFormat,
                'count' => $count,
                'countAdd' => $countAdd,
                'countDelete' => $countDelete,
            ];

            $startDate->addYear();
        }

        return response()->json($yearlyData);
    }

    public function totalCount() {
        $usersCount = User::count();
        $seekersCount = Seeker::count();
        $companiesCount = Company::count();
        $employeesCount = Employee::count();
        $postsCount = Post::count();
        $opportunitiesCount = Opportunity::count();
        $appliesCount = Apply::count();
        $activitesCount = Activity::count();
        $countAdd = Activity::where('event', 'created')->count();
        $countDelete = Activity::where('event', 'deleted')->count();
        $data = [
            'usersCount' => $usersCount,
            'seekersCount' => $seekersCount,
            'companiesCount' => $companiesCount,
            'employeesCount' => $employeesCount,
            'postsCount' => $postsCount,
            'opportunitiesCount' => $opportunitiesCount,
            'appliesCount' => $appliesCount,
            'activitesCount' => $activitesCount,
            'countAdd' => $countAdd,
            'countDelete' => $countDelete,
        ];
        return $this->apiResponse($data, 'Success', 200);
    }

    public function paginateList($items, $perPage = 10, $page = null, $options = []) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }
}
