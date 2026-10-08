<?php
namespace App\Enums;
enum AppointmentStatus: string { case SCHEDULED='scheduled'; case CONFIRMED='confirmed'; case CHECKED_IN='checked_in'; case COMPLETED='completed'; case CANCELLED='cancelled'; case NO_SHOW='no_show'; }
