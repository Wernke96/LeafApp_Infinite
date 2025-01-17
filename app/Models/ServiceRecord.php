<?php

namespace App\Models;

use App\Models\Contracts\HasHaloDotApi;
use Database\Factories\ServiceRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

/**
 * @property int $id
 * @property int $player_id
 * @property float $kd
 * @property float $kda
 * @property int $total_score
 * @property int $total_matches
 * @property int $matches_won
 * @property int $matches_lost
 * @property int $matches_tied
 * @property int $matches_left
 * @property int $total_seconds_played
 * @property int $kills
 * @property int $deaths
 * @property int $assists
 * @property int $betrayals
 * @property int $suicides
 * @property int $vehicle_destroys
 * @property int $vehicle_hijacks
 * @property int $medal_count
 * @property int $damage_taken
 * @property int $damage_dealt
 * @property int $shots_fired
 * @property int $shots_landed
 * @property int $shots_missed
 * @property float $accuracy
 * @property int $kills_melee
 * @property int $kills_grenade
 * @property int $kills_headshot
 * @property int $kills_power
 * @property int $assists_emp
 * @property int $assists_driver
 * @property int $assists_callout
 * @property-read Player $player
 * @property-read float $win_percent
 * @property-read float $average_score
 * @property-read string $time_played
 * @property-read string $kd_color
 * @property-read string $kda_color
 * @property-read string $win_percent_color
 * @property-read string $accuracy_color
 * @method static ServiceRecordFactory factory(...$parameters)
 */
class ServiceRecord extends Model implements HasHaloDotApi
{
    use HasFactory;

    public $guarded = [
        'id'
    ];

    public $casts = [
        'total_matches' => 'int'
    ];

    public function getWinPercentAttribute(): float
    {
        if ($this->total_matches == 0) {
            return 100;
        }

        return ($this->matches_won / $this->total_matches) * 100;
    }

    public function getAverageScoreAttribute(): float
    {
        if ($this->total_matches == 0) {
            return $this->total_score;
        }

        return $this->total_score / $this->total_matches;
    }

    public function getTimePlayedAttribute(): int
    {
        return now()->addSeconds($this->total_seconds_played)->diffInHours();
    }

    public function getWinPercentColorAttribute(): string
    {
        switch (true) {
            case $this->win_percent > 50:
                return 'has-text-success';

            case $this->win_percent > 35 && $this->win_percent <= 50:
                return 'has-text-warning';

            default:
            case $this->win_percent < 35:
                return 'has-text-danger';
        }
    }

    public function getAccuracyColorAttribute(): string
    {
        switch (true) {
            case $this->accuracy > 55:
                return 'has-text-success';

            case $this->accuracy > 40 && $this->accuracy <= 55:
                return 'has-text-info';

            case $this->accuracy > 20 && $this->accuracy <= 40:
                return 'has-text-warning';

            default:
            case $this->accuracy < 20:
                return 'has-text-danger';
        }
    }

    public function getKdaColorAttribute(): string
    {
        switch (true) {
            case $this->kda >= 2:
                return 'has-text-success';

            case $this->kda > 1 && $this->kda < 2:
                return 'has-text-warning';

            default:
            case $this->kda < 1:
                return 'has-text-danger';
        }
    }

    public function getKdColorAttribute(): string
    {
        switch (true) {
            case $this->kd >= 1:
                return 'has-text-success';

            case $this->kd > 0.5 && $this->kd < 1:
                return 'has-text-warning';

            default:
            case $this->kd < 0.5:
                return 'has-text-danger';
        }
    }

    public static function fromHaloDotApi(array $payload): ?self
    {
        /** @var Player $player */
        $player = Arr::get($payload, 'player');

        /** @var ServiceRecord $serviceRecord */
        $serviceRecord = ServiceRecord::query()
            ->where('player_id', $player->id)
            ->firstOrNew();

        $serviceRecord->player()->associate($player);
        $serviceRecord->kd = Arr::get($payload, 'data.kdr');
        $serviceRecord->kda = Arr::get($payload, 'data.kda');
        $serviceRecord->total_score = Arr::get($payload, 'data.total_score');
        $serviceRecord->total_matches = Arr::get($payload, 'data.matches_played');
        $serviceRecord->matches_won = Arr::get($payload, 'data.breakdowns.matches.wins');
        $serviceRecord->matches_lost = Arr::get($payload, 'data.breakdowns.matches.losses');
        $serviceRecord->matches_tied = Arr::get($payload, 'data.breakdowns.matches.draws');
        $serviceRecord->matches_left = Arr::get($payload, 'data.breakdowns.matches.left');
        $serviceRecord->total_seconds_played = Arr::get($payload, 'data.time_played.seconds');
        $serviceRecord->kills = Arr::get($payload, 'data.summary.kills');
        $serviceRecord->deaths = Arr::get($payload, 'data.summary.deaths');
        $serviceRecord->assists = Arr::get($payload, 'data.summary.assists');
        $serviceRecord->betrayals = Arr::get($payload, 'data.summary.betrayals');
        $serviceRecord->suicides = Arr::get($payload, 'data.summary.suicides');
        $serviceRecord->vehicle_destroys = Arr::get($payload, 'data.summary.vehicles.destroys');
        $serviceRecord->vehicle_hijacks = Arr::get($payload, 'data.summary.vehicles.hijacks');
        $serviceRecord->medal_count = Arr::get($payload, 'data.summary.medals');
        $serviceRecord->damage_taken = Arr::get($payload, 'data.damage.taken');
        $serviceRecord->damage_dealt = Arr::get($payload, 'data.damage.dealt');
        $serviceRecord->shots_fired = Arr::get($payload, 'data.shots.fired');
        $serviceRecord->shots_landed = Arr::get($payload, 'data.shots.landed');
        $serviceRecord->shots_missed = Arr::get($payload, 'data.shots.missed');
        $serviceRecord->accuracy = Arr::get($payload, 'data.shots.accuracy');
        $serviceRecord->kills_melee = Arr::get($payload, 'data.breakdowns.kills.melee');
        $serviceRecord->kills_grenade = Arr::get($payload, 'data.breakdowns.kills.grenades');
        $serviceRecord->kills_headshot = Arr::get($payload, 'data.breakdowns.kills.headshots');
        $serviceRecord->kills_power = Arr::get($payload, 'data.breakdowns.kills.power_weapons');
        $serviceRecord->assists_emp = Arr::get($payload, 'data.breakdowns.assists.emp');
        $serviceRecord->assists_driver = Arr::get($payload, 'data.breakdowns.assists.driver');
        $serviceRecord->assists_callout = Arr::get($payload, 'data.breakdowns.assists.callouts');

        // If we get no time played or score. We are going to assume account is private.
        if ($serviceRecord->total_seconds_played === 0 && $serviceRecord->total_score === 0) {
            $serviceRecord->player->is_private = true;
            $serviceRecord->player->saveOrFail();
        } elseif ($serviceRecord->player->is_private) {
            $serviceRecord->player->is_private = false;
            $serviceRecord->player->saveOrFail();
        }

        if ($serviceRecord->isDirty()) {
            $serviceRecord->saveOrFail();
        }

        return $serviceRecord;
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
