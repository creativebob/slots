<?php

namespace App\Http\Controllers;

use App\Services\SlotService;

class HoldController extends Controller
{

    public function create(SlotService $slotService, $slotId)
    {
        $hold = $slotService->createHold($slotId);
        return response()->json($hold, 201);
    }

    public function confirm(SlotService $slotService, $id)
    {
        $slotService->confirmHold($id);
        return response()->noContent(204);
    }

    public function cancel(SlotService $slotService, $id)
    {
        $slotService->cancelHold($id);
        return response()->noContent(204);
    }
}
