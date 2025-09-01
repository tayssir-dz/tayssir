<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="header-block"
    style="padding:30px">
    <tr>
        <td align="center">
            @if (!config('app.env') === 'local')
                <!-- Development: Use a placeholder or base64 encoded image -->
                <div style="display:inline-block;padding:20px;font-size:18px;font-weight:700;">
                    {{ config('app.name') }}
                </div>
            @else
                <!-- Production: Use actual domain URL -->
                <img src="{{ asset('tayssir.png') }}" alt="{{ config('app.name') }}" width="114"
                    style="display:block;margin:0 auto;padding:30px;" />
            @endif
        </td>
    </tr>
</table>
