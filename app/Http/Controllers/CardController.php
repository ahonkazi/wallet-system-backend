<?php

namespace App\Http\Controllers;

use App\Models\BankAcount;
use App\Models\CardInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    //
    public function addCardAccount(Request $request): JsonResponse
    {
        $request->validate([
            'card_number'=>'required|numeric',
            'exp_month'=>'required|numeric',
            'exp_year'=>'required|numeric',
            'cvv'=>'required|numeric',
            'card_holder_name'=>'required|string'
        ]);

        $user = Auth::user();
        $oldCardAccount = CardInformation::where('user_id', $user->id)->first();
        if ($oldCardAccount) {
            return response()->json(['message' => 'Card account already exists.'], 409);
        }
//        create new card account

        try {
            $cardAccount = new CardInformation();
            $cardAccount->user_id = $user->id;
            $cardAccount->card_number = $request->card_number;
            $cardAccount->exp_year = $request->exp_year;
            $cardAccount->exp_month = $request->exp_month;
            $cardAccount->cvv = $request->cvv;
            $cardAccount->card_holder_name = $request->card_holder_name;
            $cardAccount->save();
            return response()->json(['message' => 'Card account added.', 'card' => $cardAccount], 201);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }

    public function editCardAccount(Request $request, $id):JsonResponse
    {
        $request->validate([
            'card_number'=>'numeric',
            'exp_month'=>'numeric',
            'exp_year'=>'numeric',
            'cvv'=>'numeric',
            'card_holder_name'=>'string'
        ]);

        $user = Auth::user();
        $cardAccount = CardInformation::where('id', $id)->where('user_id', $user->id)->first();
        if (!$cardAccount) {
            return response()->json(['message' => 'No card account found.'], 404);
        }

        try {
            if ($request->has('card_number')) {
                $cardAccount->card_number = $request->card_number;
            }

            if ($request->has('exp_month')) {
                $cardAccount->exp_month = $request->exp_month;
            }

            if ($request->has('exp_year')) {
                $cardAccount->exp_year = $request->exp_year;
            }

            if ($request->has('cvv')) {
                $cardAccount->cvv = $request->cvv;
            }
            if ($request->has('card_holder_name')) {
                $cardAccount->card_holder_name = $request->card_holder_name;
            }

            $cardAccount->save();
            return response()->json(['message' => 'Updated successfully.', 'card' => $cardAccount], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }

    public function getCardInformation(Request $request){
        $user = Auth::user();
        return response()->json(['card' => $user->card_information], 200);

    }
}
