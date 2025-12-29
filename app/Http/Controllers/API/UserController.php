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
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    final public function show(): UserResource
    {
        $user = auth()->user();

        $history = DB::table('history')
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

        $seedingSize = $user->seedingTorrents()->sum('size');

        $bonusUploaded = BonTransactions::query()
            ->where('sender_id', '=', $user->id)
            ->where('name', 'like', '%Upload%')
            ->sum('cost');

        $uploadsCount = $user->torrents()->count();

        UserResource::withoutWrapping();

        return new UserResource($user, $history, (int) $seedingSize, (int) $bonusUploaded, $uploadsCount);
    }
}
