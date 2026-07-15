<?php

/*
|--------------------------------------------------------------------------
| Contact / Book-a-Demo configuration
|--------------------------------------------------------------------------
| The public company contact details are driven entirely by the environment
| so the client can change them without touching code. Never hardcode the
| company email address anywhere in the application — set CONTACT_EMAIL in
| .env instead.
*/

return [
    // Where Contact-Us / Book-a-Demo notifications are delivered.
    'email' => env('CONTACT_EMAIL'),

    // Public "for quick contact" phone number shown on the contact page.
    'phone' => env('CONTACT_PHONE'),
];
