<?php

define('DATETIME_LOCAL', 'Y-m-d H:i:s');

/**
 * Get diff of datetimes with millisecond resolution.
 *
 * @param \DateTime  $op1
 * @param \DateTime  $op2
 * @return int
 */
function getMilliDiff($op1, $op2)
{
    return (int)$op1->format('Uv') - (int)$op2->format('Uv');
}

/**
 * Get media name.
 *
 * @param string  $target
 * @return string
 */
function getMediaName($target)
{
    switch ($target) {
        case 'tw': return 'Twitter';
        case 'mstdn': return 'Mastodon';
        case 'tunblr': return 'Tunblr';
        default: return 'Unknown';
    }
}
