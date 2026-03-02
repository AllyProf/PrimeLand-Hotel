<x-mail::message>
# {{ (isset($generalNotes) && (str_contains(strtolower($generalNotes), 'proforma') || str_contains(strtolower($generalNotes), 'quotation'))) ? 'Proforma Corporate Invoice / Quotation' : 'Corporate Booking Invoice' }}

Hello {{ $company->name }},

Thank you for your corporate booking at PrimeLand Hotel. This email contains your booking invoice and summary.

## Company Information

**Company:** {{ $company->name }}  
**Email:** {{ $company->email }}  
@if($company->phone)
**Phone:** {{ $company->phone }}  
@endif

## Booking Summary

**Check-in:** {{ \Carbon\Carbon::parse($checkIn)->format('F d, Y') }}  
**Check-out:** {{ \Carbon\Carbon::parse($checkOut)->format('F d, Y') }}  
**Number of Nights:** {{ $nights }} night(s)  
**Total Guests:** {{ count($bookings) }} guest(s)

## Financial Summary

**Total Group Value:** ${{ number_format($companyCharges + $selfPaidCharges, 2) }}  
**Total Amount Paid:** ${{ number_format($totalCompanyPaid, 2) }}  
**Overall Balance Due:** ${{ number_format(($companyCharges + $selfPaidCharges) - $totalCompanyPaid, 2) }}

Please find the attached **{{ (isset($generalNotes) && (str_contains(strtolower($generalNotes), 'proforma') || str_contains(strtolower($generalNotes), 'quotation'))) ? 'Proforma Corporate Invoice / Quotation' : 'Corporate Booking Invoice' }} (PDF)** for the full detailed breakdown of guest rooms, individual costs, and payment responsibilities.

@if($generalNotes)
## General Notes

{{ $generalNotes }}

@endif

## Payment Information

All company charges will be invoiced separately. Individual guests with self-paid responsibility will handle their own payments.

## Next Steps

1. All guests have been sent their booking confirmations with login credentials
2. Guests can log in to their accounts to request services during your stay
3. Company invoice will be sent separately for payment processing
4. For any questions or modifications, please contact our reception team

## Booking Policy

All bookings will be confirmed by receiving 50% of deposit before guests check in, remaining balance to be cleared on arrival or before guests check out. Full pre-payment will be required within 7 days of booking time.

## Cancellation & No Show Policy

All cancellations must be received with written confirmation.

- **30+ days before arrival:** Free cancellation (no charges)
- **14-30 days before arrival:** 50% cancellation fee of total deposit
- **7-14 days before arrival:** 100% cancellation fee of total deposit
- **1 day before check-in or No Show:** Hotel reserves the right to charge the guest the total amount of the booking as penalty

If you have any questions, please contact us at your earliest convenience.

Thank you for choosing PrimeLand Hotel!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
