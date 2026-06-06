# Use Case Diagram - Wikanda Hair Salon

```mermaid
flowchart LR
    Guest["General User"]
    Member["Member"]
    Staff["Staff"]
    Admin["Admin"]
    Owner["Owner"]

    Browse["View services and salon info"]
    Register["Register account"]
    Login["Login"]
    Book["Create booking"]
    Pay["Attach payment slip"]
    History["View booking history"]
    Review["Write review"]
    StaffQueue["View assigned queue"]
    UpdateStatus["Update booking status"]
    ManageMaster["Manage services, staff, users"]
    ApprovePayment["Review and approve payments"]
    Reports["View revenue and operations reports"]

    Guest --> Browse
    Guest --> Register
    Guest --> Login
    Member --> Book
    Member --> Pay
    Member --> History
    Member --> Review
    Staff --> StaffQueue
    Staff --> UpdateStatus
    Admin --> ManageMaster
    Admin --> ApprovePayment
    Admin --> Reports
    Owner --> ManageMaster
    Owner --> ApprovePayment
    Owner --> Reports
```

Role scope:
- Guest can browse, register, and log in.
- Member can book, pay, review, and track history.
- Staff handles daily assigned queues and status updates.
- Admin and Owner manage operations, payments, and reports.
