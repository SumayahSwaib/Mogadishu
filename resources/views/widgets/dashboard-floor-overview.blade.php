@push('styles')
    <style>
        .dashboard-floor-card {
            border-radius: 1.5rem !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.09) !important;
            background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%) !important;
        }

        .dashboard-floor-card:hover {
            transform: translateY(-6px) scale(1.025);
            box-shadow: 0 12px 40px rgba(0, 123, 255, 0.13), 0 2px 8px rgba(0, 0, 0, 0.07);
            text-decoration: none;
        }

        .dashboard-floor-card .badge {
            border-radius: 1.2rem;
            padding: 0.55em 1.3em;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .badge-occupied {
            background: linear-gradient(90deg, #ff6a6a 0%, #ffb199 100%);
            color: #fff;
        }

        .badge-available {
            background: linear-gradient(90deg, var(--primary) 0%, #38f9d7 100%);
            color: #fff;
        }
    </style>
@endpush
<?php

$title = $title ?? 'Title';
$style = $style ?? 'success';
$number = $number ?? '0.00';
$sub_title = $sub_title ?? null;
$link = $link ?? 'javascript:;';

?>

<a href="{{ admin_url('rooms?floor%5B%5D=' . $floor) }}" class="card shadow-lg border-0 mb-4 mb-md-5 dashboard-floor-card"
    style="transition: transform 0.2s, box-shadow 0.2s; border-radius: 1.5rem; background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.09);">
    <div class="card-body py-4 px-4 d-flex flex-column justify-content-between" style="min-height: 240px;">
        <div class="d-flex align-items-center mb-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary) 60%, #6dd5fa 100%); box-shadow: 0 2px 12px rgba(0,123,255,0.13);">
                <i class="fas fa-building text-white" style="font-size: 2rem;"></i>
            </div>
            <div class="ml-4">
                <p class="h4 font-weight-bold mb-1" style="color: var(--primary); letter-spacing: 1px;">
                    {{ is_numeric($floor) ? 'Floor ' . $floor : $floor }}
                </p>
                @if ($sub_title)
                    <small class="text-muted" style="font-size: 1rem;">{{ $sub_title }}</small>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase font-weight-bold" style="font-size: 1rem; color: #6c757d;">
                    Occupied
                </span>
                <span class="badge badge-pill badge-occupied bg-danger">
                    {{ $rooms->where('status', 'Occupied')->count() }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-uppercase font-weight-bold" style="font-size: 1rem; color: #6c757d;">
                    Available
                </span>
                <span class="badge badge-pill badge-available bg-success">
                    {{ $rooms->where('status', 'Vacant')->count() }}
                </span>
            </div>
        </div>

        <div class="mt-auto">
            <hr class="my-2"
                style="background: linear-gradient(90deg, var(--primary) 0%, #6dd5fa 100%); height: 2.5px; border: none;">
            @php
                $roomNumbers = $rooms->pluck('name')->sort();
               
            @endphp
            <div class="d-flex align-items-center mt-2">
                <i class="fas fa-door-open mr-2" style="color: var(--primary); font-size: 1.3rem;"></i>
                <span class="h6 mb-0 font-weight-bold" style="color: var(--primary); letter-spacing: 1px;">
                    FROM {{ $range }}
                </span>
            </div>
        </div>
    </div>
</a>
