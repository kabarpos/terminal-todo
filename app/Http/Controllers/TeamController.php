<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::with(['creator', 'users'])->get();
        
        return Inertia::render('Teams/Index', [
            'teams' => $teams
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Teams/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ]);

        $team = Team::create([
            ...$validated,
            'created_by' => Auth::id()
        ]);

        // Add creator as team leader
        $team->users()->attach(Auth::id(), ['role' => 'leader']);

        return Redirect::route('teams.index')->with('success', 'Tim berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['creator', 'users']);
        
        return Inertia::render('Teams/Show', [
            'team' => $team
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $team->load(['creator', 'users']);
        
        return Inertia::render('Teams/Edit', [
            'team' => $team
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ]);

        $team->update($validated);

        return Redirect::route('teams.show', $team)->with('success', 'Tim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return Redirect::route('teams.index')->with('success', 'Tim berhasil dihapus.');
    }

    /**
     * Get available users to add to team
     */
    public function getAvailableUsers(Team $team)
    {
        $users = User::whereNotIn('id', $team->users->pluck('id'))
            ->where('id', '!=', Auth::id()) // Exclude current user as they're already leader
            ->get(['id', 'name', 'email']);
        
        return response()->json($users);
    }

    /**
     * Add member to team
     */
    public function addMember(Request $request, Team $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,leader'
        ]);

        $team->users()->attach($validated['user_id'], [
            'role' => $validated['role']
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Remove member from team
     */
    public function removeMember(Request $request, Team $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Prevent removing the last leader
        if ($request->user()->id === $validated['user_id']) {
            $leaderCount = $team->users()->wherePivot('role', 'leader')->count();
            if ($leaderCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus ketua tim terakhir.');
            }
        }

        $team->users()->detach($validated['user_id']);

        return back()->with('success', 'Anggota berhasil dihapus dari tim.');
    }

    /**
     * Update member role
     */
    public function updateMemberRole(Request $request, Team $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,leader'
        ]);

        // Prevent demoting the last leader
        if ($validated['role'] === 'member') {
            $leaderCount = $team->users()->wherePivot('role', 'leader')->count();
            if ($leaderCount <= 1 && $team->users()->wherePivot('user_id', $validated['user_id'])->wherePivot('role', 'leader')->exists()) {
                return back()->with('error', 'Tidak dapat mengubah peran ketua tim terakhir.');
            }
        }

        $team->users()->updateExistingPivot($validated['user_id'], [
            'role' => $validated['role']
        ]);

        return back()->with('success', 'Peran anggota berhasil diperbarui.');
    }
}
