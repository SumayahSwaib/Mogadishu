@php
    use App\Models\Utils;
@endphp
{{-- resources/views/admin/dashboard/house-room-grid.blade.php --}}
<style>
    .house-section {
        margin-bottom: 2.5rem;
    }

    .house-title {
        font-size: 1.35rem;
        margin-bottom: 1.2rem;
        color: #222;
        display: flex;
        align-items: center;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .house-title .fa-building-o {
        margin-right: 0.7rem;
        color: #1976d2;
        font-size: 1.3em;
    }

    .house-title small {
        margin-left: 0.7rem;
        font-size: 0.95em;
        color: #888;
    }

    .room-item {
        padding: 0.7rem;
    }

    .room-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f8e9 100%);
        border-radius: 0.75rem;
        padding: 1.2rem 1rem 1rem 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: box-shadow 0.18s, transform 0.15s;
        box-shadow: 0 2px 8px rgba(25, 118, 210, 0.07);
        border: 1.5px solid #e3e6ea;
        position: relative;
        overflow: hidden;
    }

    .room-card:hover {
        transform: translateY(-5px) scale(1.025);
        box-shadow: 0 8px 24px rgba(25, 118, 210, 0.13);
        border-color: #90caf9;
        z-index: 2;
    }

    .room-card.vacant {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border: 1.5px solid #a5d6a7;
    }

    .room-card.occupied {
        background: linear-gradient(135deg, #ffebee 0%, #fff3e0 100%);
        border: 1.5px solid #ef9a9a;
    }

    .room-badge {
        font-size: 0.8rem;
        padding: 0.25em 0.7em;
        border-radius: 0.35rem;
        display: inline-block;
        margin-bottom: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .room-badge.vacant {
        background-color: #c8e6c9;
        color: #256029;
        border: 1px solid #81c784;
    }

    .room-badge.occupied {
        background-color: #ffcdd2;
        color: #b71c1c;
        border: 1px solid #e57373;
    }

    .room-card h6 {
        font-size: 1.5rem;
        margin: 0.3rem 0 0.7rem 0;
        font-weight: 500;
        color: #222;
        letter-spacing: 0.01em;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .room-actions {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 1rem;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .room-actions li a {
        color: #1976d2;
        text-decoration: none;
        padding: 0.15em 0.5em;
        border-radius: 0.2em;
        transition: background 0.13s, color 0.13s;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.3em;
    }

    .room-actions li a:hover {
        background: #e3f2fd;
        color: #0d47a1;
        text-decoration: none;
    }

    /* Responsive tweaks */
    @media (max-width: 575.98px) {
        .room-item {
            padding: 0.4rem;
        }

        .room-card {
            padding: 0.7rem;
        }

        .house-title {
            font-size: 1.1rem;
        }
    }
</style>

<div class="container-fluid">
    @foreach ($houses as $house)
        <div class="row">
            @forelse($house->rooms as $room)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 room-item">
                    <div class="room-card {{ strtolower($room->status) }}">
                        <span class="room-badge {{ strtolower($room->status) }}">
                            {{ ucfirst($room->status) }}
                            @if ($room->status === 'Occupied' && $room->latest_renting)
                                until ({{ Utils::my_date_4($room->latest_renting->end_date) }})
                            @endif
                        </span>
                        <h6 title="{{ $room->name }}">
                            {{ $room->name }}
                        </h6>
                        <ul class="room-actions mb-0">

                            <li>
                                <a href="{{ admin_url('rentings?room_id=' . $room->id) }}">
                                    <i class="fa fa-file-text-o"></i> Invoices
                                </a>
                            </li>
                            <li>
                                @if ($room->status === 'Vacant')
                                    <a href="{{ admin_url('rentings/create?room_id=' . $room->id) }}">
                                        <i class="fa fa-plus-circle"></i> New Rent
                                    </a>
                                @else
                                    <a href="{{ admin_url('rentings/' . $room->latest_renting->id . '/edit') }}">
                                        <i class="fa fa-edit"></i> Update Rent
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    @endforeach
</div>
