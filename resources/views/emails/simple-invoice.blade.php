<x-mail::message>
# {{ $subject }}

Hello {{ $name }},

Thank you for choosing PrimeLand Hotel.

Please find your **{{ $invoiceType }}** attached as a PDF document for your records. This includes the full cost breakdown and payment details.

@if(isset($notes) && $notes)
## Notes
{{ $notes }}
@endif

<x-mail::button :url="route('customer.login')">
Login to Your Account
</x-mail::button>

If you have any questions, please feel free to contact us.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
