<?php

namespace App\Http\Controllers\Api;

use App\Models\Concert;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BuyController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'concert' => ['required','numeric'],
            'quantity' => ['required','numeric']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'data not match with our validation',
                'data' => $validator->errors()
            ], 422);
        }

        $validated = $validator->getData();

        $concert = Concert::where('id', $validated['concert'])->get()->first();

        if ($concert == null) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'data not found in our database'
            ], 404);
        }

        $date = now();

        for ($i=0; $i < $validated['quantity']; $i++) {
            Transaction::create([
                'concert' => $concert->id,
                'user' => auth('sanctum')->user()->id,
                'paid_at' => $date->format('Y-m-d'),
                'book_at' => $date->format('Y-m-d'),
                'created_at' => $date
            ]);
        }

        $transaction = Transaction::where('created_at', $date)->where('user', auth('sanctum')->user()->id)->get();

        if (count($transaction) > 0) {
            return response()->json([
                'code' => 202,
                'status' => 'success',
                'message' => 'Data Berhasil Dibuat',
                'data' => $transaction
            ], 202);
        }

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'Data Berhasil Dibuat',
            'data' => 'no data available'
        ], 202);
    }
}
