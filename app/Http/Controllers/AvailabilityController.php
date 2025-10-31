<?php

namespace App\Http\Controllers;

use App\Services\SlotService;

class AvailabilityController extends Controller
{
    public function getAvailability(SlotService $slotService)
    {
        $availabilitySlots = $slotService->getAvailability();
        return response()->json($availabilitySlots, 200);
    }
}
