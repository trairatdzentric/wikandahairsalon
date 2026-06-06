# Test Plan - Wikanda Hair Salon

## Smoke Tests

- Public home loads at `/public/`.
- Services page loads at `/public/services`.
- Login and register pages load and submit validation works.
- CSS and JavaScript assets return HTTP 200.
- API health paths return JSON, especially `/api/v1/services` and `/api/v1/auth`.

## Role Tests

- General user can browse services, login, and register.
- Member can open dashboard, create a booking, view booking detail, and see history.
- Staff can open staff dashboard and assigned bookings.
- Admin can open dashboard and manage users, services, staff, bookings, payments, and reports.
- Owner can access admin-level pages and reports.

## Booking Tests

- Required fields block empty booking submission.
- Date picker cannot select dates before today.
- Time validation allows only 10:00 to 20:00.
- Duplicate booking slot is rejected by `BookingService`.
- Created booking appears in member history and staff queue.

## Payment Tests

- Payment record can be created for a booking.
- Slip upload accepts configured image types only.
- Slip2Go verification handles configured, disabled, success, and failure states.
- Admin can review pending payments.

## Integration Tests

- LINE Messaging API stays disabled without a real token.
- With a valid token, booking notification sends without breaking booking creation.
- API responses follow `{ success, message, data }` or `{ success, message, errors }`.

## Visual QA

- Desktop width 1440px has no horizontal overflow.
- Mobile width 390px has readable text and usable navigation.
- Buttons, form labels, tables, and cards do not overlap.
- Sidebar collapse/expand state persists.
