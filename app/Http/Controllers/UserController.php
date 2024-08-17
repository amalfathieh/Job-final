<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Jobs\ChangeEmailJob;
use App\Jobs\ForgotPasswordJob;
use App\Jobs\MailJob;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\ResetCodePassword;
use App\Models\Seeker;
use App\Models\User;
use App\Models\VerificationCode;
use App\Notifications\SendNotification;
use App\Traits\responseTrait;
use App\Traits\NotificationTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Ilovepdf\Exceptions\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;

use function Symfony\Component\HttpKernel\Log\format;

class UserController extends Controller
{
    use responseTrait,NotificationTrait;

    //REGISTER METHOD -POST
    public function register(RegisterRequest $request)
    {

        // Delete all old code that user send before.
        VerificationCode::where('email', $request->email)->delete();
        //Generate new code
        $data['email'] = $request->email;
        $data['code'] = mt_rand(100000, 999999);
        $codeData = VerificationCode::create($data);
        $user = User::query()->create([
            'user_name' => $request['user_name'],
            'email' => $request['email'],
            'password' => $request->password,
            'roles_name' => ['user' ,$request['roles_name']],
        ]);
        $user->syncRoles($user->roles_name);
        MailJob::dispatch($request->email, $data['code']);
        return $this->apiResponse([], __('strings.verification_code_sent') , 200);
    }

public function customToken(){
    try {
        $user = Auth::user(); // المستخدم الحالي
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $uid = (string) $user->id; // تحويل معرف المستخدم إلى سلسلة نصية

        // إنشاء حساب Firebase مخصص باستخدام ملف خدمة الحساب الصحيح
        $firebase = (new Factory)->withServiceAccount(storage_path('keys/firebase_service_account.json'));
        $auth = $firebase->createAuth();

        // إنشاء الـCustom Token
        $customToken = $auth->createCustomToken($uid);

        return  $customToken->toString();
    } catch (AuthException $e) {
        // حدوث خطأ في عملية المصادقة
        return response()->json(['error' => 'AuthException: ' . $e->getMessage()], 500);
    } catch (FirebaseException $e) {
        // حدوث خطأ في Firebase
        return response()->json(['error' => 'FirebaseException: ' . $e->getMessage()], 500);
    } catch (Exception $e) {
        // أي استثناء آخر
        return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
    }
}

public function verifyAccount(Request $request )
{
    $validator = Validator::make($request->all(), [
        'code' => ['required', 'string', 'exists:verification_codes'],
    ]);

    if ($validator->fails()) {
        return $this->apiResponse([], $validator->errors()->first(), 422);
    }

    $ver_code = VerificationCode::firstwhere('code', $request->code);
    // check if it does not expired: the time is one hour
    if ($ver_code->created_at->addHour() < now()) {
        VerificationCode::where('code', $ver_code->code)->delete();
        return $this->apiResponse([], __('code_has_expired') , 422);
    }
    // find user's email
    $user = User::where('email', $ver_code->email)->first();
    $token = $user->createToken("API TOKEN")->plainTextToken;
    $data = [];
    $data['user'] = $user;
    $data['token'] = $token;

    $customToken = $this->customToken();
    $data['firebase_custom_token'] = $customToken;

    $user->update(['is_verified' => true]);
    // $user->is_verified = true;
    VerificationCode::where('code', $ver_code->code)->delete();
    return $this->apiResponse($data, __('strings.created_successfully') , 200);
}

    public function login(LoginRequest $request)
    {
        $login = $request->input('login');
        $password =  $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
        $user = User::where($fieldType, $login)->first();
        if ($user) {
            if ($user->banned_at) {
                $ban_info = $user->bans->last();
                $reason = $ban_info->comment;
                $lang = $request->headers->get('accept-language');
                $expired_at = $ban_info->expired_at ? $ban_info->expired_at : ($lang == 'ar' ? 'الأبد' : 'ever');
                return $this->apiResponse([], __('strings.you\'re_blocked', ['expired_at' => $expired_at, 'reason' => $reason]) , 403);
            }
        }

        if (!Auth::attempt([$fieldType => $login, 'password' => $password])) {
            return $this->apiResponse([], __('strings.email_password_mismatch') , 401);
        }

        $user = new UserResource($user);
        if ($user->is_verified) {
            $token = $user->createToken("API TOKEN")->plainTextToken;
            $data['user'] = $user;
            $data['token'] = $token;

            $role = [];
            foreach ($user->roles_name as $ro) {
                $role[$ro] = Role::findByName($ro, 'web')->permissions->pluck('name');
            }

            $data['user']->roles_name = $role;

            $customToken = $this->customToken();
            $date['firebase_custom_token'] = $customToken;

            return $this->apiResponse($data, __('strings.user_logged_in_successfully') , 200);
        } else
            return $this->apiResponse(null, __('strings.account_not_verified') , 401);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();
        return $this->apiResponse([], __('strings.user_logged_out_successfully') , 200);
    }

    public function sendCodeVerification(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => ['required', 'email:rfc,dns']
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 400);
            }

            VerificationCode::where('email', $request->email)->delete();
            //Generate new code
            $data['email'] = $request->email;
            $data['code'] = mt_rand(100000, 999999);
            $codeData = VerificationCode::create($data);
            MailJob::dispatch($request->email, $data['code']);
            return $this->apiResponse([], __('strings.code_sent_email') , 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function checkPassword(Request $request) {
        $user = User::where('id', Auth::user()->id)->first();
        // return $request->password;
        return password_verify($request->password, $user->password)?
            $this->apiResponse(null, __('strings.password_correct'), 200):

            $this->apiResponse(null, __('strings.password_incorrect'), 401);
    }

    public function resetPassword(ResetPasswordRequest $request) {

        $user = User::where('id', Auth::user()->id)->first();
        $user->password = $request->password;
        if($user->hasRole('employee')){
            $employee = $user->employee;

            if (!$employee->is_change_password) {
                $employee->is_change_password = true;
                $employee->save();
            }
        }
        $data['token'] = $user->createToken("API TOKEN")->plainTextToken;
        $user->save();
        $data['user'] = new UserResource($user);

        return $this->apiResponse($data, __('strings.password_reset_success'), 200);
    }



    // Reset Password When user forgot his password

    public function sendCode(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email|exists:users',
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user->is_verified) {
                return $this->apiResponse(null, __('strings.account_not_verified_simple'), 400);
            }

            VerificationCode::where('email', $request->email)->delete();

            $data['email'] = $user->email;
            $data['code'] = mt_rand(100000, 999999);

            $codeData = VerificationCode::create($data);

            ForgotPasswordJob::dispatch($data['email'], $data['code']);

            return $this->apiResponse([], __('strings.code_sent_email'), 200);
        } catch (\Exception $ex) {
            return $this->apiResponse([], $ex->getMessage(), 500);
        }
    }

    public function checkCode(Request $request) {
        $validate = Validator::make($request->all(),[
            'code' => ['required', 'integer', 'exists:verification_codes'],
        ]);
        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }

        $ver_code = VerificationCode::firstwhere('code', $request->code);
        // check if it does not expired: the time is one hour
        if ($ver_code->created_at->addHour() < now()) {
            VerificationCode::where('code', $ver_code->code)->delete();
            return $this->apiResponse(null, __('strings.code_has_expired'), 422);
        }
        return $this->apiResponse(null, __('strings.code_is_correct'), 200);
    }

    public function rePassword(RePasswordRequest $request)
    {
        $passwordReset = VerificationCode::firstWhere('code', $request->code);

        if ($passwordReset->created_at->addHour() < now()) {
            $passwordReset->delete();
            return $this->apiResponse([], __('strings.code_has_expired'), 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $user['password'] = $request['password'];
        $user->update([
            'password' => $request->password,
        ]);
        $passwordReset->delete();
        $data['token'] = $user->createToken("API TOKEN")->plainTextToken;
        $data['user'] = new UserResource($user);
        $role = [];
            foreach ($user->roles_name as $ro) {
                $role[$ro] = Role::findByName($ro, 'web')->permissions->pluck('name');
            }
        $data['user']->roles_name = $role;
        return $this->apiResponse($data, __('strings.password_reset_success_again'), 200);
    }

    public function update(Request $request) {
        $user = User::where('id', Auth::user()->id)->first();
        if ($user->update($request->all())){
            return $this->apiResponse($user, __('strings.updated_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.something_went_wrong'), 500);
    }

    // Delete Account
    public function delete() {
        $user = User::where('id', Auth::user()->id)->first();

        if ($user->delete()) {
            $user->tokens()->delete();
            return $this->apiResponse(null, __('strings.account_deleted_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.something_went_wrong'), 500);
    }

    public function getMyInfo() {
        $user = User::where('id', Auth::user()->id)->first();
        return $this->apiResponse(new UserResource($user), __('strings.get_my_info_successfully'), 200);
    }

    public function storeToken(Request $request){
        $user = Auth::user();
        $exists = $user->deviceTokens()->where('token',$request->token)->exists();
        if(!$exists) {
            $user->deviceTokens()->create([
                'token' => $request->token,
            ]);
            return $this->apiResponse(null, 'success', 200);
        }
        return $this->apiResponse(null, 'success', 200);
    }

    public function noti(){
        return $this->sendPushNotification('test notification','this is new notificatino', ['fNtgp5QlTPGtB4xuCw7K-U:APA91bF0pi2GMfD3xIXHjMYSmhwPeFBdHGcsQ4_lYNmWafRYq_WCOmz_knTYbVxnhjoy8IMyJJUdYq08dCBi3df-ENhHcqV5j6tRB5u0qxHNRF9l7khkQAgkt6j8ULMd4lXAJS3IBFa3']);
        //        return $this->sendPushNotification('test notification','this is new notificatino', ['ftx7nfY2QGmh56bPg-rD8b:APA91bG4gPytWFpQN-MPqv97b5ZP-EKoToTPhpVfDioktBn8dlQkSVk05yX-HpOL9cmWEgprZRoMbgZ8FwUZULD5KXHRxx3yZrOLH19h_13OFipcklpxQCySzIREkNKv0zOiU4cYkQIm']);
    }

    public function search($search){
        $userId = auth()->id();
        $users = User::where(function ($query) use ($search){
            $query->where('user_name', 'LIKE', '%' . $search . '%');

        })->orWhereHas('seeker', function ($query) use ($search) {
            $query->where('first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('last_name', 'LIKE', '%' . $search . '%');

        })->orWhereHas('company', function ($query) use ($search) {
            $query->where('company_name', 'LIKE', '%' . $search . '%');
        })->get();
        $users = $users->reject(function(User $user) use ($userId) {
            return $user->id == $userId;
        });
        $users = $users->reject(function(User $user) {
            $roles = $user->roles_name;
            foreach ($roles as $value) {
                return $value === 'owner' || $value === 'employee';
            }
            return $user->isBanned();
        });

        if($users->isEmpty()){
            return $this->apiResponse(null,__('strings.not_found'),404);
        } else{
            $result = UserResource::collection($users);
        }
        return $this->apiResponse($result,__('strings.success'),200);
    }

    public function testStore(){
        try {
            $users=User::where('id','!=',auth()->user()->id)->get();
            $user2 = User::find(1);
            $data =[
                'obj_id'=>1,
                'title'=>'Login',
                'body'=>'to22 notification',
            ];
            Notification::send($user2,new SendNotification($data));
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "sent successfully", 200);
    }

    public function getUserProfile($user_id){
        $user =User::find($user_id);
        $data['profile'] = new UserResource($user);
        if ($user->hasRole('company')) {
            $opportunity = Opportunity::where('company_id', $user->company->id)->get();
            $data['opportunity']= OpportunityResource::collection($opportunity);
        }
        if ($user->hasRole('job_seeker')) {
            $posts = Post::where('seeker_id', $user->seeker->id)->get();
            $data['post']= PostResource::collection($posts);
        }
        return $this->apiResponse($data, __('strings.success'), 200);;
    }

    public function ee($date){
        $cacheKey = 'ee_' . $date;
        $cacheTime = now()->addHour();

        return cache()->remember($cacheKey, $cacheTime, function () use ($date) {
            if($date == 'day'){
                return $dailyUsers = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as users'))
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();
            }

            if($date == 'week'){
                return $weeklyUsers = User::select([
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('WEEK(created_at) as week'),
                    DB::raw('count(*) as users')])
                    ->groupBy('year', 'week')
                    ->orderBy('year', 'asc')
                    ->orderBy('week', 'asc')
                    ->get();
            }

            if($date == 'month'){
                return $monthlyUsers = User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as users'))
                    ->groupBy('month')
                    ->orderBy('month', 'ASC')
                    ->get();
            }
        });
    }

    public function checkToken() {
        return $this->apiResponse(null, 'Token is working', 200);
    }
}
