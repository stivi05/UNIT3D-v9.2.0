<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     username: string,
     *     group: string,
     *     uploaded: string,
     *     downloaded: string,
     *     ratio: string,
     *     buffer: string,
     *     seeding: int,
     *     leeching: int,
     *     seedbonus: string,
     *     hit_and_runs: int,
     *     real_uploaded: int,
     *     real_downloaded: int,
     *     credited_uploaded: int,
     *     credited_downloaded: int,
     *     average_seedtime: int,
     *     seeding_size: int,
     *     fl_tokens: int,
     *     uploads_count: int,
     *     downloads_count: int,
     *     bonus_uploaded: int,
     * }
     */
    public function toArray(Request $request): array
    {
        $historyStats = $this->history_stats;
        $avgSeedtime = $historyStats?->count > 0
            ? (int) (($historyStats->seedtime_sum ?? 0) / $historyStats->count)
            : 0;

        return [
            'username'            => $this->username,
            'group'               => $this->group->name,
            'uploaded'            => str_replace("\u{00A0}", ' ', $this->formatted_uploaded),
            'downloaded'          => str_replace("\u{00A0}", ' ', $this->formatted_downloaded),
            'ratio'               => $this->formatted_ratio,
            'buffer'              => str_replace("\u{00A0}", ' ', $this->formatted_buffer),
            'seeding'             => $this->seeding_torrents_count,
            'leeching'            => $this->leeching_torrents_count,
            'seedbonus'           => $this->seedbonus,
            'hit_and_runs'        => $this->hitandruns,
            'real_uploaded'       => (int) ($historyStats?->upload_sum ?? 0),
            'real_downloaded'     => (int) ($historyStats?->download_sum ?? 0),
            'credited_uploaded'   => (int) ($historyStats?->credited_upload_sum ?? 0),
            'credited_downloaded' => (int) ($historyStats?->credited_download_sum ?? 0),
            'average_seedtime'    => $avgSeedtime,
            'seeding_size'        => (int) $this->seeding_size,
            'fl_tokens'           => $this->fl_tokens,
            'uploads_count'       => $this->torrents_count,
            'downloads_count'     => (int) ($historyStats?->download_count ?? 0),
            'bonus_uploaded'      => (int) $this->bonus_uploaded,
        ];
    }
}
