# System Architecture - Wikanda Hair Salon

```mermaid
flowchart TB
    Browser["Browser UI<br/>Bootstrap 5 + custom CSS + vanilla JS"]
    Front["public/index.php<br/>Web front controller"]
    ApiFront["api/index.php<br/>REST API front controller"]
    Router["App\\Core\\Router"]
    Controllers["Controllers<br/>Home, Auth, Member, Booking, Staff, Admin"]
    ApiHandlers["api/v1 handlers"]
    Middleware["AuthMiddleware<br/>login and role guards"]
    Services["Services<br/>Auth, Booking, Payment, Report, LINE, Slip2Go"]
    Repos["Repositories<br/>JSON data access"]
    Data["data/*.json"]
    Storage["storage/uploads/slips"]
    Line["LINE Messaging API"]
    Slip2Go["Slip2Go API"]

    Browser --> Front
    Browser --> ApiFront
    Front --> Router --> Controllers
    Controllers --> Middleware
    Controllers --> Services
    ApiFront --> ApiHandlers --> Services
    Services --> Repos --> Data
    Services --> Storage
    Services -. optional .-> Line
    Services -. optional .-> Slip2Go
```

Deployment target:
- XAMPP Apache + PHP 8+.
- `public/` is the web root.
- JSON storage is current production mode for this phase.
- MySQL migration is reserved for Phase 7.
