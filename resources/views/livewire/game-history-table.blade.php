<?php
/** @var App\Models\Game[] $games */
?>
<div>
    <table class="table is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
            <tr>
                <th>Mode</th>
                <th>Map</th>
                <th>Gametype</th>
                <th>Outcome</th>
                <th>Kills</th>
                <th>Deaths</th>
                <th><abbr title="Kills / Deaths">KD</abbr></th>
                <th><abbr title="Kills + Assists / Deaths">KDA</abbr></th>
                <th><abbr title="Shots Hit / Shots Taken">Accuracy</abbr></th>
                <th><abbr title="Ordered by Points">Score</abbr></th>
                <th>Rank</th>
                <th>Occurred At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($games as $game)
                <tr>
                    <td>
                        {{ $game->experience->description }}
                        @if ($game->is_ranked)
                            <abbr title="Ranked"><i class="fa fa-crosshairs"></i></abbr>
                        @endif
                    </td>
                    <td>{{ $game->map->name }}</td>
                    <td>{{ $game->category->name }}</td>
                    <td class="{{ $game->personal->getVictoryColor() }}">
                        {{ $game->personal->outcome->description }}
                    </td>
                    <td>{{ $game->personal->kills }}</td>
                    <td>{{ $game->personal->deaths }}</td>
                    <td class="{{ $game->personal->getKdColor() }}">
                        {{ $game->personal->kd }}
                    </td>
                    <td class="{{ $game->personal->getKdaColor() }}">
                        {{ $game->personal->kda }}
                    </td>
                    <td>{{ $game->personal->accuracy }}</td>
                    <td>{{ $game->personal->score }}</td>
                    <td>{{ $game->personal->rank }}</td>
                    <td>{{ $game->occurred_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $games->links() }}
</div>
