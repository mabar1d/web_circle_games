<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobNotifFirebaseModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.dashboard', []);
    }

    private function sendFcmNotif($to, $title, $body, $icon, $url)
    {
        $token = $to;
        $from = env("SERVER_FCM_KEY");
        $msg = array(
            'title' => $title,
            'body' => $body,
            'icon' => $icon,
            'image' => $icon,
            'picture' => $icon,
            'click_action' => $url
        );

        $fields = array(
            'to' => $token,
            'notification' => $msg
        );

        $headers = array(
            'Authorization: key=' . $from,
            'Content-Type: application/json'
        );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result) {
            return json_decode($result);
        } else return false;
    }

    public function testSendNotif()
    {
        try {
            $count = 0;
            $getListNotif = JobNotifFirebaseModel::getList(array(
                "status" => 0,
                "limit" => 1000
            ));
            foreach ($getListNotif as $rowListNotif) {
                $keyClient = $rowListNotif["client_key"];
                $titleFirebase = $rowListNotif["notif_title"];
                $bodyFirebase = $rowListNotif["notif_body"];
                $imgFirebase = $rowListNotif["notif_img_url"];
                $urlFirebase = $rowListNotif["notif_url"];
                $send = $this->sendFcmNotif($keyClient, $titleFirebase, $bodyFirebase, $imgFirebase, $urlFirebase);
                if ($send->success == 1) {
                    JobNotifFirebaseModel::find($rowListNotif["id"])->update([
                        "status" => 1
                    ]);
                    $count++;
                } else {
                    JobNotifFirebaseModel::find($rowListNotif["id"])->update([
                        "status" => 2
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
    }
}
