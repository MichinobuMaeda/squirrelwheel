<?php

namespace App\Repositories;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedRepository
{
    /**
     * List articles not posted with or without trashed.
     *
     * @param string  $url
     * @param DateTimeZone  $tz
     * @return array|false
     */
    public function atom(string $url, DateTimeZone $tz)
    {
        $response = Http::get($url);

        if ($response->status() != 200) {
            Log::warn('status: ' . $response->status());
            return false;
        }

        $feed = simplexml_load_string(
            $response->body(),
            null,
            LIBXML_NOCDATA,
            'atom',
            true,
        );

        $feed->registerXPATHNamespace('atom', 'http://www.w3.org/2005/Atom');
        $updated = new DateTime(
            $feed->xpath('/atom:feed/atom:updated/text()')[0],
        );
        $updated->setTimezone($tz);
        $entries = [];

        foreach ($feed->xpath('/atom:feed/atom:entry') as $i => $v) {
            $item = [
                'updated' => new DateTime(
                    $feed->xpath('/atom:feed/atom:entry/atom:updated/text()')[$i],
                ),
                'link' => $feed->xpath('/atom:feed/atom:entry/atom:link/@href')[$i],
                'title' => $feed->xpath('/atom:feed/atom:entry/atom:title/text()')[$i],
            ];
            $item['updated']->setTimezone($tz);
            $entries[] = $item;
        }

        return [
            'updated' => $updated,
            'entries' => $entries,
        ];
    }
}
