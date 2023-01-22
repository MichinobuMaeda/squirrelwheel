<?php

define('DATETIME_LOCAL', 'Y-m-d H:i:s');

/**
 * Get diff of datetimes with millisecond resolution.
 *
 * @param \DateTime  $op1
 * @param \DateTime  $op2
 * @return void
 */
function getMilliDiff($op1, $op2)
{
    return (int)$op1->format('Uv') - (int)$op2->format('Uv');
}
