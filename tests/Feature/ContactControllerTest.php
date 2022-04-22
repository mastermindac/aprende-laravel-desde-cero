<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_store_contacts()
    {
        $user = User::factory()->create();

        $contact = Contact::factory()->makeOne([
            'phone_number' => '123456789',
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('contacts.store'), $contact->getAttributes());
    
        $response->assertRedirect(route('home'));

        $this->assertDatabaseCount('contacts', 1);

        $this->assertDatabaseHas('contacts', [
            'user_id' => $user->id,
            'name' => $contact->name,
            'email' => $contact->email,
            'age' => $contact->age,
            'phone_number' => $contact->phone_number,
        ]);
    }

    public function test_store_contact_validation() {
        $user = User::factory()->create();

        $contact = Contact::factory()->makeOne([
            'phone_number' => "Wrong phone number",
            'email' => "Wrong email",
            'name' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('contacts.store'), $contact->getAttributes());
    
        $response->assertSessionHasErrors(['phone_number', 'email', 'name']);

        $this->assertDatabaseCount('contacts', 0);
    }

    /**
     * @depends test_user_can_store_contacts
     */
    public function test_only_owner_can_update_or_delete_contact() {
        [$owner, $notOwner] = User::factory(2)->create();

        $contact = Contact::factory()->createOne([
            'phone_number' => '123456789',
            'user_id' => $owner->id,
        ]);

        $response = $this
            ->actingAs($notOwner)
            ->put(route('contacts.update', $contact->id), $contact->getAttributes());

        $response->assertForbidden();

        $response = $this
            ->actingAs($notOwner)
            ->delete(route('contacts.destroy', $contact->id), $contact->getAttributes());

        $response->assertForbidden();
    }
}
