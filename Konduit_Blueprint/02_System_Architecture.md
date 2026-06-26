# Architecture

Frontend
- Flutter (Agency App)
- Flutter (Client App)
- Responsive Web Portal

Backend
- Laravel
- PostgreSQL
- Redis
- Horizon
- Queue Workers
- Sanctum/Passport
- Stripe via Laravel Cashier

Automation
- n8n hidden behind Konduit
- Webhooks where supported
- Scheduled syncs where appropriate

Data Flow

External Systems
→ Integration Layer
→ Normalization Layer
→ Database
→ AI
→ Dashboards
→ Reports
→ Notifications

Never allow AI to query third-party APIs directly.
