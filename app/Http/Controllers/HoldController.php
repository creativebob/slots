<?php

namespace App\Http\Controllers;

use App\Services\SlotService;

class HoldController extends Controller
{
    /**
     * Create a hold for a slot
     *
     * @param SlotService $slotService
     * @param int $slotId
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(SlotService $slotService, $slotId)
    {
        $hold = $slotService->createHold($slotId);
        return response()->json($hold, 201);
    }

    /**
     * Confirm a hold
     *
     * @param SlotService $slotService
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function confirm(SlotService $slotService, $id)
    {
        $slotService->confirmHold($id);
        return response()->noContent(204);
    }

    /**
     * Cancel a hold
     *
     * @param SlotService $slotService
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(SlotService $slotService, $id)
    {
        $slotService->cancelHold($id);
        return response()->noContent(204);
    }
}
