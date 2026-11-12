<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodBag;
use Illuminate\Http\Request;
class BloodBagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bloodBags = BloodBag::latest()->paginate(10);

        return response()->json([
            'message' => 'Blood bags retrieved successfully',
            'data' => $bloodBags
        ], 200);
    }

    /** * Store a new blood bag. */ 
    public function store(Request $request)
    { 
        $validated = $request->validate([
            'bag_number' => 'required|string|max:255|unique:blood_bags,bag_number',
            'blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'donor_name' => 'required|string|max:255',
            'collection_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:collection_date',
            'quantity' => 'required|integer|min:1',
            'status' => 'nullable|in:Available,Reserved,Dispatched,Expired',
        ]); 
        
        $bloodBag = BloodBag::create($validated);

        return response()->json([
             'message' => 'Blood bag created successfully', 'data' => $bloodBag, ], 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
            $bloodBag = BloodBag::find($id);

    if (!$bloodBag) {
        return response()->json([
            'message' => 'Blood bag not found'
        ], 404);
    }

    return response()->json([
        'message' => 'Blood bag retrieved successfully',
        'data' => $bloodBag
    ], 200);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $bloodBag = BloodBag::find($id);

        if (!$bloodBag) {
            return response()->json([
                'message' => 'Blood bag not found'
            ], 404);
        }

        $validated = $request->validate([
            'bag_number' => 'sometimes|required|string|max:255|unique:blood_bags,bag_number,' . $id,
            'blood_group' => 'sometimes|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'donor_name' => 'sometimes|required|string|max:255',
            'collection_date' => 'sometimes|required|date',
            'expiry_date' => 'sometimes|required|date|after:collection_date',
            'quantity' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:Available,Reserved,Dispatched,Expired',
        ]);

        $bloodBag->update($validated);

        return response()->json([
            'message' => 'Blood bag updated successfully',
            'data' => $bloodBag->fresh()
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        $bloodBag = BloodBag::find($id);

        if (!$bloodBag) {
            return response()->json([
                'message' => 'Blood bag not found'
            ], 404);
        }

        $bloodBag->delete();

        return response()->json([
            'message' => 'Blood bag deleted successfully'
        ], 200);
    }

    public function expiringWithin24Hours()
    {
        $now = now();

        $bloodBags = BloodBag::whereBetween('expiry_date', [
            $now,
            $now->copy()->addHours(24)
        ])
        ->orderBy('expiry_date')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Blood bags expiring within 24 hours',
            'total' => $bloodBags->count(),
            'data' => $bloodBags
        ]);
    }

    public function expiredBloodBags()
    {
        $bloodBags = BloodBag::where('expiry_date', '<', now())
            ->orderBy('expiry_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Already expired blood bags',
            'total' => $bloodBags->count(),
            'data' => $bloodBags
        ]);
    }

    public function nearRiskInventory()
    {
        $now = now();

        // Count available blood bags that have not expired
        $totalInventory = BloodBag::where('status', 'Available')
            ->where('expiry_date', '>=', $now)
            ->count();

        // Count available blood bags expiring within 24 hours
        $nearRiskBags = BloodBag::where('status', 'Available')
            ->whereBetween('expiry_date', [
                $now,
                $now->copy()->addHours(24)
            ])
            ->count();

        $riskPercentage = $totalInventory > 0
            ? ($nearRiskBags / $totalInventory) * 100
            : 0;

        return response()->json([
            'success' => true,
            'message' => 'Near-risk inventory analysis',
            'total_inventory' => $totalInventory,
            'near_risk_bags' => $nearRiskBags,
            'risk_percentage' => round($riskPercentage, 2)
        ]);
    }
}
