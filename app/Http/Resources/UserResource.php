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

use App\Helpers\StringHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    public function __construct(
        User $user,
        private readonly ?object $history = null,
        private readonly int $seedingSize = 0,
        private readonly int $bonusUploaded = 0,
        private readonly int $uploadsCount = 0,
    ) {
        parent::__construct($user);
    }

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
     *     real_uploaded: string,
     *     real_downloaded: string,
     *     credited_uploaded: string,
     *     credited_downloaded: string,
     *     average_seedtime: string,
     *     seeding_size: string,
     *     fl_tokens: int,
     *     uploads_count: int,
     *     downloads_count: int,
     *     bonus_uploaded: string,
     * }
     */
    public function toArray(Request $request): array
    {
        $avgSeedtime = $this->history?->count > 0
            ? (int) (($this->history->seedtime_sum ?? 0) / $this->history->count)
            : 0;

        return [
            'username'           => $this->username,
            'group'              => $this->group->name,
            'uploaded'           => str_replace("\u{00A0}", ' ', $this->formatted_uploaded),
            'downloaded'         => str_replace("\u{00A0}", ' ', $this->formatted_downloaded),
            'ratio'              => $this->formatted_ratio,
            'buffer'             => str_replace("\u{00A0}", ' ', $this->formatted_buffer),
            'seeding'            => \count($this->seedingTorrents),
            'leeching'           => \count($this->leechingTorrents),
            'seedbonus'          => $this->seedbonus,
            'hit_and_runs'       => $this->hitandruns,
            'real_uploaded'      => str_replace("\u{00A0}", ' ', StringHelper::formatBytes((int) ($this->history?->upload_sum ?? 0), 2)),
            'real_downloaded'    => str_replace("\u{00A0}", ' ', StringHelper::formatBytes((int) ($this->history?->download_sum ?? 0), 2)),
            'credited_uploaded'  => str_replace("\u{00A0}", ' ', StringHelper::formatBytes((int) ($this->history?->credited_upload_sum ?? 0), 2)),
            'credited_downloaded' => str_replace("\u{00A0}", ' ', StringHelper::formatBytes((int) ($this->history?->credited_download_sum ?? 0), 2)),
            'average_seedtime'   => StringHelper::timeElapsed($avgSeedtime),
            'seeding_size'       => str_replace("\u{00A0}", ' ', StringHelper::formatBytes($this->seedingSize, 2)),
            'fl_tokens'          => $this->fl_tokens,
            'uploads_count'      => $this->uploadsCount,
            'downloads_count'    => (int) ($this->history?->download_count ?? 0),
            'bonus_uploaded'     => str_replace("\u{00A0}", ' ', StringHelper::formatBytes($this->bonusUploaded, 2)),
        ];
    }
}
