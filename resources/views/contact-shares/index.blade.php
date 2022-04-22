@extends('layouts.app')

@section('content')
  <div class="container">
    <h1 class="text-center">Contacts shared with me</h1>
    @forelse ($contactsSharedWithUser as $contact)
      <div class="d-flex justify-content-between bg-dark mb-3 rounded px-4 py-2">
        <div>
          <a href="{{ route('contacts.show', $contact->id) }}">
            <img class="profile-picture"
              src="{{ Storage::url($contact->profile_picture) }}">
          </a>
        </div>

        <div class="d-flex align-items-center">
          <p class="me-2 mb-0">{{ $contact->name }}</p>
          <p class="me-2 mb-0 d-none d-md-block">
            <a href="mailto:{{ $contact->email }}">
              {{ $contact->email }}
            </a>
          </p>
          <p class="me-2 mb-0 d-none d-md-block">
            <a href="tel:{{ $contact->phone_number }}">
              {{ $contact->phone_number }}
            </a>
          </p>

          <p class="me-2 mb-0">Shared by
            <span class="text-info">{{ $contact->user->email }}</span>
          </p>
        </div>
      </div>
    @empty
      <div class="col-md-4 mx-auto">
        <div class="card card-body text-center">
          <p>No contacts where shared with you yet</p>
        </div>
      </div>
    @endforelse

    <h1 class="text-center">Contacts shared by me</h1>
    @forelse ($contactsSharedByUser as $contact)
      @foreach ($contact->sharedWithUsers as $user)
        <div
          class="d-flex justify-content-between bg-dark mb-3 rounded px-4 py-2">
          <div>
            <a href="{{ route('contacts.show', $contact->id) }}">
              <img class="profile-picture"
                src="{{ Storage::url($contact->profile_picture) }}">
            </a>
          </div>

          <div class="d-flex align-items-center">
            <p class="me-2 mb-0">{{ $contact->name }}</p>
            <p class="me-2 mb-0 d-none d-md-block">
              <a href="mailto:{{ $contact->email }}">
                {{ $contact->email }}
              </a>
            </p>
            <p class="me-2 mb-0 d-none d-md-block">
              <a href="tel:{{ $contact->phone_number }}">
                {{ $contact->phone_number }}
              </a>
            </p>

            <p class="me-2 mb-0">Shared with
              <span class="text-info">{{ $user->email }}</span>
            </p>

            <form action="{{ route('contact-shares.destroy', $user->pivot->id) }}"
              method="POST">
              @csrf
              @method("DELETE")
              <button type="submit" class="btn btn-danger mb-0 p-1 px-2">
                Unshare
              </button>
            </form>
          </div>
        </div>
      @endforeach
    @empty
      <div class="col-md-4 mx-auto">
        <div class="card card-body text-center">
          <p>You did not share any contacts yet</p>
          <p>Share contacts <a
              href="{{ route('contact-shares.create') }}">here</a>.</p>
        </div>
      </div>
    @endforelse
  </div>
@endsection
