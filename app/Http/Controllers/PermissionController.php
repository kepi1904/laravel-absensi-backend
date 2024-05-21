<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;





class PermissionController extends Controller
{
    //index
    public function index(Request $request)
    {
        $permissions = Permission::with('user')
            ->when($request->input('name'), function ($query, $name) {
                $query->whereHas('user', function ($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%');
                });
            })->orderBy('id', 'desc')->paginate(10);
        return view('pages.permission.index', compact('permissions'));
    }

    //view
    public function show($id)
    {
        $permission = Permission::with('user')->find($id);
        return view('pages.permission.show', compact('permission'));
    }

    //edit
    public function edit($id)
    {
        $permission = Permission::find($id);
        return view('pages.permission.edit', compact('permission'));
    }


    // Update
    // public function update(Request $request, $id)
    // {
    //     $permission = Permission::find($id);

    //     if (!$permission) {
    //         return redirect()->route('permissions.index')->with('error', 'Permission not found');
    //     }

    //     $permission->is_approved = $request->is_approved;
    //     $str = $request->is_approved == 1 ? 'Disetujui' : 'Ditolak';
    //     $permission->save();

    //     $this->sendNotificationToUser($permission->user_id, 'Status Izin anda adalah ' . $str);

    //     return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    // }

    //delete
    // Destroy
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return redirect()->route('permissions.index')->with('error', 'Permission not found');
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }
    // Update method
    public function update(Request $request, $id)
    {
        // Get user and their FCM token
        $user = User::find(11);
        $token = $user->fcm_token;
        $permission = Permission::find($id);
        $permission->is_approved = $request->is_approved;
        $str = $request->is_approved == 1 ? 'Disetujui' : 'Ditolak';
        $permission->save();
        $this->sendNotificationToUser($permission->user_id, 'Status Izin anda adalah ' . $str);
        return redirect()->route('permissions.index')->with('success', $token);
    }

    // Method to send notification to user
    public function sendNotificationToUser($userId, $message)
    {
        // Get user and their FCM token
        $user = User::find($userId);
        $token = $user->fcm_token;

        if ($token) {
            // Create the notification
            $notification = Notification::create('Status Izin', $message);

            // Create the CloudMessage instance
            $cloudMessage = CloudMessage::withTarget('token', 'Permission updated successfully')
                ->withNotification($notification);

            // Prepare the message data
            $messageData = [
                'to' => $token,
                'notification' => [
                    'title' => 'Status Izin',
                    'body' => $message
                ]
            ];

            // Create Guzzle client
            $client = new Client();

            try {
                // Send POST request to FCM endpoint
                $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                    'headers' => [
                        'Authorization' => 'key=AAAAvjtIFy8:APA91bEvjhZiOcmLm5tW7ouLPFJUlLk91rHzZALMr_8m3-hBCX9ZPkz8aRYDV9Ns8d7D33w3TfcLTkokt0_eSI8qZlHFiwQx2fgYQEk5xmd8ZFZ_1-HkIlQhlOeOVoFyTiab5L4a2gkN', // Replace with your server key
                        'Content-Type' => 'application/json',
                        'Content-Length' => strlen(json_encode($messageData)) // Manually set Content-Length
                    ],
                    'body' => json_encode($messageData)
                ]);

                if ($response->getStatusCode() == 200) {
                    Log::info('Notification sent successfully to user ID: ' . $userId);
                } else {
                    Log::error('Failed to send notification. Status code: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                // Log the error or handle it as necessary
                Log::error('Error sending FCM message: ' . $e->getMessage());
            }
        } else {
            // Handle the case where the user does not have a FCM token
            Log::warning('User does not have a FCM token.');
        }
    }
    // //update
    // public function update(Request $request, $id)
    // {
    //     $permission = Permission::find($id);
    //     $permission->is_approved = $request->is_approved;
    //     $str = $request->is_approved == 1 ? 'Disetujui' : 'Ditolak';
    //     $permission->save();
    //     $this->sendNotificationToUser($permission->user_id, 'Status Izin anda adalah ' . $str);
    //     return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    // }

    // public function sendNotificationToUser($userId, $message)
    // {
    //     // Dapatkan FCM token user dari tabel 'users'

    //     $user = User::find($userId);
    //     $token = $user->fcm_token;

    //     // Kirim notifikasi ke perangkat Android
    //     $messaging = app('firebase.messaging');
    //     $notification = Notification::create('Status Izin', $message);

    //     $message = CloudMessage::withTarget('token', $token)
    //         ->withNotification($notification);

    //     $messaging->send($message);
    // }

    // public function sendNotificationToUser($userId, $message)
    // {
    //     $user = User::find($userId);

    //     if ($user && !empty($user->fcm_token)) {
    //         $token = $user->fcm_token;

    //         $notification = [
    //             'title' => 'Status Izin',
    //             'body' => $message
    //         ];

    //         $message = [
    //             'token' => $token,
    //             'notification' => $notification
    //         ];

    //         try {
    //             $this->sendFirebaseNotification($message);
    //         } catch (\Exception $e) {
    //             Log::error('Failed to send FCM notification: ' . $e->getMessage());
    //         }
    //     } else {
    //         Log::warning('User with ID ' . $userId . ' does not have an FCM token.');
    //     }
    // }

    // private function sendFirebaseNotification(array $message)
    // {
    //     $client = new Client(); // Instance dari GuzzleHttp Client
    //     $url = 'https://fcm.googleapis.com/fcm/send';
    //     $jsonData = json_encode($message);
    //     $contentLength = strlen($jsonData);

    //     try {
    //         $response = $client->request('POST', $url, [
    //             'headers' => [
    //                 'Authorization' => 'key=AAAAvjtIFy8:APA91bEf4YMaIcDNJ4GHL3-3wN_knFEemA0UXpkrgMdy7qg0QKBCLh-lBKunKNNET5zyO_M-FyHous8ixAXYtUoLFEOird3I8XnWghrmxBSj635Db23x0CIA3dea7-l5oSwoiITbHjLx', // Ganti dengan server key Anda
    //                 'Content-Type' => 'application/json',
    //                 'Content-Length' => $contentLength,
    //             ],
    //             'body' => $jsonData
    //         ]);

    //         return $response->getBody()->getContents();
    //     } catch (RequestException $e) {
    //         Log::error('Failed to send request: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }
}


    // // Update
    // public function update(Request $request, $id)
    // {
    //     // Temukan permission berdasarkan ID
    //     $permission = Permission::find($id);

    //     if (!$permission) {
    //         return redirect()->route('permissions.index')->with('error', 'Permission not found');
    //     }

    //     // Update status approval
    //     $permission->is_approved = $request->is_approved;
    //     $str = $request->is_approved == 1 ? 'Disetujui' : 'Ditolak';
    //     $permission->save();

    //     // Kirim notifikasi ke user terkait
    //     $this->sendNotificationToUser($permission->user_id, 'Status Izin anda adalah ' . $str);

    //     // Redirect dengan pesan sukses
    //     return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    // }

    // // Fungsi untuk mengirim notifikasi ke user
    // public function sendNotificationToUser($userId, $message)
    // {
    //     // Dapatkan user berdasarkan ID
    //     $user = User::find($userId);

    //     // Cek apakah token FCM tersedia
    //     if ($user && !empty($user->fcm_token)) {
    //         $token = $user->fcm_token;

    //         // Kirim notifikasi ke perangkat Android
    //         $messaging = app('firebase.messaging');
    //         $notification = Notification::create('Status Izin', $message);

    //         $message = CloudMessage::withTarget('token', $token)
    //             ->withNotification($notification);

    //         try {
    //             $messaging->send($message);
    //         } catch (\Kreait\Firebase\Exception\MessagingException $e) {
    //             // \Log::error('Failed to send FCM notification: ' . $e->getMessage());
    //         }
    //     } else {
    //         // Log jika user tidak memiliki token FCM
    //         // \Log::warning('User with ID ' . $userId . ' does not have an FCM token.');
    //     }
    // }
