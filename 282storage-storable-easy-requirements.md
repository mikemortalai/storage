# 282 Storage / Storable Easy — Requirements Discovery

**Status:** Phase 1 + Phase 2 complete (public site + live admin/reports, read-only).  
**Constraint honored:** Look, don’t touch — login only; no creates/edits/saves; no customers/units/rentals/waiting-list/payments/settings mutations.  
**Date:** 2026-08-02  
**Sources:** Live public site crawl of `282Storage.com` + authenticated admin walkthrough of Facility ID **8435** (Storable Easy). No tenant PII is recorded in this document.

---

## 0. Executive summary

282 Storage (Ellijay, GA) currently runs on **Storable Easy** (Easy Storage Solutions / Storable). The public website, online rental widgets, tenant login/payment portal, waiting list, and facility CMS pages are all served by the Storable Easy hosted platform.

Goal of this project (IsoVerse):

1. **Replicate** Storable Easy’s end-to-end facility management + public website functionality for 282 Storage.
2. Add **AI-native features** as differentiators.
3. Package the product as a **multi-tenant Storage Management Software platform** that can be sold by subscription to other facility owners — including marketable **website/site templates** (282 Storage is the first production tenant and reference implementation).

This document captures:

1. What was verified live on the public site (read-only).
2. The Storable Easy functional surface area that must be replicated (live admin/reports verified).
3. A proposed IsoVerse domain model, epic breakdown, AI backlog, and **SaaS/template commercialization** requirements.

---

## 1. Discovery method & safety

- Authenticated via `/login` → `POST /sessions` (username continue, then password).
- Landed on `/reports/dashboard` as manager/owner user.
- Crawled admin nav destinations and report pages with **GET only**.
- Intentionally avoided: New Customer/Unit, Import, Make Payment, Mass Edit, Rent/Remove waitlist actions, Save on settings, clone/delete pages, batch creates.
- **Security:** Temporary credentials were shared in chat for this session — **rotate the password immediately** after discovery. Prefer Cursor Secrets for future runs.

---

## 2. Business profile (verified from public site)

| Field | Value |
|---|---|
| Brand | **282 Storage** / Mike the Storage Guy |
| Location | 3875 Tails Creek Rd, Ellijay, GA 30540 |
| Phones | (706) 972-2065; owner line mentioned as 404-550-4390 |
| Email | Protected on site (Cloudflare email obfuscation); appears as facility contact email |
| Positioning | Owner-operated, climate control with **temperature + humidity**, paved, new facility, strong local/personal brand |
| Office hours | Sun–Wed/Fri/Sat 10:00 AM–6:00 PM; Thu 10:00 AM–9:00 PM |
| Platform | Powered by Storable Easy (`storageunitsoftware.com`) |
| Brand colors (site CSS) | Primary `#114393`, secondary `#0edb47` |
| Social | Facebook, Instagram `@282storage`, TikTok `@282storage`, YouTube channel `UC0Crr7w7n4W6Y_jIat82tEw` |

---

## 3. Public website inventory (verified live)

### 3.1 Hosts / URLs

| URL | Role |
|---|---|
| `https://www.282storage.com/` | Canonical public site |
| `https://282storage.com/` | Redirects to www |
| `https://282storage.storageunitsoftware.com/` | Same Storable Easy site on vendor domain |
| `https://www.282storage.com/login` | Shared facility login (tenant + likely staff continue flow) |
| `https://www.282storage.com/dashboard` | Redirects to login when unauthenticated |
| `https://www.282storage.com/unit_types` | Redirects to login |
| `https://www.282storage.com/api/unit_types` | Returns **401** unauthenticated |

### 3.2 Information architecture

**Primary nav**

- Home
- Rent Storage (`/pages/storage`)
- Map (`/pages/map`)
- Mike the Storage Guy (`/pages/Mike-the-Storage-Guy`)
- 282 Storage Blog (`/pages/blog`)
- Contact Us (`/pages/contact`)
- More → Rent 10x10 / Rent 10x20 / Rent 10x15 landing pages
- CTA: **Make a Payment/Login** → `/login`

**CMS / content pages (sitemap)**

- `/pages/storage`, `/pages/map`, `/pages/contact`, `/pages/blog`, `/pages/Mike-the-Storage-Guy`
- Product landers: `/pages/rent10x10`, `/pages/rent10x10climate`, `/pages/rent10x15storage`, `/pages/rent10x20`
- Blog posts under `/blog/*` (40+ storage tips / climate-control content pieces)
- Facility site map page exists as content page (map imagery/content)

**Global footer**

- Address, phones, office hours
- Blog / Mike / unit landers / Book Online
- “Powered by Storable Easy”
- Google review CTA

### 3.3 Unit catalog observed on homepage (point-in-time)

> Prices/availability are live inventory and will change. Captured 2026-08-02, read-only.

| Unit type (display) | Dimensions | Price shown | Availability CTA | Known unit #s / IDs |
|---|---|---|---|---|
| 8x20 Container | 8 x 20 | $110 / month | **Rent Now** | Unit `1006` (internal unit id `1436508`); rental plan id seen `147357`; unit_type_id seen `93064` |
| 10x20x12 Climate Control | 20 x 10 x 12 | $190 / month | **Rent Now** | Units `301, 307, 308, 313, 315` (ids `2504307`…); plan id `204107`; unit_type_id `135790` |
| Uncovered parking | 40 x 12 | $125 / month | **Waiting List** | unit_type_id `180551` |
| 10x15x12 Climate Control | 10 x 15 x 12 | Starting at $150 / month | **Waiting List** | unit_type_id `135802` |
| 10x10x12 Climate Control | 10 x 10 x 12 | $120 / month | **Waiting List** | unit_type_id `135799` |

Widget id observed on waiting-list links: `154111`.

Admin Unit Types confirms additional inventory beyond homepage widgets (sign rental, 10x12 climate, covered garage/parking, Carvers Creek gallery/shop). See §4.5.

### 3.4 Public flows to replicate

#### A. Browse availability / rent online

1. Visitor lands on Home or Rent Storage.
2. Sees unit types with price, description, amenity copy.
3. For available types:
   - Optional unit picker (“Automatically Select” or specific unit number).
   - Payment option radio (observed: **1 Month** plan).
   - Submit **Rent Now** → `POST /rental_intents` with `rental_intent[unit_type_id|plan_id|unit_id|customer_id]`.
4. Expected continuation (not executed): account creation / login, lease/e-sign, payment method, move-in date, confirmation.

#### B. Waiting list (full units)

1. CTA → `/waiting_list?unit_type_id=…&widget_id=…`
2. Form fields observed (not submitted):
   - `waiting_customers_form[name]`
   - `waiting_customers_form[cell_phone]`
   - `waiting_customers_form[email]`
   - `waiting_customers_form[texting_consent]`
   - `waiting_customers_form[unit_type_id]`
   - `waiting_customers_form[move_in_on]`
   - `waiting_customers_form[notes]`
3. `POST /waiting_list`

#### C. Tenant portal: Make a Payment / Login

1. `/login` collects **Username** via `facility_login_form[username]` then **Continue**.
2. Multi-step auth (password / MFA) not fully observed without credentials.
3. Post-login capabilities expected from product docs + site CTAs:
   - View balance / invoices
   - Pay rent
   - Autopay / payment methods
   - Account profile
   - Possibly gate/access info, documents, move-out request

#### D. Contact lead form

Fields: `name`, `email`, `phone`, `message` (not submitted).

#### E. Content / SEO / brand

- Owner video blog (“Mike the Storage Guy”) with YouTube + TikTok embeds
- Long-form blog for SEO / education
- Review acquisition links
- Custom branded CSS (blue/green)

### 3.5 Amenities / value props to preserve in marketing site

- Complimentary carts
- Onsite security / digital video surveillance
- 10 cameras live to owner phone
- Online bill pay
- Variety of unit sizes
- Drive-up access
- Climate control (temperature + humidity)
- Paved roads
- Owner-operated / human answers phone
- SSL / secure payments messaging

---

## 4. Live admin surface area (Facility ID 8435) — VERIFIED

### 4.1 Primary navigation (exact)

| Top-level | Items observed |
|---|---|
| **Dashboard** | `/reports/dashboard` — tasks due, undelivered notifications; editable manager dashboard |
| **Task Manager** | `/tasks` |
| **Make Customer Payment** | `/customers/make_payment` (not exercised) |
| **Setup** | Settings, Users & Roles, Contact, Gate, Fees, Late/Lien, Payment Processing, QuickBooks, Tax Rates, Rate Management, Promotions, SpareFoot |
| **Units** | Site Map, Grid View, Notes, Unit Types, List View, New Unit, Create Multiple Units, Field Settings |
| **Customers** | All Customers, Retail Sale, New Quote, Reservations, Waiting List, New Customer, Import Customers, Field Settings, Mass Edit Rental Prices |
| **Reports** | All / Accounting/Financials / Customers / Facility / Payments and Deposits |
| **Email, Txt & Print** | Print Batches, Templates, Communication Settings, Print/Email Letters, Send Email, Mailing Labels, Text Messages |
| **Website** | Pages, Blog, Image Library, Additional Services, Website Settings, View Site |
| **Account** | Edit Account, Log out |

Unit status color legend (from admin CSS): Auction `#FFEA5E`, Available `#139771`, Late `#D13500`, Lien `#FFC153`, Locked Out `#916397`, Moving Out `#97524F`, Pending `#BCD191`, Pre-Lien `#F28500`, Rented `#4A6197`, Reserved `#81CDD1`, Unavailable `#959790`.

### 4.2 Facility settings currently enabled (selected values)

| Setting | Live value |
|---|---|
| Locale | United States — Dollar ($) — MM/DD/YYYY |
| Billing period (invoice lead days) | **7** days prior to due date |
| Time zone | Eastern Time (US & Canada) |
| Phone format | `(555) 555-5555` |
| Unit type format | `10l x 10w x 10h` |
| Customer rental prorating | Prorate this month and bill now for the first billing cycle |
| Default manager prorating option | Prorate this month and bill full billing cycle starting next month |
| Customers can prepay | Yes |
| Disable partial payments for locked-out customers | Yes |
| Save unpaid customer rentals | Yes |
| Auto-approve rentals | Yes |
| Additional deposits enabled | Yes |
| Customers can edit profile | Yes |
| Customers can edit payment accounts | Yes |
| Customers can schedule move-outs | Yes |
| Move-out days restriction | **10** |
| Future reservation window | **1** day |
| Gate company | **QuikStor** |
| Gate keys enabled | Yes |
| QuickBooks Online | **Inactive** (not connected); QBO file export available |
| SpareFoot | Integration promo page present (not confirmed connected) |
| Easy Storage Payments | **Verified and active**; daily rolling 2-business-day card deposits; ACH up to ~9 business days |
| Tax rates | Screen present; no named rates populated in list UI at crawl time |
| Website login button text | `Make a Payment/Login` |
| Public contact | 3875 Tails Creek rd, Ellijay GA 30540; (706) 972-2065; website contact email configured |
| Social | Facebook, Instagram, YouTube, TikTok footer link, Google review URL |

### 4.3 Late / Lien automation (live rules)

| Status | Days past due | Fees | Notifications (email/text) |
|---|---|---|---|
| Late | 5 | Late **$15.00** | Notice of Lockout; Notice of Intended Sale at Auction |
| Locked Out | 7 | Lock out fee **$5.00** | Same lockout/sale notices |
| Pre-Lien | — | *(no rules configured)* | — |
| Lien | 31 | Lien Fee **$100.00** | Foreclosure/sale notice with `[[AUCTION_DATE]]` |
| Auction | 45 | No fees | Foreclosure/sale notice |

### 4.4 Users & roles

Roles present: **Owner/Manager**, **Manager**, **Sales Associate**. Multi-facility flag exists on users. (Usernames/PII omitted.)

### 4.5 Unit types & inventory (admin Unit Types)

Approx **78 units** across types:

| Unit type | Units | Pricing observed |
|---|---|---|
| Sign Rental (8×4×4) | 2 | $75 / 1 month |
| 8x20 Container | 13 | $110 / 1 month |
| Uncovered parking (40×12) | 1 | $125 / 1 month |
| 10x10x12 Climate Control | 11 | $120 / 1 month |
| 10x12x12 Climate Control | 2 | $90 and $120 / 1 month plans |
| 10x15x12 Climate Control | 22 | $150 and $170 / 1 month plans |
| 10x20 Covered Garage | 1 | $175 / 1 month |
| 10x20x12 Climate Control | 23 | $190 / 1 month |
| Carvers Creek Gallery (50×50) | 1 | $1,900 / 1 month |
| 12x20 covered parking | 1 | $175 / 1 month |
| Carvers Creek Shop (40×100×10) | 1 | $1,500 / 1 month |

Point-in-time occupancy (Occupancy report totals): **68 / 78 occupied** (~87% unit occupancy); economic occupancy ~76%.

Also present beyond public homepage merchandising: sign rental, 10x12 climate, covered garage/parking, and two **Carvers Creek** commercial spaces (gallery/shop).

### 4.6 Customers module capabilities (verified UI)

- Search by name/unit/phone/email/address/DL/custom field/check number/last4/auth code
- Filters: Active, Current, Recurring, Non-Recurring, Outstanding, Past Due, Late, Locked Out, Auction Date Set, Waiting List, Late Fee Exempt, Tax Exempt, Automatic Lockout Disabled, Archived, All
- Sort: Name, Balance, Lock Out Date, Auction Date, Unit, Days Behind
- Export CSV / PDF
- Waiting list + scheduled move-outs share `/waiting_customers` (waitlist columns: requested move-in, date added, name, email, contact, unit type, available count, notes; actions Edit/Rent/Remove — not used)
- Retail products configured: Overlock Service $75, Moving Pad $39, Disc Lock $30 (with inventory counts)
- Reservations list page present

### 4.7 Communications

Default templates include: Account Information, Automatic Payment Receipt, Failed Recurring Payment, Invoice Reminder, Manual Payment Receipt, Move Out Receipt/Request, Notice of Scheduled Rate Change, Rental Instructions, Reservation Receipt, Scheduled Move Out, Storage Agreement, Waiting List Confirmation, plus Late/Lien custom templates.

Communication settings cover notification/reply/from emails, reminder period, owner texts on credit-without-payment and online rentals, print invoice reminders (letter/postcard), and owner emailed digests (failed notifications, lockouts, signed agreements, deposits, etc.).

### 4.8 Website CMS

Managed pages match public sitemap landers (storage, map, rent landers, Mike the Storage Guy, blog, contact). Features: Manage Navigation, Add Page, Add External Link, clone page, blog posts, image library, additional services (SEO upsell).

### 4.9 Reports catalog — LIVE complete list

#### Accounting / Financials
| Report | Path | Notes / columns or filters |
|---|---|---|
| Check Batches | `/check_batches` | Batch, Created, Checks, Status, Total |
| Lost Revenue | `/reports/lost_revenue` | Unit, type, monthly price; include unavailable |
| Expected Revenue | `/reports/expected_revenue` | Standard vs actual price variance |
| Future Revenue | `/reports/future_revenue` | Month/year; invoice-oriented |
| Collections | `/reports/collections` | Balance, days behind, status |
| Revenues | `/reports/revenues` | Date range; group-by |
| Yearly Revenues | `/reports/yearly` | Year |
| Monthly Deposits | `/reports/deposits` | Month/year |
| Sales | `/reports/sales` | Category/amount/tax |
| Sales Tax | `/reports/sales_tax` | Date range |
| Total Deposits | `/reports/total_deposits` | |
| Credit Without Payment | `/reports/credit_without_payment` | Date range |
| Refunds | `/refunds` | Refunds list UI |
| Failed and Declined Payments | `/reports/failed_and_declined_transactions` | Recurring/one-time + dates |
| Alterations | `/reports/alterations` | Category + dates |
| Accrual | `/reports/accrual` | Invoice/payment accrual lines |
| Retail Sales | `/reports/retail_sales` | Date range |
| Rate Management Batches | `/rate_change_batches` | Scheduled rate changes |
| Occupancy | `/reports/occupancy` | By unit type: units, sqft, occ %, economic occ |
| Occupancy History | `/reports/occupancy_history` | Monthly by unit type |
| Recurring Fees | `/reports/recurring_charges` | Interval/amount/next bill |
| Daily Close Batches | `/close_batches` | Cash drawer close workflow |

#### Customers
| Report | Path | Notes |
|---|---|---|
| Tenant Credit | `/reports/credit` | Credits outstanding |
| Reservations | `/reservations` | Active reservations |
| Next Bill Due | `/rentals/next_charge_on_report` | Next bill offset |
| Move In / Move Out | `/reports/move_in_move_out` | Month/year |
| Scheduled Move Outs | `/waiting_customers` | Combined UI with waitlist |
| Waiting List | `/waiting_customers` | |
| Lock Outs | `/reports/locked_out` | Approval + dates |
| Tenant Data | `/reports/tenant_data` | Unit/type/move-in/billing/rent/paid-to |
| Rental Transfers | `/reports/rental_transfers` | Date range |
| Storage Agreements | `/reports/storage_agreements` | Send agreement actions (not used) |
| Undelivered Notifications | `/notification_dismissal_batches/new` | Review UI (no dismiss submitted) |
| Customer Notes | `/reports/customer_notes` | Account type + dates |
| Credit Card Expiration Dates | `/reports/credit_card_expiration_dates` | |
| Active Promotions | `/reports/active_promotions` | |

#### Payments and Deposits (processor)
| Report | Path |
|---|---|
| Transactions | `/reports/processor_transactions` |
| Bank Activity | `/reports/processor_bank_activity` |
| Statements | `/reports/processor_statements` |
| Merchant Accounts | `/reports/processor_merchant_accounts` |
| Chargebacks | `/reports/processor_chargebacks` |

#### Facility
| Report | Path | Notes |
|---|---|---|
| Gate Codes | `/gate_key_management_report` | Unit, gate key, status, tenant, balance |
| Unit List | `/units/list` | Operational list |
| Management Summary | `/reports/management_summary` | Date |
| Unit Notes | `/units/notes` | |
| Rent Roll | `/reports/rent_roll` | Rates, variance, paid-to, deposits |
| Square Footage | `/reports/square_footage` | Occupied area % |
| Retail Inventory Summary | `/reports/inventory` | Product inventory movement |
| Tasks | `/reports/tasks` | Completed status / creator / dates |
| Custom Fields | `/reports/custom_fields` | |
| Unit Status | `/reports/unit_status` | Counts by status per type |
| Length of Stay | `/reports/length_of_stay` | Avg days/months |
| Vacant Units | `/reports/vacant_units` | Available by type |
| Self Insured Rentals | `/reports/self_insured` | |

### 4.10 Core operational workflows to replicate

1. Lead → reservation/waitlist → rental application → e-lease → payment → move-in  
2. Manual move-in from Units or Customers  
3. Recurring billing (7-day lead) + proration options observed above  
4. Online + in-office payments via Easy Storage Payments (+ check batches / daily close)  
5. Autopay / saved payment accounts (tenant-editable)  
6. Delinquency ladder: Late → Locked Out → Lien → Auction with fees + email/SMS  
7. Tenant communications (email/SMS/print batches + templates)  
8. QuikStor gate keys + automatic lockout/removal  
9. Retail products / manual fees / recurring fees  
10. Unit grid/list/map + unit types/plans/photos  
11. Waitlist + scheduled move-out  
12. Rate management batches / promotions  
13. Task manager + undelivered notification handling  
14. Website CMS/blog/image library  
15. Optional QuickBooks Online sync (currently inactive)  
16. Staff roles (Owner/Manager/Sales Associate)  
17. Exports (customer CSV/PDF; report PDFs where offered)

### 4.11 Integrations (live)

| Integration | Status |
|---|---|
| Easy Storage Payments | Active / verified |
| QuikStor gate | Selected; gate keys enabled |
| QuickBooks Online | Inactive |
| SpareFoot / Marketplace | Setup page present |
| SEO & Online Marketing add-on | Marketed under Additional Services |
| Email + SMS notifications | In use (templates + undelivered queue) |

---

## 5. Target IsoVerse domain model (draft)

```
Platform (IsoVerse)
  ├─ Plans / Subscriptions / Entitlements
  ├─ SiteTemplates (theme + starter pages + demo content packs)
  ├─ Marketplace / Marketing site (sell subscriptions)
  └─ Tenants (subscriber accounts)
       └─ Organization
            ├─ Facilities (1..n)
            │    ├─ Users (owner, managers, sales associates)
            │    ├─ Settings (billing, taxes, late/lien, hours, contracts)
            │    ├─ UnitType → Unit
            │    ├─ Customer / Tenant ledger, rentals, access, docs
            │    ├─ Leads / Waitlist / Reservations / Applications
            │    ├─ Invoices / Payments / Batches / Reports / Tasks
            │    └─ Website (custom domain, pages, blog, media, theme)
            ├─ Branding / Template selection / AI feature flags
            └─ BillingCustomer (subscription to IsoVerse)
```

**Unit statuses (minimum):** Available, Reserved, Rented, Late, Locked Out, Unavailable, Pending Move-Out.  
**Customer statuses:** Active, Late, Lockout, Archived, Lead/Waitlist.  
**Hard multi-tenant rule:** no cross-tenant data leakage; every query scoped by organization/facility.

---

## 6. Functional requirements by epic (replication)

### Epic P0 — Foundation

- Multi-role auth (owner/manager/tenant), MFA-ready
- Facility settings, branding theme tokens
- Audit log for all admin mutations
- Soft-delete / archive patterns; no silent hard deletes for financial history

### Epic P1 — Inventory & occupancy

- Unit types + units CRUD (admin)
- Grid/list/site-map views with status colors
- Website visibility controls per unit type
- Real-time availability for public rent/waitlist widgets

### Epic P2 — Tenant lifecycle

- Online rent flow + e-sign lease
- Manual rent/move-in
- Waitlist
- Move-out scheduling
- Tenant portal (pay, autopay, profile, docs)

### Epic P3 — Billing & payments

- Recurring invoices, proration rules, prepaid months
- Fees (late, admin, retail, recurring add-ons)
- Payment methods: card, ACH, cash, check, other
- Autopay
- Refunds/chargebacks
- Daily close + check batches + processor deposits
- Tax handling

### Epic P4 — Delinquency & access

- Configurable reminder cadence (email/SMS)
- Late fee rules
- Lockout rules tied to access control
- Lien workflow configuration / documentation checkpoints
- Gate code issuance & activity log

### Epic P5 — Comms & CRM

- Tenant timeline (notes, messages, calls logged)
- Templates for invoices, receipts, late notices
- Lead/contact inbox
- Review-request nudges

### Epic P6 — Reports & accounting

- Parity with Storable Easy report list (finalize in Phase 2)
- PDF/CSV export
- QuickBooks or generic accounting export
- Saved date ranges / scheduled email reports

### Epic P7 — Marketing website

- CMS pages, blog, video hub
- SEO metadata, sitemap, redirects
- Unit merchandising widgets
- Brand-first landing experience (owner personality + climate-control proof)

### Epic P8 — AI features (net-new; see §7)

### Epic P9 — Multi-tenant SaaS, site templates & subscriptions (see §7A)

---

## 7. AI feature requirements (IsoVerse differentiators)

Prioritize features that help a single owner-operator (Mike) save time and grow occupancy.

### 7.1 Owner copilot (admin)

- Natural-language questions over facility data: “Who is 30+ days late?”, “What’s my occupancy by unit type?”, “Which units turn over most?”
- Daily briefing: move-ins/outs, delinquencies, open tasks, waitlist hot leads
- Suggested actions with confirmation gates (never auto-mutate without approval)

### 7.2 Pricing & occupancy intelligence

- Recommend rate changes by unit type using occupancy, waitlist depth, seasonality, local comps (when available)
- Explain recommendations in plain language
- Optional A/B web price tests with guardrails

### 7.3 Delinquency prediction & collections assist

- Risk score for likely late payers
- Draft personalized reminder messages for approval/send
- Next-best-action for lockout/lien stages

### 7.4 Lead & waitlist conversion

- Auto-qualify contact form / waitlist notes
- Suggested follow-up scripts / call times
- Instant answers for common pre-rental questions (sizes, climate control, hours)

### 7.5 Tenant-facing AI (branded as Mike the Storage Guy)

- Chat on site: unit size advice, climate vs container, packing tips grounded in Mike’s blog/videos
- Escalation to human/owner with transcript
- Strict boundaries: no inventing availability/prices — always read live inventory APIs

### 7.6 Content & SEO assist

- Draft blog posts from video transcripts
- FAQ generation from real customer questions
- Review response drafts

### 7.7 Ops vision (optional later)

- Camera event summarization (“gate open after hours”) — privacy/security review required
- Unit condition notes from move-in/out photos

**AI non-negotiables**

- Read-only by default; writes require explicit owner confirmation
- Full audit trail of AI suggestions vs accepted actions
- No training on tenant PII for external models without explicit policy
- Clear disclosure when tenant is chatting with AI
- AI features must be **plan-entitled** (packaged for upsell across subscriber facilities)

---

## 7A. SaaS commercialization — site templates & subscriptions

IsoVerse is not only 282 Storage’s internal stack. It must be sellable as **Storage Management Software-as-a-Subscription**, with the public website experience offered as **branded site templates** other operators can launch quickly.

### 7A.1 Product packaging

| Offering | Includes (minimum) |
|---|---|
| **Starter** | 1 facility, templated marketing site + custom domain, online rent/waitlist, tenant pay portal, basic admin (units/customers/billing), core reports |
| **Growth** | Delinquency automations, SMS/email templates, gate integration hooks, retail/products, advanced reports, multi-user roles |
| **Pro / AI** | Everything in Growth + AI copilot, pricing assist, delinquency prediction, tenant chat, content assist |
| **Add-ons** | Extra facilities, extra template packs, premium SEO landing pack, additional SMS volume, white-label removal of “Powered by IsoVerse”, priority support |

282 Storage runs on the same platform codepath as paying subscribers (dogfood). Prefer feature flags/entitlements over forks.

### 7A.2 Site template system

- **Template gallery** for prospects: preview desktop/mobile demos (e.g. “Owner-operated climate control”, “Budget drive-up”, “Mixed vehicle + climate”).
- Each template provides: theme tokens (colors/fonts), header/footer layouts, starter pages (Home, Rent, Map/Directions, Contact, FAQ/Blog), sample amenity blocks, SEO defaults.
- **One-click provision:** create organization → choose template → set facility name/address/phone → connect domain → seed demo or empty inventory.
- Tenant can **re-skin without code**: logo, colors, fonts, nav, office hours, social links, login CTA label (parity with Easy website settings).
- Support **custom domain** + managed SSL; optional `*.isoverse.app` subdomain for trial.
- 282 Storage remains a **reference template/case study**, not the only skin.
- Templates must not hardcode 282-specific copy; personal brand modules (e.g. “Owner video hub”) are optional blocks.

### 7A.3 Subscriber lifecycle (sell the software)

1. Marketing site explaining IsoVerse (features, pricing, AI differentiators, template demos).
2. Self-serve trial signup (time-boxed) or sales-assisted onboarding.
3. Subscription billing (card) with plan changes, proration, invoices, failed-payment dunning for **platform** fees (separate from facility tenant rent billing).
4. Onboarding checklist: units import, rates, lease template, payments, gate, domain DNS.
5. In-app upgrade prompts when hitting plan limits (facilities, SMS, AI).
6. Churn safeguards: data export, cancel flow, grace period, read-only mode on nonpayment.

### 7A.4 Multi-tenant platform requirements

- Strict **organization/facility isolation** (data, files, search, AI context, logs).
- Platform admin (IsoVerse operator) console: list subscribers, impersonate with audit, suspend, view plan usage — never silently alter facility ledgers.
- Per-tenant encryption keys or equivalent isolation strategy for documents/PII at rest (target architecture).
- Configurable “Powered by IsoVerse” footer; removable on higher plans.
- Horizontal scalability for many small facilities; noisy-neighbor protections.
- Localization hooks (currency/date/phone formats already required by Easy parity).
- Partner/reseller optional later: ability for agencies to deploy templates under their clients.

### 7A.5 Migration & competitive displacement

- Import path from Storable Easy-like CSV/exports (units, customers, balances) as a sales wedge.
- Side-by-side go-live checklist for operators switching from Easy/Edge/other PMS.
- Template + admin demo environment that sales can reset.

### 7A.6 Compliance / trust for selling SaaS

- Clear DPA / terms / privacy for subscriber facilities and their end-tenants
- PCI scope minimized (hosted payment fields) for both platform billing and facility rent collection
- Backup/restore and uptime SLAs stated per plan
- Audit logs available to facility owners for staff actions

---

## 8. Non-functional requirements

- Mobile-responsive public site + usable admin on laptop
- PCI-aware payment architecture (hosted fields / processor tokenization)
- SSL everywhere; secure password reset; MFA for staff
- Role-based access control **and** organization-level tenancy isolation
- Backup/export of tenant + ledger data (owner data portability)
- Uptime suitable for unmanned rental/payment 24/7
- Observability: payment failures, gate API failures, email/SMS delivery, **per-tenant usage metrics**
- Template provisioning completes in minutes; custom domain SSL automatable
- Platform subscription billing reliability independent of facility rent billing

---

## 9. Acceptance criteria for “feature parity” + sellability

Parity means an owner can stop using Storable Easy for day-to-day ops because IsoVerse supports:

1. Online rent + pay + waitlist without phone
2. Tenant self-service portal
3. Full ledger accuracy matching processor deposits
4. Delinquency automation + access lockout
5. Unit grid/map operations
6. Report set covering revenue, occupancy, collections, deposits, tenant data
7. Website CMS equivalent for current 282 content
8. Data migration path from Storable Easy (tenants, units, balances, documents)

**Sellability** means IsoVerse can also:

9. Sign up a second (non-282) facility on a subscription plan without code changes
10. Launch that facility on a **site template** with its own branding/domain
11. Enforce plan entitlements (features/limits) automatically
12. Bill the subscriber for software via subscription (trial → paid → cancel/export)
13. Keep 282 Storage and all other tenants isolated

AI features and template marketplace packs are **above** baseline parity, not a substitute for it.

---

## 10. Phase 2 completion notes

Completed read-only against Facility **8435**. Still useful follow-ups (optional, still read-only):

1. Open one anonymized tenant profile and map every sub-tab/button (Rent, Pay, Fees, Agreements, Gate, Notes) without changing data.
2. Capture lease/storage agreement field tokens and e-sign flow screenshots.
3. Confirm whether tenant protection / self-insured plans are actively sold (Self Insured Rentals report exists).
4. Document import/export formats for migration (customer import preview columns; any bulk export beyond CSV).
5. Trace online rental intent → application → agreement → payment end-to-end in a **staging** facility (not production).

---

## 11. Open questions for owner

1. Single facility only, or future multi-facility? *(Users UI already has a Multi-Facility flag.)*
2. Confirm QuikStor hardware details / any secondary gate vendors.
3. Do you want QuickBooks Online connected in IsoVerse, or CSV/export only? *(QBO currently inactive in Easy.)*
4. Tenant protection / self-insured: offered today? Required or optional?
5. Must IsoVerse migrate historical ledger/documents, or current occupants + balances only?
6. Keep Easy Storage Payments vs bring your own processor?
7. Highest-pain AI use case first: delinquency, pricing, tenant chat, or daily briefing?
8. Preserve SpareFoot/Marketplace lead flow?
9. Should Carvers Creek Gallery/Shop stay in the same product as storage units?
10. Target subscription price points / trial length for selling IsoVerse to other operators?
11. White-label: require “Powered by IsoVerse” on lower plans?
12. Which site templates should ship first besides the 282 reference style?
13. Self-serve signup vs sales-assisted onboarding for v1 of the SaaS motion?

---

## 12. Appendix — technical observations

- Public app appears Rails-like (CSRF `authenticity_token`, form object naming).
- Rental start endpoint: `POST /rental_intents`.
- Login form object: `facility_login_form[username]` then continue.
- Waiting list form object: `waiting_customers_form[...]`.
- Unauthenticated JSON unit APIs are protected (`/api/unit_types` → 401).
- Custom domain and `*.storageunitsoftware.com` both serve the facility site.
- Homepage screenshot artifact: `/opt/cursor/artifacts/discovery/home.png` (agent run artifact; not necessarily committed).

---

## 13. Change log

| Date | Change |
|---|---|
| 2026-08-02 | Phase 1 public discovery written |
| 2026-08-02 | Phase 2 live admin/report inventory completed (read-only); password shared in chat — rotate immediately |
| 2026-08-02 | Added Epic P9 / §7A: multi-tenant SaaS, site templates, and subscription sellability requirements |
