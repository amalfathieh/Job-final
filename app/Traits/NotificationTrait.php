<?php
namespace App\Traits;


trait NotificationTrait
  {
    use responseTrait;

//Route::patch('/fcm-token', [HomeController::class, 'updateToken'])->name('fcmToken');
//$fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

    function sendPushNotification($title, $body,$token){
        try {
        $SERVER_API_KEY = 'AAAAH63tbUw:APA91bEC2LGwyU9cb4K6C-UZEYkbRBJaLlAiXaNhtsQaQAXhQeeQ0lLBno-0EZY4jsOhXXBTfJ3DN_rLGQR30Cqgy0xOtm_C0mrqPSlF4HZZKtOQOKP81N7ybilaQcaedEgedVZ6bTWc';

        $data = [
        "registration_ids" => $token,

        "notification" => [
            "title" => $title,
            "body" => $body,
            "sound" => "default"
            ]
        ];
       // return $data;
        $dataString = json_encode($data);

        $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
        ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
            //curl_close($ch);

            return response()->json(['success' => true, 'response' => $response]);

        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), $ex->getCode());
        }

    }

}
