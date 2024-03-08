<?php

namespace App\Http\Controllers;

use App\Models\NidInformation;
use App\Models\PassportInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassportController extends Controller
{
    //
    public function addPassportInformation(Request $request): JsonResponse
    {
        $request->validate([
            'passport_number'=>'numeric|required',
            'exp_date'=>'date|required',
            'country'=>'string|required'
        ]);

        $user = Auth::user();
        $oldPassportInformation = PassportInformation::where('user_id', $user->id)->first();
        if ($oldPassportInformation) {
            return response()->json(['message' => 'Passport information already exists.'], 409);
        }
//        create new nid information

        try {
            $passportInformation = new PassportInformation();
            $passportInformation->user_id = $user->id;
            $passportInformation->passport_number = $request->passport_number;
            $passportInformation->exp_date = $request->exp_date;
            $passportInformation->country = $request->country;
            $passportInformation->save();
            return response()->json(['message' => 'Passport information added.', 'passport' => $passportInformation], 201);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.','error'=>$exception], 500);

        }

    }

    public function editPassportInformation(Request $request, $id):JsonResponse
    {
        $request->validate([
            'passport_number'=>'numeric',
            'exp_date'=>'date',
            'country'=>'string'
        ]);

        $user = Auth::user();
        $passportInformation = PassportInformation::where('id', $id)->where('user_id', $user->id)->first();
        if (!$passportInformation) {
            return response()->json(['message' => 'No passport information found.'], 404);
        }

        try {
            if ($request->has('passport_number')) {
                $passportInformation->passport_number = $request->passport_number;
            }

            if ($request->has('exp_date')) {
                $passportInformation->exp_date = $request->exp_date;
            }

            if ($request->has('country')) {
                $passportInformation->country = $request->country;
            }


            $passportInformation->save();
            return response()->json(['message' => 'Updated successfully.', 'passport' => $passportInformation], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }

    public function getPassportInformation(Request $request)
    {
           $user = Auth::user();
        return response()->json(['nid' => $user->passport_information], 200);    }
}
