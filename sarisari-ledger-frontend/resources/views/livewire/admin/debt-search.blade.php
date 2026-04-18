<section class="card" wire:poll.5s>
    <h2>People With Debt</h2>
    <p class="muted">Search by name, code, or email. Updates automatically every 5 seconds.</p>

    <label for="search">Search</label>
    <input id="search" type="text" wire:model.live.debounce.300ms="search"
        placeholder="e.g. John, JOHN-1234, john@example.com">

    @if ($rows->isEmpty())
        <p class="muted">No matching customers with debt.</p>
    @else
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Email</th>
                    <th>Debt</th>
                </tr>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['code'] }}</td>
                        <td>{{ $row['email'] }}</td>
                        <td>{{ number_format($row['debt'], 0) }} PHP</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
</section>
