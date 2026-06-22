# Laravel Enterprise Feature Implementation Prompt

## Qualification Skills Integration for Courses (MVC + APIs + Angular Compatible + Localized Fields)

You are a senior Laravel architect and enterprise full-stack engineer.

I have an existing Laravel MVC application that is currently being migrated gradually to APIs + Angular.

Your task is to implement a new feature in a production-grade, scalable, maintainable, and optimized way while preserving ALL existing functionality and keeping full backward compatibility.

---

# IMPORTANT RULES

Before implementing anything:

1. Analyze the entire project structure first
2. Understand existing architecture and conventions
3. Reuse existing patterns/components/services/resources/helpers
4. Follow existing naming conventions
5. Preserve ALL existing MVC functionality
6. Preserve ALL existing UI/UX styling
7. Avoid regressions completely
8. Keep implementation scalable and reusable
9. Optimize performance everywhere
10. Do NOT introduce unnecessary libraries/packages
11. Keep controllers thin
12. Keep business logic outside controllers/views
13. Follow SOLID principles
14. Follow clean architecture principles
15. Ensure Angular compatibility for APIs
16. Follow the existing localization architecture already used in the project

---

# FEATURE REQUIREMENT

Create a new entity named:

```sql
qualification_skills
```

A Course can have multiple Qualification Skills.

This relationship must work correctly in BOTH:

1. Existing MVC flows
2. Existing/New API flows

The feature must be fully synchronized between both systems.

---

# LOCALIZATION REQUIREMENT (VERY IMPORTANT)

The qualification skill name is localized.

The database MUST support:

- English value
- Arabic value

The APIs MUST return the localized value based on the current language/culture/header exactly like the existing project localization behavior.

DO NOT return:

```json
{
    "name": {
        "en": "Communication",
        "ar": "التواصل"
    }
}
```

Instead return ONLY the localized value depending on current request language.

Example when locale is `en`:

```json
{
    "id": 1,
    "name": "Communication"
}
```

Example when locale is `ar`:

```json
{
    "id": 1,
    "name": "التواصل"
}
```

IMPORTANT:
You MUST analyze how localization currently works in the project and follow the exact same implementation style already used.

Examples:

- spatie translatable
- custom JSON localization
- separate columns
- translation tables
- accessor-based localization
- API resource localization
- header-based localization

Reuse existing localization architecture completely.

---

# DATABASE REQUIREMENTS

## Create Table: qualification_skills

Required columns:

| Column | Type            |
| ------ | --------------- |
| id     | bigint          |
| name   | localized field |

Also include:

- timestamps
- soft deletes

ONLY if the project already uses them globally.

---

# LOCALIZATION STORAGE REQUIREMENTS

IMPORTANT:
Before implementation, analyze how existing localized fields are stored in the project.

Possible existing patterns:

## Option 1: JSON Column

```php
$table->json('name');
```

Stored example:

```json
{
    "en": "Communication",
    "ar": "التواصل"
}
```

---

## Option 2: Separate Columns

```php
$table->string('name_en');
$table->string('name_ar');
```

---

## Option 3: Translation Table

Use existing translation architecture if project already has one.

---

# RULE

DO NOT invent a new localization architecture.

Reuse the existing localization system already implemented in the project.

---

# MANY TO MANY RELATIONSHIP

Create pivot table:

```sql
course_qualification_skills
```

---

# Pivot Table Requirements

Columns:

| Column                 | Type      |
| ---------------------- | --------- |
| course_id              | foreignId |
| qualification_skill_id | foreignId |

Requirements:

- Foreign keys
- Cascade delete where appropriate
- Composite unique constraint
- Indexed properly
- Optimized for performance

Example:

```php
Schema::create('course_qualification_skills', function (Blueprint $table) {

    $table->foreignId('course_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('qualification_skill_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unique([
        'course_id',
        'qualification_skill_id'
    ]);
});
```

---

# ELOQUENT MODEL REQUIREMENTS

Update/Create models properly.

## Models

- Course
- QualificationSkill

---

# Relationships

## Course Model

```php
public function qualificationSkills()
{
    return $this->belongsToMany(
        QualificationSkill::class,
        'course_qualification_skills'
    );
}
```

---

## QualificationSkill Model

```php
public function courses()
{
    return $this->belongsToMany(
        Course::class,
        'course_qualification_skills'
    );
}
```

---

# LOCALIZED ACCESSORS REQUIREMENTS

Implement localized accessors/resources exactly like the existing project style.

Example only if project uses accessors:

```php
public function getNameAttribute($value)
{
    return LocalizationHelper::getLocalizedValue($value);
}
```

DO NOT duplicate localization logic if project already has helpers/services/traits.

Reuse existing implementation.

---

# MVC FLOW REQUIREMENTS

Update ALL related MVC flows.

This includes:

- Create Course page
- Edit Course page
- Course details page if necessary
- Course listing if necessary

---

# UI REQUIREMENTS

Add a multi-select field for qualification skills.

Requirements:

- Preserve existing design EXACTLY
- Preserve existing CSS/theme/layout
- No UI regression
- Match existing form styling
- Responsive behavior
- Validation support
- old() support
- Edit mode preselected values
- Reusable Blade component if project architecture supports it

---

# LOCALIZED UI REQUIREMENTS

In MVC:

- Display Arabic names when locale is Arabic
- Display English names when locale is English

Follow existing localization mechanism already used in the project.

---

# IMPORTANT UI RULES

If the project already uses:

- Select2
- TomSelect
- Choices.js
- Alpine components
- Livewire components

THEN reuse existing libraries/components.

DO NOT introduce unnecessary frontend dependencies.

---

# MVC CONTROLLER REQUIREMENTS

Update:

- store()
- update()

Requirements:

- Controllers must remain thin
- Use Form Requests if project already uses them
- Use Services/Actions if project already uses them
- Use transactions where appropriate
- Proper sync handling
- Proper add/remove handling

Example:

```php
$course->qualificationSkills()->sync(
    $request->qualification_skill_ids ?? []
);
```

---

# VALIDATION REQUIREMENTS

Create proper validation rules.

Requirements:

- Validate localized values
- Validate array structure
- Validate integer IDs
- Validate IDs exist
- Prevent duplicate IDs
- Handle null safely
- Handle malformed requests safely
- Clean validation messages

Example:

```php
'name.en' => ['required', 'string'],
'name.ar' => ['required', 'string'],
```

OR follow existing localization validation architecture already used in the project.

---

# API REQUIREMENTS

Create RESTful APIs for qualification skills.

---

# QUALIFICATION SKILLS ENDPOINTS

## Get All Skills

```http
GET /api/qualification-skills
```

Requirements:

- Lightweight response
- Pagination-ready
- Search-ready architecture
- Optimized query
- Return localized name only

---

## Create Skill

```http
POST /api/qualification-skills
```

Example Request:

```json
{
    "name": {
        "en": "Communication",
        "ar": "التواصل"
    }
}
```

OR follow existing project localization request structure.

---

## Update Skill

```http
PUT /api/qualification-skills/{id}
```

---

## Delete Skill

```http
DELETE /api/qualification-skills/{id}
```

Requirements:

- Validate existence
- Prevent invalid deletion scenarios if needed
- Proper error responses

---

# COURSE API REQUIREMENTS

Update Course APIs to support qualification skills.

---

# REQUEST EXAMPLE

```json
{
    "title": "Course Name",
    "description": "Course Description",
    "qualification_skill_ids": [1, 2, 3]
}
```

---

# RESPONSE EXAMPLE

## English Response

```json
{
    "id": 1,
    "title": "Course Name",
    "qualification_skills": [
        {
            "id": 1,
            "name": "Communication"
        }
    ]
}
```

---

## Arabic Response

```json
{
    "id": 1,
    "title": "اسم الكورس",
    "qualification_skills": [
        {
            "id": 1,
            "name": "التواصل"
        }
    ]
}
```

---

# API RESPONSE STANDARDS

Follow existing API response structure.

If project has:

- API Resources
- Transformers
- DTOs
- BaseResponse classes

THEN reuse them.

Responses must be:

- Consistent
- Angular-friendly
- Predictable
- Lightweight
- Pagination-ready
- Localized correctly

---

# HTTP STATUS CODE REQUIREMENTS

Use correct HTTP status codes everywhere.

Examples:

| Status | Usage                 |
| ------ | --------------------- |
| 200    | Success               |
| 201    | Created               |
| 400    | Bad Request           |
| 401    | Unauthorized          |
| 403    | Forbidden             |
| 404    | Not Found             |
| 409    | Conflict              |
| 422    | Validation Errors     |
| 500    | Internal Server Error |

NEVER return incorrect status codes.

---

# PERFORMANCE REQUIREMENTS

Everything must be optimized for performance.

Requirements:

- Avoid N+1 queries
- Use eager loading properly
- Use select() where appropriate
- Avoid unnecessary columns
- Add DB indexes
- Use transactions where appropriate
- Optimize sync operations
- Avoid duplicated queries
- Keep responses lightweight
- Use pagination-ready architecture

Example:

```php
Course::with([
    'qualificationSkills:id,name'
]);
```

---

# CLEAN ARCHITECTURE REQUIREMENTS

Follow enterprise Laravel standards.

Use existing project patterns if available:

- Services
- Actions
- Repositories
- DTOs
- Resources
- Traits
- Base Controllers
- Shared Components

---

# AVOID THE FOLLOWING

DO NOT:

- Create fat controllers
- Put business logic in Blade
- Duplicate localization logic
- Hardcode values
- Break existing flows
- Change existing styling unnecessarily
- Introduce unnecessary packages
- Create unoptimized queries

---

# ANGULAR COMPATIBILITY

The APIs must be designed for Angular consumption.

Requirements:

- Stable response structure
- Predictable validation format
- Consistent naming
- Frontend-friendly payloads
- Pagination-ready structure
- Reusable response shape
- Proper localization behavior

---

# TESTING & VERIFICATION REQUIREMENTS

Before finishing:

1. Run migrations
2. Verify migrations rollback safely
3. Verify MVC create flow
4. Verify MVC edit flow
5. Verify APIs work correctly
6. Verify qualification skills save correctly
7. Verify sync operations work correctly
8. Verify localization works correctly
9. Verify English responses
10. Verify Arabic responses
11. Verify validation works
12. Verify no regression occurs
13. Verify UI consistency
14. Verify performance optimization
15. Fix ALL compile/runtime errors
16. Verify eager loading correctness
17. Verify Angular compatibility

---

# DELIVERABLES

You MUST implement ALL of the following:

1. Database migrations
2. Pivot table
3. Eloquent relationships
4. Localization handling
5. MVC form updates
6. MVC controller updates
7. API controllers
8. Form Requests
9. Validation rules
10. API Resources if applicable
11. Service layer updates if applicable
12. Optimized queries
13. Proper error handling
14. Correct status codes
15. Full CRUD for qualification skills
16. Full course integration
17. Proper sync handling
18. Production-safe implementation
19. Regression-safe implementation
20. Correct localized API responses

---

# FINAL RESULT EXPECTATION

The final implementation must be:

- Production-ready
- Enterprise-grade
- Scalable
- Maintainable
- Reusable
- Fully optimized
- Backward compatible
- MVC compatible
- Angular/API compatible
- Fully localized
- Cleanly architected
- Regression-safe
