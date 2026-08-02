<?php

namespace App\Services;

use App\Models\AiSuggestion;
use App\Models\Facility;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\UnitType;
use App\Models\User;
use App\Models\WaitingListEntry;

class AiCopilotService
{
    public function dailyBriefing(Facility $facility, ?User $user = null): AiSuggestion
    {
        $occupied = Unit::where('facility_id', $facility->id)->whereIn('status', ['rented', 'late', 'locked_out', 'lien', 'auction', 'moving_out'])->count();
        $total = Unit::where('facility_id', $facility->id)->where('status', '!=', 'unavailable')->count();
        $available = Unit::where('facility_id', $facility->id)->where('status', 'available')->count();
        $late = Rental::where('facility_id', $facility->id)->whereIn('status', ['late', 'locked_out', 'lien', 'auction'])->count();
        $waitlist = WaitingListEntry::where('facility_id', $facility->id)->where('status', 'waiting')->count();
        $tasks = $facility->tasks()->where('status', 'open')->count();
        $occPct = $total > 0 ? round(($occupied / $total) * 100, 1) : 0;

        $text = "Daily briefing for {$facility->name}\n\n"
            ."Occupancy: {$occupied}/{$total} units ({$occPct}%). {$available} available.\n"
            ."Delinquency: {$late} rentals in late/lockout/lien/auction.\n"
            ."Waitlist: {$waitlist} active leads.\n"
            ."Open tasks: {$tasks}.\n\n"
            ."Suggested focus: "
            .($late > 0 ? 'Review delinquencies and send approved reminders first. ' : 'No urgent delinquencies. ')
            .($waitlist > 0 ? 'Call top waitlist leads for types with new vacancies.' : 'Promote available climate units on the website.');

        return AiSuggestion::create([
            'facility_id' => $facility->id,
            'user_id' => $user?->id,
            'type' => 'briefing',
            'prompt' => 'daily briefing',
            'suggestion' => $text,
            'status' => 'pending',
            'meta' => compact('occupied', 'total', 'available', 'late', 'waitlist', 'tasks', 'occPct'),
        ]);
    }

    public function answer(Facility $facility, string $question, ?User $user = null): AiSuggestion
    {
        $q = strtolower($question);
        $answer = $this->ruleAnswer($facility, $q);

        return AiSuggestion::create([
            'facility_id' => $facility->id,
            'user_id' => $user?->id,
            'type' => 'chat',
            'prompt' => $question,
            'suggestion' => $answer,
            'status' => 'pending',
            'meta' => ['read_only' => true],
        ]);
    }

    public function pricingSuggestions(Facility $facility, ?User $user = null): AiSuggestion
    {
        $lines = [];
        $types = UnitType::withCount([
            'units as available_units_count' => fn ($q) => $q->where('status', 'available'),
            'units as total_units_count',
        ])->where('facility_id', $facility->id)->get();

        foreach ($types as $type) {
            $occ = $type->total_units_count > 0
                ? 1 - ($type->available_units_count / $type->total_units_count)
                : 0;
            $wait = WaitingListEntry::where('unit_type_id', $type->id)->where('status', 'waiting')->count();

            if ($occ >= 0.9 || $wait >= 2) {
                $suggested = round((float) $type->monthly_rate * 1.05, 0);
                $lines[] = "{$type->name}: high demand (occ ".round($occ * 100)."%, waitlist {$wait}). Consider \${$suggested}/mo (from \${$type->monthly_rate}).";
            } elseif ($occ <= 0.5 && $type->available_units_count > 0) {
                $suggested = max(1, round((float) $type->monthly_rate * 0.95, 0));
                $lines[] = "{$type->name}: soft demand (occ ".round($occ * 100)."%). Consider promo or \${$suggested}/mo.";
            }
        }

        if (! $lines) {
            $lines[] = 'Rates look balanced vs occupancy/waitlist. No change recommended today.';
        }

        $text = "Pricing assist for {$facility->name} (approval required before changes):\n\n".implode("\n", $lines);

        return AiSuggestion::create([
            'facility_id' => $facility->id,
            'user_id' => $user?->id,
            'type' => 'pricing',
            'prompt' => 'pricing recommendations',
            'suggestion' => $text,
            'status' => 'pending',
            'meta' => ['requires_confirmation' => true],
        ]);
    }

    protected function ruleAnswer(Facility $facility, string $q): string
    {
        if (str_contains($q, 'late') || str_contains($q, 'delinquen')) {
            $count = Rental::where('facility_id', $facility->id)->whereIn('status', ['late', 'locked_out', 'lien', 'auction'])->count();
            $names = Rental::with('customer', 'unit')
                ->where('facility_id', $facility->id)
                ->whereIn('status', ['late', 'locked_out', 'lien', 'auction'])
                ->limit(8)
                ->get()
                ->map(fn ($r) => $r->customer->fullName().' (Unit '.$r->unit->unit_number.', '.$r->status.', '.$r->daysPastDue().' days)')
                ->implode('; ');

            return "There are {$count} delinquent rentals. ".($names ?: 'None listed.').' Reminder drafts require your confirmation before send.';
        }

        if (str_contains($q, 'occup')) {
            $occupied = Unit::where('facility_id', $facility->id)->whereIn('status', ['rented', 'late', 'locked_out', 'lien', 'auction', 'moving_out'])->count();
            $total = Unit::where('facility_id', $facility->id)->where('status', '!=', 'unavailable')->count();
            $byType = UnitType::where('facility_id', $facility->id)->get()->map(function ($t) {
                $occ = $t->units()->whereIn('status', ['rented', 'late', 'locked_out', 'lien', 'auction', 'moving_out'])->count();
                $tot = $t->units()->count();

                return "{$t->name}: {$occ}/{$tot}";
            })->implode('; ');

            return "Facility occupancy {$occupied}/{$total}. By type: {$byType}.";
        }

        if (str_contains($q, 'available') || str_contains($q, 'vacant')) {
            $units = Unit::with('unitType')->where('facility_id', $facility->id)->where('status', 'available')->limit(15)->get();
            $list = $units->map(fn ($u) => $u->unit_number.' ('.$u->unitType->name.', $'.$u->effectiveRate().')')->implode('; ');

            return $list ? "Available units: {$list}." : 'No units currently available. Direct prospects to the waitlist.';
        }

        if (str_contains($q, 'wait')) {
            $count = WaitingListEntry::where('facility_id', $facility->id)->where('status', 'waiting')->count();

            return "There are {$count} people on the waiting list. Prioritize types with new vacancies.";
        }

        return 'I can answer occupancy, delinquencies, available units, waitlist, and pricing questions using live facility data. Ask something like “Who is late?” or “What’s my occupancy by unit type?” AI never changes data without your confirmation.';
    }
}
