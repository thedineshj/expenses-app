<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BalanceRecord;
use Illuminate\Support\Facades\Validator;


class BalanceRecordController extends Controller
{

    public function listOfBalances(Request $request){

        $validator = Validator::make($request->all(), [
            "userId" => "required",
        ]);


        if ($validator->fails() ){
            return response()->json([
                'status' => false,
                'errors' => $validator->messages(),
                'message' => "Invalid data"
            ]);
        }


        $query = BalanceRecord::query();
        $query->where('lender_id', $request->input('userId') );
        $query->with(['borrower']);
        $result = $query->get();


        return response()->json([
            'status' => false,
            'errors' => [],
            'data' => $result ? $result->toArray() : [] ,
            'message' => "Balances of the user fetched successfully"
        ]);



    }

}
