<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactShareControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_share_contact()
    {
        [$user1, $user2] = User::factory(2)->create();

        $contact = Contact::factory()->createOne([
            'phone_number' => '123456789',
            'user_id' => $user1->id,
        ]);


        $response = $this->actingAs($user1)->post(route('contact-shares.store'), [
            'contact_email' => $contact->email,
            'user_email' => $user2->email,
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseCount('contact_shares', 1);

        $sharedContacts = $user2->sharedContacts()->first();

        $this->assertTrue($contact->is($sharedContacts));
    }

    /**
     * @depends test_user_can_share_contact
     */
    public function test_user_can_see_shared_contact() {
        [$user1, $user2] = User::factory(2)->hasContacts(5)->create();

        $contact = $user1->contacts()->first();

        $contact->sharedWithUsers()->attach($user2->id);

        $response = $this->actingAs($user2)->get(route('contacts.show', $contact->id));

        $response->assertOk();
    }

    /**
     * @depends test_user_can_share_contact
     */
    public function test_user_cant_share_already_shared_contact() {
        [$user1, $user2] = User::factory(2)->hasContacts(5)->create();

        $contact = $user1->contacts()->first();

        $contact->sharedWithUsers()->attach($user2->id);

        $response = $this->actingAs($user1)->post(route('contact-shares.store'), [
            'contact_email' => $contact->email,
            'user_email' => $user2->email,
        ]);

        $response->assertSessionHasErrors(['contact_email']);

        $this->assertDatabaseCount('contact_shares', 1);
    }
}
