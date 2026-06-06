# Wireframe Notes - Wikanda Hair Salon

## Public Home

```mermaid
flowchart TD
    Hero["Hero: brand, CTA booking, service highlights"]
    Services["Featured service cards"]
    Trust["Stats, opening hours, contact"]
    Footer["Footer"]
    Hero --> Services --> Trust --> Footer
```

## Auth

```mermaid
flowchart TD
    AuthShell["Split auth shell"]
    Form["Login/Register form"]
    Benefits["Salon trust panel"]
    AuthShell --> Form
    AuthShell --> Benefits
```

## Member

```mermaid
flowchart TD
    Sidebar["Role navigation"]
    Dashboard["Upcoming bookings and quick actions"]
    BookingForm["Booking form"]
    History["Booking history"]
    Sidebar --> Dashboard
    Sidebar --> BookingForm
    Sidebar --> History
```

## Admin / Owner / Staff

```mermaid
flowchart TD
    Sidebar["Role navigation"]
    Metrics["Metric grid"]
    Tables["Operational tables"]
    Actions["Status and management actions"]
    Sidebar --> Metrics --> Tables --> Actions
```

Design direction:
- Premium salon feel with warm neutrals, rose accents, glass panels, and high readability.
- Dense enough for repeated admin/staff work, but still polished for customers.
- Mobile-first layouts with no horizontal overflow.
