<x-mail::message>
# Shopping List Finalized

The shopping list **{{ $list->name }}** has been finalized and recorded in the system.

**Summary:**
- **Date:** {{ $list->shopping_date }}
- **Total Actual Cost:** TSh {{ number_format($list->total_actual_cost, 0) }}
- **Budget Amount:** TSh {{ number_format($list->budget_amount, 0) }}
- **Remaining Budget:** TSh {{ number_format($list->amount_remaining, 0) }}
- **Items:** {{ $list->items->count() }}

<x-mail::button :url="config('app.url') . '/manager/restaurants/shopping-list/' . $list->id">
View Full Report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
