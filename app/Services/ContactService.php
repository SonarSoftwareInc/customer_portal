<?php

namespace App\Services;

use Carbon\Carbon;
use SonarSoftware\CustomerPortalFramework\Controllers\ContactController;
use SonarSoftware\CustomerPortalFramework\Exceptions\ApiException;
use SonarSoftware\CustomerPortalFramework\Models\Contact;
use Illuminate\Support\Facades\Cache;

class ContactService
{
  /**
   * Get info on the current user via the Sonar API.
   *
   * @throws ApiException
   */
  public function getContact(): Contact
  {
    if (! Cache::tags('profile.details')->has(get_user()->contact_id)) {
      $contactController = new ContactController();
      $contact = $contactController->getContact(get_user()->contact_id, get_user()->account_id);
      Cache::tags('profile.details')->put(get_user()->contact_id, $contact, Carbon::now()->addMinutes(10));
    }

    return Cache::tags('profile.details')->get(get_user()->contact_id);
  }
}
