<?php

namespace App\Database\Types;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Custom type for medium integer.
 */
class MediumInteger extends IntegerType
{
    // Important: leave it as is, Laravel Schema will look for it by this name
    const NAME = 'mediuminteger';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // getMediumIntTypeDeclarationSQL() not defined
        // return $platform->getMediumIntTypeDeclarationSQL($fieldDeclaration);

        // _getCommonIntegerTypeDeclarationSQL() protected, so cant call it
        // return 'MEDIUMINT' . $platform->_getCommonIntegerTypeDeclarationSQL($fieldDeclaration);

        // call the generation of Integer type, prepend the missing word MEDIUM
        return 'MEDIUM' . $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is read from the database. Make your conversions here, optionally using the $platform.
        return $value === null ? null : (int) $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is written to the database. Make your conversions here, optionally using the $platform.
        return $value === null ? null : (int) $value;
    }

    public function getName()
    {
        return self::NAME;
    }
}
