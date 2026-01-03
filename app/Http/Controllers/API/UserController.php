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

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\BonTransactions;
use App\Models\History;

class UserController extends BaseController
{
    final public function show(): UserResource
    {
        $user = auth()->user();

        $user->loadCount(['torrents', 'seedingTorrents', 'leechingTorrents']);

        $user->history_stats = History::query()
            ->withTrashed()
            ->where('user_id', '=', $user->id)
            ->where('created_at', '>', $user->created_at)
            ->selectRaw('SUM(actual_uploaded) as upload_sum')
            ->selectRaw('SUM(uploaded) as credited_upload_sum')
            ->selectRaw('SUM(actual_downloaded) as download_sum')
            ->selectRaw('SUM(downloaded) as credited_download_sum')
            ->selectRaw('SUM(seedtime) as seedtime_sum')
            ->selectRaw('SUM(actual_downloaded > 0) as download_count')
            ->selectRaw('COUNT(*) as count')
            ->first();

        $user->seeding_size = $user->seedingTorrents()->sum('size');

        $user->bonus_uploaded = BonTransactions::query()
            ->where('sender_id', '=', $user->id)
            ->whereRelation('exchange', 'upload', '=', true)
            ->sum('cost');

        UserResource::withoutWrapping();

        return new UserResource($user);
    }
}
