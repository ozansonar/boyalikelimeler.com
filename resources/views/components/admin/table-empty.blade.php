@props([
    'colspan' => 5,
    'icon' => 'bi-inbox',
    'message' => 'Henüz kayıt bulunamadı.',
])

<tr>
    <td colspan="{{ $colspan }}" class="text-center py-5 text-clr-muted">
        <i class="bi {{ $icon }} fs-1 d-block mb-2 opacity-50"></i>
        {{ $message }}
    </td>
</tr>
