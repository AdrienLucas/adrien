<?php

namespace SensioLabs\JobBoardBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    private static $utc = null;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return;
        }
        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }
        $value->setTimeZone(self::$utc);

        return $value->format($platform->getDateTimeFormatString());
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return;
        }
        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }
        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
