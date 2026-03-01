<x-mail::message>
# Thank You for Your Feedback

Hello {{ $feedback->guest_name }},

Thank you for sharing your experience with us at PrimeLand Hotel. We deeply value your feedback as it helps us provide the best possible service to our guests.

## Your Review Summary

**Rating:** {{ $feedback->rating }} / 5 Stars

@if($feedback->comment)
**Your Comments:**
"{{ $feedback->comment }}"
@endif

We have received your feedback and our management team will review it. If you've mentioned any specific areas for improvement, please know that we are committed to addressing them.

## Join Us Again

We would love to welcome you back to PrimeLand Hotel soon! Feel free to visit our dashboard to check for special offers and make future bookings.

<x-mail::button :url="config('app.url')">
Visit PrimeLand Hotel
</x-mail::button>

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
