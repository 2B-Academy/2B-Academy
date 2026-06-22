# NAS Smart LMS — Mobile UX Business Document
**Employee Mobile App · UX Screen & Flow Specification**
*Version 1.2 · May 2026 · Audience: Product, Design, Engineering*

---

> **How to read this document**
> Every screen section follows the same structure: JTBD → Screen anatomy → Heuristics applied → Heuristics at risk → RTL notes → Accessibility requirements. Nothing ships without all five fields answered.

---

## Table of Contents

1. [Principles & Constraints](#1-principles--constraints)
2. [App Structure & Entry Points](#2-app-structure--entry-points)
3. [Screen Inventory](#3-screen-inventory)
4. [S-01 · Services — Academy Entry Card](#s-01--services--academy-entry-card)
5. [S-02 · Academy — Course List](#s-02--academy--course-list)
6. [S-03 · Course Detail & Enrolment](#s-03--course-detail--enrolment)
7. [S-04 · Enrolment Confirmation](#s-04--enrolment-confirmation)
8. [S-05 · Profile — My Learning](#s-05--profile--my-learning)
9. [S-06 · Attendance — Mark Present & Passcode](#s-06--attendance--mark-present--passcode)
10. [S-07 · Certificates](#s-07--certificates)

---

## 1. Principles & Constraints

### 1.1 What This App Is
The NAS Smart LMS is a **learning module embedded inside the existing NAS HR mobile app**. It is not a standalone product and must never feel like one. Every design decision must reinforce the user's existing mental model of the NAS app — not introduce a new one.

### 1.2 Audience — Employees Only
The mobile app is exclusively for employees. Instructors, admins, and super admins operate through the web dashboard. Never mix these audiences. Never expose admin controls, grading interfaces, or content management to this surface.

### 1.3 The Two Entry Points Rule
The LMS is accessible from two places in the existing NAS app, each carrying a fundamentally different user intent:

| Entry | Location | Icon Label | User Intent | Mental Model |
|---|---|---|---|---|
| **Primary** | Services section | **Academy** | Discovery & enrolment — "what can I join?" | Browse, choose, apply |
| **Secondary** | Profile section | **My Learning** | Attendance & achievement — "am I present, and what have I earned?" | Check attendance, mark presence in live sessions, view certificates |

One design language serves both. The content and emphasis are distinct — the components and patterns are shared.

### 1.4 Non-Negotiable UX Constraints

- **WCAG 2.1 AA minimum** on all screens — no exceptions
- **Arabic RTL is a first-class layout** — not a post-launch retrofit
- **Fira Sans** for English · **Almarai** for Arabic — never mixed within a text block
- **Design tokens only** — no hardcoded hex values, no inline styles
- **Progressive disclosure by default** — start with the essential, reveal depth on demand

---

## 2. App Structure & Entry Points

### 2.1 LMS Within the Existing Navigation

The NAS app's existing bottom navigation and section structure is not modified by the LMS. The LMS anchors itself within two already-existing sections.

```
NAS App
├── Services ─────────────────── PRIMARY LMS ENTRY
│   ├── Permission Request
│   ├── Vacation Request
│   ├── Internal Jobs
│   ├── [Academy]  ◄── Employee browses & enrols here
│   └── ...
│
├── Notifications
├── Chat Bot
│
└── Profile ──────────────────── SECONDARY LMS ENTRY
    ├── Salary Overview
    ├── Attendance Report
    ├── Company Policies
    ├── [My Learning]  ◄── Employee checks attendance & views certificates here
    └── ...
```

### 2.2 Why Two Entry Points Exist — and What Each Does

**Services → Academy (Discovery Intent)**
The employee is in "explore and act" mode. The Academy screen shows all available courses — open cohorts the employee can browse, read the details of, and enrol in instantly. Enrolment is confirmed immediately. When a cohort reaches capacity, it is removed from the list — the employee only ever sees courses they can still join. This entry point is never about progress — it is about what is available and whether the employee has a seat.

**Profile → My Learning (Attendance & Achievement Intent)**
The employee is in "confirm and collect" mode. My Learning shows their active courses with session attendance status, the attendance passcode flow anchored inside each active course card, and the certificates they have already earned. This entry point is not about progress tracking or course discovery — it is about marking presence in live sessions and accessing earned certificates.

---

## 3. Screen Inventory

| ID | Screen | Entry Point | Primary Action |
|---|---|---|---|
| S-01 | Services — Academy Entry Card | Services | Tap → Course List |
| S-02 | Academy — Course List | Services → Academy | Browse available courses |
| S-03 | Course Detail & Enrolment | Course List | View details / Request enrolment |
| S-04 | Enrolment Confirmation | Course Detail → Enrol | See confirmed seat |
| S-05 | Profile — My Learning | Profile | View active progress, qualifications, certificates |
| S-06 | Attendance — Mark Present & Passcode | My Learning → active course card | Enter passcode to confirm attendance |
| S-07 | Certificates | My Learning → Certificates | View / Download certificate |

---

## S-01 · Services — Academy Entry Card

### JTBD
> *When I open the Services section, I want to see that there are courses available for me, so I can quickly enter and browse what I can enrol in.*

### Screen Anatomy

The Academy entry in Services is a **single tappable card** — consistent with every other service card like Permission Request and Vacation Request. It is a door, not a dashboard.

```
┌─────────────────────────────────────────────┐
│  🎓  Academy                                │
│  ─────────────────────────────────────────  │
│                                             │
└─────────────────────────────────────────────┘
```

**Card elements:**
- **Icon + label** — outlined, 20×20px, stroke 1.7 — identical style to all other Services cards
- Label is **"Academy"** — not "Courses", not "eLearning"

**When no courses are open:**
- When the user enters the Academy card they will see an empty state

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Course availability count gives instant orientation before the user taps in |
| 2 | Match system & real world | "Academy" — institutional, familiar language matching NAS's internal brand |
| 4 | Consistency & standards | Card shell, icon style, and CTA pattern identical to all other Services cards |
| 8 | Aesthetic & minimalist design | Single label — no progress bars or history here (those belong in Profile → My Learning) |

### Heuristics at Risk

| # | Heuristic | Risk | Mitigation |
|---|---|---|---|
| 1 | Visibility of system status | Card could show a stale count if sync is slow on app open | Skeleton loader on the count during fetch; never show a blank card |

### RTL Notes
- Icon shifts to the right side of the card header
- Card layout mirrors horizontally
- "أكاديمية" is the Arabic label

### Accessibility
- Entire card is a single focusable element: `role="button"`, `aria-label="Academy"`
- Status line is plain text only — no colour-only states

---

## S-02 · Academy — Course List

### JTBD
> *When I enter the Academy, I want to see all courses currently available, so I can find one relevant to me and decide whether to apply.*

### Screen Anatomy

```
┌─────────────────────────────────────────────┐
│  ←         Academy                          │
│─────────────────────────────────────────────│
│  🔍 Search courses...                       │
│─────────────────────────────────────────────│
│  [ All · 4 ]  [ Special Courses · 3 ]  [ General... ] │
│─────────────────────────────────────────────│
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [thumbnail]  Advanced Excel          │  │
│  │               for Finance             │  │
│  │               Qualifications · GDPR Awareness  +2 │
│  │               ★ 4.8  📅 10 Jun  ⊞ Online │
│  └───────────────────────────────────────┘  │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [thumbnail]  Business Writing        │  │
│  │               Essentials              │  │
│  │               Qualifications · For All │  │
│  │               ★ 3.2  📅 10 Jul  ⊞ Hybrid │
│  └───────────────────────────────────────┘  │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [thumbnail]  Workplace Safety &      │  │
│  │               Compliance              │  │
│  │               Qualifications · GDPR Awareness │
│  │               ★ ---  📅 10 Aug  ⊞ Online │
│  └───────────────────────────────────────┘  │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [thumbnail]  Project Management      │  │
│  │               Fundamentals            │  │
│  │               Qualifications · GDPR Awa...  [Development] │
│  │               ★ 4.8  📅 10 Sep  ⊞ Offline │
│  └───────────────────────────────────────┘  │
│                                             │
└─────────────────────────────────────────────┘
```

**Page header:**
- Screen title: **"Academy"** — centred, nav-bar weight
- Back arrow `←` navigates to Services

**Search bar:**
- Full-width pill input, always visible at the top of the list
- Placeholder: "Search courses..."
- Searches across title, qualification tags, and category
- Inline results as the user types — no submit button

**Filter chips (tab pills):**
- Displayed as a horizontal scrollable row immediately below search
- Each chip shows its label and a count badge: `All · 4`, `Special Courses · 3`, `General · …`
- Active chip: filled dark background, white label — inactive chips: outlined
- Default active filter on arrival: **All**
- Single-select — tapping a chip filters the list instantly, no confirmation needed
- Chip count reflects the number of courses in that category

**Course cards:**

Each card is a full-width tappable row with a fixed-height thumbnail on the left and a text block on the right.

| Slot | Content | Notes |
|---|---|---|
| Thumbnail | Square image, rounded corners | Course cover or category illustration |
| Title | Course name, 2-line max | Bold, wraps onto second line if needed |
| Qualification tag line | "Qualifications · [Category name]" | Secondary text, muted; multiple categories truncated with `+N` overflow badge |
| `+N` overflow badge | Teal-bordered pill showing remaining category count | Only shown when ≥2 additional categories exist beyond the visible one |
| Rating | Star icon + numeric score (e.g. `★ 4.8`) | If unrated: `★ ---` |
| Date | Calendar icon + `DD Mon` (e.g. `📅 10 Jun`) | Next cohort start date |
| Delivery format | Grid icon + `Online` / `Hybrid` / `Offline` | Delivery method for the next cohort |

Cards are tappable in their entirety → navigates to S-03 (Course Detail). No secondary CTA button on the card.

**What this screen does NOT show:**
- The employee's own progress (that belongs in Profile → My Learning)
- Courses the employee has already completed
- Any administrative controls
- Enrolment status badges (shown on S-03 post-action)

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Chip count badges show total courses per category at a glance; `★ ---` explicitly signals "not yet rated" rather than hiding the field |
| 2 | Match system & real world | "Special Courses" and "General" map to how NAS categorises training internally — not edtech taxonomy |
| 6 | Recognition over recall | Rating, date, and delivery format on every card — employee decides without opening each course |
| 7 | Flexibility & efficiency | Search + filter chips serve goal-directed users (know what they want) and browsers equally |
| 8 | Aesthetic & minimalist design | `+N` overflow keeps the tag line to one row — avoids card height explosion from multi-category courses |

### Heuristics at Risk

| # | Heuristic | Risk | Mitigation |
|---|---|---|---|
| 8 | Aesthetic & minimalist design | Long course titles + overflow badge can still make cards feel dense | Enforce 2-line title clamp; `+N` badge prevents tag line overflow |
| 5 | Error prevention | Enrolment deadline could pass while the user is browsing | Deadline warnings shown at card level if deadline is ≤3 days away |
| 1 | Visibility of system status | Filter chip counts could go stale if loaded once at page open | Counts refresh on filter tap; skeleton state during fetch |

### RTL Notes
- Search icon and cursor align to the right; placeholder text right-aligned
- Filter chips scroll left-to-right in LTR; right-to-left in RTL
- Card: thumbnail on right, text block on left
- `+N` badge appears to the left of the qualification tag line
- Rating · Date · Delivery read right-to-left: `أونلاين ⊞  يونيو 10 📅  4.8 ★`

### Accessibility
- Search: `role="searchbox"`, `aria-label="Search courses"`, results announced via `aria-live="polite"`
- Filter chips: `role="tab"` within a `role="tablist"`, `aria-selected` per chip
- Course cards: `role="button"`, announced as "[Title], Qualifications: [tags], Rating [X] out of 5, Next cohort [date], [Delivery format]"
- `+N` overflow badge: `aria-label="and [N] more qualification categories"`
- Unrated courses: `aria-label="Not yet rated"` on the `★ ---` element

---

## S-03 · Course Detail & Enrolment

### JTBD
> *When I tap a course I am interested in, I want to understand what it covers, how long it takes, and when the next cohort runs, so I can confirm my enrolment while a seat is still available.*

### Screen Anatomy

```
┌─────────────────────────────────────────────┐
│  ← Courses                                  │
│─────────────────────────────────────────────│
│  [Full-width course cover image]            │
│─────────────────────────────────────────────│
│  Advanced Excel for Finance                 │
│  Technical · Finance Department             │
│                                             │
│  ★ 4.8  (96 ratings)   8h total   12 units  │
│  Instructor: Ahmad Youssef                  │
│                                             │
│  NEXT COHORT ──────────────────────────── │
│  📅 Starts: 10 June 2026                   │
│  ⏳ Enrolment closes: 5 June 2026           │
│  👥 14 seats remaining                      │
│                                             │
│  ─────────────────────────────────────────  │
│  ABOUT THIS COURSE                          │
│  Master advanced Excel functions for        │
│  financial modelling and reporting...       │
│  [ Show more ↓ ]                            │
│                                             │
│  WHAT YOU'LL LEARN                          │
│  ✓ Advanced formulas and data validation    │
│  ✓ Financial modelling techniques           │
│  ✓ PivotTable and PivotChart mastery        │
│  ✓ Dashboard design for reporting           │
│                                             │
│  COURSE UNITS (12 total)                    │
│  1  Introduction & Setup          15 min    │
│  2  Data Structures for Finance   22 min    │
│  3  Lookup Functions Deep Dive    28 min    │
│  [ View all 12 units ↓ ]                    │
│─────────────────────────────────────────────│
│  [ Request Enrolment ]   ← sticky CTA       │
└─────────────────────────────────────────────┘
```

**Next Cohort block:**
- Always the first content block below the course header — the primary decision-relevant information
- Shows: start date, enrolment deadline, seats remaining
- **When seats reach 0: the course card is removed from the Academy list entirely — the employee never sees a full cohort or a waitlist prompt**
- If enrolment closed: "Enrolment closed — next cohort date TBA"

**About section:**
- 3-line preview by default, "Show more" expands inline — never auto-expanded

**Course Units:**
- First 3 units shown with duration and content type (Video / Reading / Quiz / Assignment / Live)
- "View all 12 units ↓" expands the full list

**Sticky CTA states:**

| State | CTA Label | Notes |
|---|---|---|
| Available, seats open | "Enrol Now" | Instant — no review step |
| Enrolment closed | "Get Notified for Next Cohort" | Deadline has passed |
| Already enrolled | "Enrolled ✓ — view in My Learning" | Disabled, teal |

**Cohort full state is never shown.** When the last seat is taken, the course is hidden from the Academy list on the server side. An employee who navigates directly to a now-full course detail (e.g. via a stale link) will see the course removed and be returned to the Academy list with no error state needed.

**Enrolment confirmation bottom sheet (on "Enrol Now" tap):**

```
┌─────────────────────────────────────────────┐
│  Confirm enrolment?                         │
│                                             │
│  Advanced Excel for Finance                 │
│  Cohort starting 10 June 2026               │
│                                             │
│  You'll be enrolled immediately.            │
│  This course will appear in My Learning.    │
│                                             │
│  [ Cancel ]              [ Enrol ]          │
└─────────────────────────────────────────────┘
```

After confirming, navigates directly to S-04 Enrolled confirmation. There is no pending or review state.

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Cohort block communicates current availability state at all times |
| 3 | User control & freedom | User can browse without committing; back is always reachable |
| 5 | Error prevention | Confirmation bottom sheet before request is submitted; CTA disabled when already requested |
| 6 | Recognition over recall | All relevant decision data on one screen |
| 8 | Aesthetic & minimalist design | Progressive disclosure on About and Units — not overwhelming by default |

### Heuristics at Risk

| # | Heuristic | Risk | Mitigation |
|---|---|---|---|
| 1 | Visibility of system status | Last seat taken by another employee while this user is reading the detail | Seat count refreshes on page focus; if seats reach 0 while the user is on this screen, CTA is replaced with "This cohort is now full" and the user is returned to the Academy list on next back navigation |
| 9 | Help recover from errors | Enrolment API failure must not leave the user uncertain | Inline error: "Enrolment failed — tap to try again". Never silently fail. |

### RTL Notes
- Cover image stays full-width — no directional change
- Next Cohort block: icons and text align right
- Sticky CTA is full-width — no directional impact
- Confirmation sheet: button order reverses in RTL (Confirm on left, Cancel on right)

### Accessibility
- Cover image: `alt="[Course title] course cover"`
- Cohort block: announced as "Next cohort starts [date], enrolment closes [date], [N] seats remaining"
- Sticky CTA: `aria-label` reflects current state
- Bottom sheet: `role="dialog"`, `aria-modal="true"`, focus trapped inside while open

---

## S-04 · Enrolment Confirmation

### JTBD
> *After enrolling, I want immediate confirmation that I have a seat, so I can plan for the course start date with certainty.*

### Business Logic
Enrolment is **instant and automatic**. There is no review step, no approval queue, and no decline state. The system accepts every employee who taps Enrol until the cohort's seat capacity is reached. When capacity is reached, the course is hidden from the Academy list server-side — employees who haven't enrolled yet simply never see it. There is no waitlist, no "cohort full" screen, and no pending state.

### Screen Anatomy — Enrolled

```
┌─────────────────────────────────────────────┐
│  ← Academy                                  │
│─────────────────────────────────────────────│
│                                             │
│         ✅  You're enrolled                 │
│                                             │
│  Advanced Excel for Finance                 │
│  Cohort: 10 June 2026                       │
│                                             │
│  You have a confirmed seat. This course     │
│  will appear in My Learning once it         │
│  starts.                                    │
│                                             │
│  📅  Add to calendar                        │
│                                             │
│  [ Back to Academy ]                        │
└─────────────────────────────────────────────┘
```

**This is the only state this screen has.** Pending and declined states do not exist.

**Re-entry:** If the employee taps a course they are already enrolled in from the Academy list, the S-03 CTA reads "Enrolled ✓ — view in My Learning" and navigates to My Learning, not back to this screen.

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Unambiguous confirmation — icon + heading + plain-language body |
| 2 | Match system & real world | "You have a confirmed seat" — direct, human language. No system jargon. |
| 3 | User control & freedom | "Add to calendar" is optional — forward action, not mandatory |

### Heuristics at Risk

| # | Heuristic | Risk | Mitigation |
|---|---|---|---|
| 1 | Visibility of system status | Race condition: two employees enrol simultaneously for the last seat | Server processes first-come-first-served; the second employee receives an API error caught by S-03's error handler — "Enrolment failed — this cohort just filled up" — and the course is removed from their Academy list on refresh |

### RTL Notes
- Status icon and heading are centre-aligned — no directional impact
- Body text right-aligns in Arabic
- CTA button is full-width — no directional impact

### Accessibility
- Status heading: `role="alert"` so it's announced immediately on navigation
- "Add to calendar": `aria-label="Add Advanced Excel cohort start date to calendar"`

---

## S-05 · Profile — My Learning

### JTBD
> *When I check my profile, I want to see my active courses and my progress in them, the qualifications I need to gain for my role, and the certificates I have already earned, so I can understand my development status at a glance.*

### Screen Anatomy

```
┌─────────────────────────────────────────────┐
│  Profile  ·  My Learning                   │
│─────────────────────────────────────────────│
│                                             │
│  ACTIVE COURSES ───────────────────────── │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [thumb]  Advanced Excel for Finance  │  │
│  │  ████████████░░░░  67% · 8 of 12 done │  │
│  │  Next: Lookup Functions Deep Dive     │  │
│  │  Last accessed: Yesterday             │  │
│  │                                       │  │
│  │  [ Continue ]      [ Mark Present ]   │  │
│  └───────────────────────────────────────┘  │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [thumb]  Business Writing Essentials │  │
│  │  ████░░░░░░░░░░░░  25% · 3 of 12 done │  │
│  │  Next: Report Structure               │  │
│  │  ⚠  Due 10 June — 8 days remaining   │  │
│  │                                       │  │
│  │  [ Continue ]      [ Mark Present ]   │  │
│  └───────────────────────────────────────┘  │
│                                             │
│  MY QUALIFICATIONS ─────────────────────── │
│  Required for your role:                   │
│                                             │
│  Financial Reporting       ████████  80%   │
│  Data Analysis             ██████░░  60%   │
│  Business Communication    ████░░░░  40%   │
│  Compliance Fundamentals   ████████  95%   │
│                                             │
│  [ See all qualifications → ]              │
│                                             │
│  MY CERTIFICATES ──────────────────────── │
│  ┌───────────────────────────────────────┐  │
│  │  🏅  Workplace Safety Fundamentals    │  │
│  │      Issued: 2 February 2026          │  │
│  │                    [ View → ]         │  │
│  └───────────────────────────────────────┘  │
│  [ See all certificates → ]                │
└─────────────────────────────────────────────┘
```

### Active Courses Section

Each active course card shows:
- Thumbnail + title
- Progress bar: percentage and unit count ("8 of 12 done")
- Next unit name — the employee knows exactly what comes next without tapping in
- Last accessed timestamp — orientation cue for returning users
- Deadline warning if applicable (⚠ orange ≤7 days, 🔴 red ≤2 days)
- Two CTAs per card:
  - **"Continue"** → opens the course at the last saved position
  - **"Mark Present"** → opens the attendance passcode flow (S-06) as a bottom sheet

**"Mark Present" visibility rule:**
Rendered only when the course has a live or in-person session scheduled for today AND the session attendance window is currently open. Hidden at all other times — never a permanent button on every card.

### My Qualifications Section

- Lists qualifications the employee must gain for their role
- Each row: qualification name + progress bar showing % complete based on course completions toward that qualification
- Default: top 4 qualifications visible; "See all qualifications →" expands the full list
- Progress is derived from course completion data — never manually entered

### My Certificates Section

- Most recent 2 certificates shown by default, most recent first
- Each entry: course/qualification name, issue date, "View →" → navigates to S-07
- "See all certificates →" navigates to the full S-07 screen

**Empty states:**
- No active courses: "No active courses yet — browse what's available in Academy" + link to Services → Academy
- No qualifications configured for role: section hidden entirely
- No certificates: "No certificates earned yet — complete a course to get started"

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Progress bars, %, next unit, and deadlines communicate current state immediately |
| 2 | Match system & real world | "My Qualifications" and "My Certificates" match how employees talk about development |
| 6 | Recognition over recall | Next unit name and last-accessed timestamp surface context — no memory required |
| 7 | Flexibility & efficiency | "Continue" resumes from exact last position — zero friction for the most common return action |
| 8 | Aesthetic & minimalist design | Three sections, each capped at a default count — no infinite scroll or data overload |

### Heuristics at Risk

| # | Heuristic | Risk | Mitigation |
|---|---|---|---|
| 8 | Aesthetic & minimalist design | Employee with many active courses could see an overwhelming list | Cap visible active course cards at 3 with a "Show more" toggle |
| 5 | Error prevention | "Mark Present" appearing when no session is active would confuse the user | Button only rendered during an open attendance window — server-side rule, not a CSS toggle |

### RTL Notes
- Progress bars fill right-to-left
- Qualification labels align right; bars fill from right
- "Continue" and "Mark Present" button order reverses in RTL card layout
- Certificate entries: issue date aligns right

### Accessibility
- Progress bars: `role="progressbar"`, `aria-valuenow`, `aria-valuemin="0"`, `aria-valuemax="100"`, `aria-label="[Course title] progress, [N]%"`
- "Mark Present": `aria-label="Mark attendance for [Course title]"` — only present in the DOM when active, not hidden with CSS
- Qualification bars: each announced as "[Qualification name], [N]% complete"

---

## S-06 · Attendance — Mark Present & Passcode

### JTBD
> *When a session is live, I want to confirm my attendance quickly by entering the instructor's passcode, so my presence is recorded without any friction or follow-up needed.*

### Entry Point

Reachable only from the **"Mark Present" button** on an active course card in My Learning (S-05). Opens as a **bottom sheet** — the employee does not leave the My Learning screen.

The "Mark Present" button only appears during an open attendance window. This screen is therefore never reachable at the wrong time.

### Screen Anatomy — Passcode Entry (Bottom Sheet)

```
┌─────────────────────────────────────────────┐
│  Mark Attendance                     ╳     │
│─────────────────────────────────────────────│
│  Advanced Excel for Finance                 │
│  Session 2 · Today, 10:00 AM                │
│                                             │
│  Enter the code shown by your instructor:   │
│                                             │
│       ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐        │
│       │  │  │  │  │  │  │  │  │  │        │
│       └──┘  └──┘  └──┘  └──┘  └──┘        │
│                                             │
│           [ 1 ] [ 2 ] [ 3 ]                 │
│           [ 4 ] [ 5 ] [ 6 ]                 │
│           [ 7 ] [ 8 ] [ 9 ]                 │
│           [ ⌫ ] [ 0 ] [ ✓ ]                 │
│                                             │
│  Code expires at 10:30 AM                   │
└─────────────────────────────────────────────┘
```

**Interaction behaviour:**
- 5 large, clearly separated digit input boxes
- In-sheet numeric keypad — no system keyboard opens
- Confirm (✓) activates automatically when the 5th digit is entered, or can be tapped manually
- Backspace (⌫) clears the last digit

### Confirmed State

```
┌─────────────────────────────────────────────┐
│  ✅  Attendance Confirmed              ╳   │
│─────────────────────────────────────────────│
│  Advanced Excel for Finance                 │
│  Session 2 · Today, 10:00 AM                │
│                                             │
│  Your attendance has been recorded.         │
│                                             │
│          [ Done ]                           │
└─────────────────────────────────────────────┘
```

Sheet auto-dismisses after 3 seconds, or the employee taps "Done". Returns to My Learning.

### Error States

**Wrong code:**
```
⚠  That code doesn't match. Check with your instructor and try again.
[Input fields reset — employee re-enters]
```

**Expired code:**
```
⚠  This code has expired. Ask your instructor to reissue it.
[Confirm button disabled until a valid new code is entered]
```

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Code expiry time always visible; confirmed state is immediate and unambiguous |
| 3 | User control & freedom | ╳ dismisses the sheet at any point without consequences |
| 5 | Error prevention | Large digit fields reduce misentry; in-sheet keypad avoids autocorrect interference |
| 9 | Help recover from errors | Wrong code and expired code each have specific, actionable messages — never a generic error |

### Heuristics at Risk

| # | Heuristic | Risk | Mitigation |
|---|---|---|---|
| 5 | Error prevention | Employee might attempt to confirm without being present | Attendance window is time-limited and instructor-controlled — this is the system's enforced boundary |

### RTL Notes
- Digit input boxes are directional-neutral (numbers are always LTR regardless of app language)
- Error and confirmation messages render in Arabic in RTL mode
- Sheet header and body text right-align
- ╳ dismiss button shifts to left side of the sheet header in RTL

### Accessibility
- Each digit input: `aria-label="Attendance code digit [N] of 5"`
- Confirmation: `role="status"`, announced immediately
- Error messages: `role="alert"`, announced immediately on appearance
- Keypad buttons: `aria-label="[digit]"`; ⌫ is "Delete last digit"; ✓ is "Confirm attendance code"

---

## S-07 · Certificates

### JTBD
> *When I want to view or share a certificate I have earned, I want to find it quickly in my profile and download it as a PDF, so I can keep a record and use it where it matters.*

### Screen Anatomy

```
┌─────────────────────────────────────────────┐
│  ← My Learning    Certificates              │
│─────────────────────────────────────────────│
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [NAS-branded certificate thumbnail]  │  │
│  │  Advanced Excel for Finance           │  │
│  │  Issued: 14 April 2026 · ID: NAS-4821 │  │
│  │                                       │  │
│  │  [ View ]          [ Download PDF ]   │  │
│  └───────────────────────────────────────┘  │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │  [NAS-branded certificate thumbnail]  │  │
│  │  Workplace Safety Fundamentals        │  │
│  │  Issued: 2 Feb 2026 · ID: NAS-3104    │  │
│  │                                       │  │
│  │  [ View ]          [ Download PDF ]   │  │
│  └───────────────────────────────────────┘  │
└─────────────────────────────────────────────┘
```

**"View" tap:** Full-screen in-app PDF viewer, pinch-to-zoom supported.

**"Download PDF" tap:** Triggers the native OS share sheet — the employee controls where the file is saved (Files, email, etc.).

**Certificate content:** Employee name, course title, completion date, NAS branding, instructor name, credential ID. Bilingual: Arabic on one side, English on the other.

**Empty state:**
```
  No certificates yet.
  Complete a course to earn your first.
```

### Heuristics Applied

| # | Heuristic | Application |
|---|---|---|
| 1 | Visibility of system status | Issue date and credential ID on every card — no ambiguity about when or what was earned |
| 3 | User control & freedom | View and download are separate actions — employee decides what to do with the certificate |
| 8 | Aesthetic & minimalist design | Compact cards; no metadata beyond what identifies the certificate |

### RTL Notes
- Certificate document: always bilingual — Arabic right side, English left side
- Card layout: thumbnail on right, text and CTAs on left in RTL
- Issue date and credential ID align right

### Accessibility
- Certificate thumbnail: `alt="[Course name] certificate"`
- Download button: `aria-label="Download [Course name] certificate as PDF"`

---

*NAS Smart LMS — Mobile UX Business Document*
*Version 1.2 · May 2026*
*Authored against: CLAUDE_v2.md · SSOT.md · lms-ux-knowledge-base.md · NAS_DesignSystem_Prompt_v2.md*
*Nielsen's 10 Heuristics applied to every screen. JTBD defined for every feature. RTL-first throughout.*
