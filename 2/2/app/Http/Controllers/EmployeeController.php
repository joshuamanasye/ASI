<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    // create
    public function store(Request $request) {
        $request->validate([
            'nomor' => 'required',
            'nama' => 'required'
        ]);

        $photo_path = null;
        if ($request->hasFile('photo')) {
            $photo_path = Storage::disk('s3')->put('photos', $request->file('photo'), 'public');
        }

        $employee = Employee::create([
            'nomor' => $request->nomor,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'talahir' => $request->talahir,
            'photo_upload_path' => $photo_path,
            'created_on' => Carbon::now(),
            'created_by' => Auth::user()?->name ?? null
        ]);

        Redis::set("emp_{$employee->nomor}", $employee->toJson());

        return response()->json($employee);
    }

    //read
    public function show($id) {
        $employee = Employee::find($id);
        return response()->json($employee);
    }

    //update
    public function update(Request $request, $id) {
        $employee = Employee::find($id);

        $employee->fill($request->only([
            'nomor', 'nama', 'jabatan', 'talahir'
        ]));

        if ($request->hasFile('photo')) {
            $photo_path = Storage::disk('s3')->put('photos', $request->file('photo'), 'public');
            $employee->photo_upload_path = $photo_path;
        }

        $employee->updated_on = Carbon::now();
        $employee->updated_by = Auth::user()?->name ?? null;
        $employee->save();

        Redis::set("emp_{$employee->nomor}", $employee->toJson());

        return response()->json($employee);
    }

    //delete
    public function destroy($id) {
        $employee = Employee::find($id);
        $employee->deleted_on = Carbon::now();
        $employee->save();

        Redis::del("emp_{$employee->nomor}");

        return response()->json(['message' => 'Employee deleted']);
    }
}
