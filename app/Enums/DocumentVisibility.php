<?php
namespace App\Enums;
enum DocumentVisibility: string { case MANAGER_ONLY='manager_only'; case CONSULTANT_AND_MANAGER='consultant_and_manager'; case PATIENT_VISIBLE='patient_visible'; }
