<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactShareController extends Controller
{
    public function create()
    {
        return view('contact-shares.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_email' => "exists:users,email|not_in:{$request->user()->email}",
            'contact_email' => Rule::exists('contacts', 'email')
                ->where('user_id', auth()->id()),
        ], [
            'user_email.not_in' => "You can't share a contact with yourself",
            'contact_email.exists' => 'This contact was not found in your contact list'
        ]);

        $user = User::where('email', $data['user_email'])->first(['id', 'email']);
        $contact = Contact::where('email', $data['contact_email'])->first(['id', 'email']);

        $shareExists = $contact->sharedWithUsers()->wherePivot('user_id', $user->id)->first();

        if ($shareExists) {
            return back()->withInput($request->all())->withErrors([
                'contact_email' => "This contact was already shared with user $user->email",
            ]);
        }

        $contact->sharedWithUsers()->attach($user->id);

        return redirect()->route('home')->with('alert', [
            'message' => "Contact $contact->email shared with $user->email successfully",
            'type' => 'success'
        ]);
    }
}
