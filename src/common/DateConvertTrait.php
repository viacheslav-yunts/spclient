<?php
namespace Sap\Odatalib\common;

/**
 * преобразование дат
 */
trait DateConvertTrait
{
    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public static function convertToDateAtom(\DateTime $dateTime)
    {
        return "datetime'" . date('Y-m-d\TH:i:s', $dateTime->getTimestamp()) . "'";
        //ci_var_dump(date(DATE_ATOM, $dateTime->getTimestamp()));
    }
}
