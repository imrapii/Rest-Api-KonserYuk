<?php

namespace App\Http\Controllers\Api;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ConcertController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $concerts = Concert::get();

        if (count($concerts) > 0) {
            return response()->json([
                'code' => 202,
                'status' => 'Silahkan untuk memilih artis berikut',
                'data konser' => 'Data Konser Artis',
                'data' => $concerts
            ], 202);
        }

        return response()->json([
            'code' => 202,
            'Silahkan untuk memilih artis berikut' => 'success',
            'data konser' => 'Data Konser Artis',
            'data' => 'no data available'
        ], 202);
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
            'name' => ['required','string','max:255','unique:concerts,name'],
            'start_at' => ['required','string','date_format:d-m-Y'],
            'end_at' => ['required','string','date_format:d-m-Y']
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

        $validated['start_at'] = date('Y-m-d', strtotime($validated['start_at']));
        $validated['end_at'] = date('Y-m-d', strtotime($validated['end_at']));

        $concert = Concert::create($validated);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'Data Berhasil Dibuat',
            'data' => $concert
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $concert = Concert::find($id);

        if (!$concert) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'data not found in our database'
            ], 404);
        }

        return response()->json([
            'code' => 206,
            'status' => 'success',
            'message' => 'Data Berhasil Kami Terima',
            'data' => $concert
        ], 206);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable','string','max:255','unique:concerts,name'],
            'start_at' => ['nullable','string','date_format:d-m-Y'],
            'end_at' => ['nullable','string','date_format:d-m-Y']
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

        $validated['start_at'] = date('Y-m-d', strtotime($validated['start_at']));
        $validated['end_at'] = date('Y-m-d', strtotime($validated['end_at']));

        $concert = Concert::find($id);
        $concert->update($validated);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'Data Berhasil Diupdate',
            'data' => $concert
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Concert::find($id)->delete();

        $concerts = Concert::get();

        if (count($concerts) > 0) {
            return response()->json([
                'code' => 202,
                'status' => 'success',
                'message' => 'Data Berhasil Dihapus',
                'data' => $concerts
            ], 202);
        }

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'Data Berhasil Dihapus',
            'data' => 'no data available'
        ], 202);
    }
}
