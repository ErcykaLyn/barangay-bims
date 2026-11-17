{{--
    Gamit:
      <x-needs-action-badge :model="$resident" :url="route('admin.residents.edit', $resident->id)" />
      <x-needs-action-badge :model="$member" />   (walang link)
    Ang $model ay Resident o FamilyMember (may ReportsMissingFields trait).
--}}
@props(['model', 'url' => null])

@if ($model->needs_action)
    @php
        $tag = $url ? 'a' : 'span';
        $label = 'Needs action: ' . implode(', ', $model->missing_fields);
    @endphp
    <{{ $tag }}
        @if ($url) href="{{ $url }}" @endif
        title="{{ $url ? 'Click to edit and complete this record' : $label }}"
        style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:999px;
               background:#fef3c7;color:#b45309;font-size:11px;font-weight:600;text-decoration:none;white-space:nowrap;">
        ⚠ {{ $label }}
    </{{ $tag }}>
@endif