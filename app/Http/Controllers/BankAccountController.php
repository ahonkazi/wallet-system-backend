<?php

namespace App\Http\Controllers;

use App\Models\BankAcount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    //

    public function addBankAccount(Request $request): JsonResponse
    {
        $request->validate([
            'account_number' => 'required|numeric',
            'account_name' => 'required',
            'bank_ifsc' => 'required',
            'account_type' => 'required'
        ]);

        $user = Auth::user();
        $oldBankAccount = BankAcount::where('user_id', $user->id)->first();
        if ($oldBankAccount) {
            return response()->json(['message' => 'Bank account already exists.'], 409);
        }
//        create new bank account

        try {
            $bankAccount = new BankAcount();
            $bankAccount->user_id = $user->id;
            $bankAccount->account_number = $request->account_number;
            $bankAccount->bank_ifsc = $request->bank_ifsc;
            $bankAccount->account_type = $request->account_type;
            $bankAccount->account_name = $request->account_name;
            $bankAccount->save();
            return response()->json(['message' => 'Bank account added.', 'bank' => $bankAccount], 201);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }

    public function editBankAccount(Request $request, $id)//:JsonResponse
    {
        $request->validate([
            'account_number' => 'numeric',
            'account_name' => 'string',
            'bank_ifsc' => 'string',
            'account_type' => 'string'
        ]);

        $user = Auth::user();
        $bankAccount = BankAcount::where('id', $id)->where('user_id', $user->id)->first();
        if (!$bankAccount) {
            return response()->json(['message' => 'No bank account found.'], 404);
        }

        try {
            if ($request->has('account_number')) {
                $bankAccount->account_number = $request->account_number;
            }

            if ($request->has('account_name')) {
                $bankAccount->account_name = $request->account_name;
            }

            if ($request->has('bank_ifsc')) {
                $bankAccount->bank_ifsc = $request->bank_ifsc;
            }

            if ($request->has('account_type')) {
                $bankAccount->account_type = $request->account_type;
            }

            $bankAccount->save();
            return response()->json(['message' => 'Updated successfully.', 'bank' => $bankAccount], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }
}
