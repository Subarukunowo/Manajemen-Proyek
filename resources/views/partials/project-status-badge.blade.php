@php
$map = [
    'Draft'     => 'badge-grey',
    'Active'    => 'badge-blue',
    'Suspended' => 'badge-yellow',
    'Completed' => 'badge-green',
    'Cancelled' => 'badge-red',
];
@endphp
<span class="badge {{ $map[$status] ?? 'badge-grey' }}">{{ $status }}</span>
