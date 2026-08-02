<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSuggestion;
use App\Services\AiCopilotService;
use App\Services\DelinquencyService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index(Request $request, AiCopilotService $ai)
    {
        $facility = $request->user()->facility;
        abort_unless($facility && $facility->organization->hasEntitlement('ai_copilot'), 403);

        $suggestions = AiSuggestion::where('facility_id', $facility->id)->latest()->limit(20)->get();
        $briefing = $suggestions->firstWhere('type', 'briefing');

        return view('admin.ai.index', compact('facility', 'suggestions', 'briefing'));
    }

    public function ask(Request $request, AiCopilotService $ai)
    {
        $facility = $request->user()->facility;
        abort_unless($facility && $facility->organization->hasEntitlement('ai_copilot'), 403);
        $data = $request->validate(['question' => 'required|string|max:1000']);
        $ai->answer($facility, $data['question'], $request->user());

        return back()->with('success', 'AI answered using live facility data (read-only).');
    }

    public function briefing(Request $request, AiCopilotService $ai)
    {
        $facility = $request->user()->facility;
        $ai->dailyBriefing($facility, $request->user());

        return back()->with('success', 'Daily briefing generated.');
    }

    public function pricing(Request $request, AiCopilotService $ai)
    {
        $facility = $request->user()->facility;
        $ai->pricingSuggestions($facility, $request->user());

        return back()->with('success', 'Pricing suggestions ready — confirm before changing rates.');
    }

    public function runDelinquency(Request $request, DelinquencyService $delinquency)
    {
        $facility = $request->user()->facility;
        $summary = $delinquency->run($facility);

        return back()->with('success', 'Delinquency ladder processed: '.json_encode($summary));
    }

    public function updateSuggestion(Request $request, AiSuggestion $suggestion)
    {
        abort_unless($suggestion->facility_id === $request->user()->facility_id, 403);
        $data = $request->validate(['status' => 'required|in:accepted,dismissed']);
        $suggestion->update(['status' => $data['status'], 'acted_at' => now()]);

        return back()->with('success', 'Suggestion marked '.$data['status'].'.');
    }
}
