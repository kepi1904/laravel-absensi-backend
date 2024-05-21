<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //index
    public function index(Request $request)
    {
        $attendances = Attendance::with('user')
            ->when($request->input('name'), function ($query, $name) {
                $query->whereHas('user', function ($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%');
                });
            })->orderBy('id', 'desc')->paginate(10);
        return view('pages.absensi.index', compact('attendances'));
    }


    //edit
    public function edit($id)
    {
        $attendance = Attendance::with('user')->find($id);

        if (!$attendance) {
            return redirect()->route('attendances.index')->with('error', 'Attendance not found');
        }

        return view('pages.absensi.edit', compact('attendance'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return redirect()->route('attendances.index')->with('error', 'Attendance not found');
        }

        // Validasi input
        $request->validate([
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'latlon_in' => 'nullable|string',
            'latlon_out' => 'nullable|string'
        ]);

        // Update attendance
        $attendance->date = $request->input('date');
        $attendance->time_in = $request->input('time_in');
        $attendance->time_out = $request->input('time_out');
        $attendance->latlon_in = $request->input('latlon_in');
        $attendance->latlon_out = $request->input('latlon_out');
        $attendance->save();

        return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully');
    }

    // Destroy
    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return redirect()->route('attendances.index')->with('error', 'Attendance not found');
        }

        $attendance->delete();

        return redirect()->route('attendances.index')->with('success', 'Attendance deleted successfully');
    }
}
