<?php

namespace App\Http\Controllers;

use App\Models\CardInformation;
use App\Models\NidInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NidController extends Controller
{
    //
    public function addNidInformation(Request $request): JsonResponse
    {
        $request->validate([
            'name'=>'string|required',
            'nid_number'=>'numeric|required',
            'father_name'=>'string|required',
            'mother_name'=>'string|required',
            'date_of_birth'=>'date|required'
        ]);

        $user = Auth::user();
        $oldNIDAccount = NidInformation::where('user_id', $user->id)->first();
        if ($oldNIDAccount) {
            return response()->json(['message' => 'NID information already exists.'], 409);
        }
//        create new nid information

        try {
            $nidInformation = new NidInformation();
            $nidInformation->user_id = $user->id;
            $nidInformation->name = $request->name;
            $nidInformation->nid_number = $request->nid_number;
            $nidInformation->father_name = $request->father_name;
            $nidInformation->mother_name = $request->mother_name;
            $nidInformation->date_of_birth = $request->date_of_birth;
            $nidInformation->save();
            return response()->json(['message' => 'NID information added.', 'nid' => $nidInformation], 201);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }

    public function editNidInformation(Request $request, $id):JsonResponse
    {
        $request->validate([
        'name'=>'string',
        'nid_number'=>'numeric',
        'father_name'=>'string',
        'mother_name'=>'string',
        'date_of_birth'=>'date'
    ]);

        $user = Auth::user();
        $nidAccount = NidInformation::where('id', $id)->where('user_id', $user->id)->first();
        if (!$nidAccount) {
            return response()->json(['message' => 'No nid information found.'], 404);
        }

        try {
            if ($request->has('name')) {
                $nidAccount->name = $request->name;
            }

            if ($request->has('nid_number')) {
                $nidAccount->nid_number = $request->nid_number;
            }

            if ($request->has('father_name')) {
                $nidAccount->father_name = $request->father_name;
            }

            if ($request->has('mother_name')) {
                $nidAccount->mother_name = $request->mother_name;
            }
            if ($request->has('date_of_birth')) {
                $nidAccount->date_of_birth = $request->date_of_birth;
            }

            $nidAccount->save();
            return response()->json(['message' => 'Updated successfully.', 'nid' => $nidAccount], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }
}
