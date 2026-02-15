# Company Paid Orders Implementation

## Overview
This document describes the implementation of the "Company Paid" workflow for the PrimeLand Hotel system. This feature allows the system to automatically handle orders for bookings where the company is responsible for all service payments.

## Database Schema
The `bookings` table contains the following relevant fields:
- `company_id` - Identifies the company associated with the booking
- `payment_responsibility` - Indicates who is responsible for payment:
  - `null` - Default/not specified
  - `"company"` - Company pays for all services
  - `"self"` - Guest pays for services

## Implementation Details

### 1. Bar Keeper Dashboard (`resources/views/dashboard/bar-keeper-dashboard.blade.php`)

**Changes Made:**
- Added logic to check if a booking has `payment_responsibility === 'company'`
- **PAY button is hidden** for company-paid bookings
- Displays a "Company Paid" badge instead of the PAY button
- Orders are automatically charged to the room when served

**Code Location:** Lines 89-204

### 2. Kitchen Dashboard (`resources/views/admin/restaurants/kitchen/dashboard.blade.php`)

**Changes Made:**
- Added logic to check if a booking has `payment_responsibility === 'company'`
- **PAY button is hidden** for company-paid bookings
- Displays a "Company Paid" badge instead of the PAY button
- Orders are automatically charged to the room when marked as served

**Code Location:** Lines 103-220

### 3. Bar Keeper Controller (`app/Http/Controllers/BarKeeperController.php`)

**Changes Made:**
- Modified `serveOrder()` method to detect company-paid bookings
- When a company-paid order is served:
  - `payment_status` is automatically set to `'room_charge'`
  - `payment_method` is automatically set to `'room_charge'`
  - Reception notes indicate "Auto-charged to Company"
  - Success message confirms auto-charging

**Code Location:** Lines 601-640

### 4. Kitchen Order Controller (`app/Http/Controllers/KitchenOrderController.php`)

**Changes Made:**
- Modified `complete()` method to detect company-paid bookings
- When a company-paid order is marked as served (without manual payment):
  - `payment_status` is automatically set to `'room_charge'`
  - `payment_method` is automatically set to `'room_charge'`
  - Reception notes indicate "Auto-charged to Company"

**Code Location:** Lines 78-117

## User Experience

### For Bar Keeper:
1. When viewing pending orders, company-paid bookings show a "Company Paid" badge
2. The "PAY" button is not displayed for these orders
3. When clicking "Serve" on a company-paid order, it is automatically charged to the room
4. A success message confirms: "Order marked as SERVED and auto-charged to Company!"

### For Kitchen Staff:
1. When viewing pending orders, company-paid bookings show a "Company Paid" badge
2. The "PAY" button is not displayed for these orders
3. When clicking "Mark Served" on a company-paid order, it is automatically charged to the room
4. The order is removed from the pending queue immediately

## Benefits

1. **Reduced Manual Work:** Staff no longer need to manually select payment method for company-paid orders
2. **Error Prevention:** Eliminates the possibility of forgetting to charge company-paid services
3. **Clear Visibility:** Staff can immediately see which orders are company-paid via the badge
4. **Audit Trail:** All auto-charged orders are logged in reception_notes for tracking

## Testing Recommendations

1. Create a test booking with `payment_responsibility = 'company'`
2. Order bar/food services for that booking
3. Verify the "Company Paid" badge appears in both Bar Keeper and Kitchen dashboards
4. Verify the "PAY" button is hidden
5. Mark the order as served and confirm it's auto-charged to room
6. Check the service_requests table to verify `payment_status = 'room_charge'` and `payment_method = 'room_charge'`
7. Verify the reception_notes contain "Auto-charged to Company"

## Future Enhancements

Potential improvements for future iterations:
1. Add a filter to show only company-paid orders
2. Generate reports specifically for company-paid services
3. Add company-specific billing summaries
4. Implement approval workflows for high-value company orders
