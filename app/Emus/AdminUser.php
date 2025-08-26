<?php

namespace App\Enums;

enum AdminUser: string
{
    case EMAIL = 'admin@cuban.com';
    case NAME = 'Super Admin';
    case PASSWORD = '12345678'; // sin hashear, lo vas a hashear en el seeder
}
