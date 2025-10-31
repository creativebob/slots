<?php

namespace App\Services;

use App\Exceptions\SlotUnavailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Enums\HoldStatus;
use App\Exceptions\HoldConflict;

class SlotService {
    public function getAvailability ()
    {
        return Cache::lock('availability_slots_build', 3)->block(3, function () {
            return Cache::remember('availability_slots', 15, function () {

                info('Запрос в БД');
                return DB::table('slots')
                    ->where('remaining', '>', 0)
                    ->selectRaw('id as slot_id, capacity, remaining')
                    ->get();
            });
        });
    }

    public function createHold($slotId)
    {
        return Cache::lock("slot_remaining_{$slotId}", 5)->block(5, function () use ($slotId) {

            DB::beginTransaction();
            try {

                $slot = DB::table('slots')
                    ->find($slotId);

                if(!$slot) {
                    throw new SlotUnavailable('Slot not found');
                }

                $activeHoldsCount = DB::table('holds')
                    ->where('slot_id', $slot->id)
                    ->where('status', HoldStatus::HELD)
                    ->where('expires_at', '>', now())
                    ->count();

                if($activeHoldsCount >= $slot->remaining) {
                    throw new SlotUnavailable('Slot is full');
                }

                $holdId = DB::table('holds')
                    ->insertGetId([
                        'status' => HoldStatus::HELD,
                        'slot_id' => $slot->id,
                        'created_at' => now(),
                        'expires_at' => now()->addMinutes(5),
                    ]);

                DB::commit();
                return DB::table('holds')->find($holdId);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }

    public function confirmHold($id)
    {
        $hold = DB::table('holds')
            ->find($id);

        if(!$hold) {
            throw new HoldConflict('Hold not found');
        }

        $slotId = $hold->slot_id;

        Cache::lock("slot_remaining_{$slotId}", 5)->block(5, function () use ($id, $slotId) {

            DB::beginTransaction();

            try {
                $updatedCount = DB::table('holds')
                ->where('id', $id)
                ->where('status', HoldStatus::HELD)
                ->where('expires_at', '>', now())
                ->update([
                    'status' => HoldStatus::CONFIRMED,
                ]);

                if($updatedCount === 0) {
                    throw new HoldConflict('Hold not found');
                }

                $updatedSlot = DB::table('slots')
                    ->where('id', $slotId)
                    ->where('remaining', '>', 0)
                    ->decrement('remaining');

                if($updatedSlot === 0) {
                    throw new HoldConflict('Slot is full');
                }

                DB::commit();
                Cache::forget('availability_slots');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }


    public function cancelHold($id)
    {
        $hold = DB::table('holds')
            ->find($id);

        if(!$hold) {
            throw new HoldConflict('Hold not found');
        }

        $slotId = $hold->slot_id;

        Cache::lock("slot_remaining_{$slotId}", 5)->block(5, function () use ($id, $slotId) {

            DB::beginTransaction();

            try {
                $updatedCount = DB::table('holds')
                ->where('id', $id)
                ->where('status', HoldStatus::CONFIRMED)
                ->update([
                    'status' => HoldStatus::CANCELLED,
                ]);

                if($updatedCount === 0) {
                    throw new HoldConflict('Hold not found');
                }

                $updatedSlot = DB::table('slots')
                    ->where('id', $slotId)
                    ->whereColumn('remaining', '<', 'capacity')
                    ->increment('remaining');

                if($updatedSlot === 0) {
                    throw new HoldConflict('Slot is full');
                }

                DB::commit();
                Cache::forget('availability_slots');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }
}
