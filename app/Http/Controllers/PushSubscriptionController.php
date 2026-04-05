<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Update the user's push subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required'
        ]);

        $endpoint = $request->endpoint;
        $key = $request->keys['p256dh'];
        $token = $request->keys['auth'];
        $contentEncoding = $request->content_encoding ?? 'aesgcm';

        $user = Auth::user();
        $user->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully.'
        ]);
    }

    /**
     * Delete the user's push subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'endpoint' => 'required'
        ]);

        $user = Auth::user();
        $user->deletePushSubscription($request->endpoint);

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully.'
        ]);
    }
}
