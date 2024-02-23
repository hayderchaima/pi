<?php

namespace App\Enum;
use ReflectionClass;

/**
 * Enum class UserRole
 */
class Role
{
    public const ADMIN = 'ADMIN';
    
    public const abonne = 'abonne';
    

    // Prevent instantiation of this class
    private function __construct() {}

    // Optional: Add a method to get all available roles
    public static function getAllRoles(): array
    {
        return [
            self::ADMIN,
            
            self::abonne,
            // Add more roles if needed
        ];
    }
    public static function toArray(): array
    {
        $reflectionClass = new ReflectionClass(static::class);
        return $reflectionClass->getConstants();
    }
}
