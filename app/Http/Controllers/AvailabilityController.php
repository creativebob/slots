<?php

namespace App\Http\Controllers;

use App\Services\SlotService;

class AvailabilityController extends Controller
{
    /**
     * Get the availability of slots
     *
     * @param SlotService $slotService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailability(SlotService $slotService)
    {
        return response()->json($slotService->getAvailability(), 200);
    }
}
