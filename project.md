You are a senior enterprise software architect and full-stack migration expert.

I have an old ASP.NET MVC project that is already working in production.
I am currently migrating it into:
1- Backend APIs
2- Angular localized frontend

Your mission is to deeply analyze the MVC project and guarantee that the new system behaves EXACTLY like the old MVC project without missing any business logic, validations, permissions, translations, or workflows.

==================================================
MAIN GOAL
==================================================

Ensure that:

- Every feature in the old MVC project exists in the new Angular + API architecture.
- APIs fully cover all old MVC functionality.
- Angular frontend behaves exactly like the MVC frontend.
- No business logic is lost during migration.
- Localization works correctly everywhere.
- The new architecture is scalable, maintainable, and production-ready.

==================================================
PROJECT STRUCTURE TO ANALYZE
==================================================

Analyze ALL related folders carefully including:

- Controllers
- Views
- ViewModels
- Models
- Services
- Helpers
- Repositories
- Filters
- Middleware
- Scripts
- JavaScript
- jQuery
- AJAX calls
- Areas
- Shared layouts
- Components
- Resource files
- Localization files
- Validation logic
- Permissions / Roles
- Session usage
- TempData
- ViewBag / ViewData
- Partial views
- Routing
- Bundles
- Config files
- Constants
- Enums

Also analyze:

- Hidden business logic inside Razor Views
- Inline JavaScript logic
- Dynamic rendering
- Conditional UI rendering
- Role-based rendering
- AJAX endpoints
- Client-side validations
- Server-side validations

==================================================
API REVIEW REQUIREMENTS
==================================================

Review all existing APIs and ensure they fully support the old MVC behavior.

For every MVC feature:

- Find the equivalent API
- Validate request/response structure
- Validate status codes
- Validate business logic
- Validate validations
- Validate permissions
- Validate edge cases
- Validate localization support
- Validate pagination/filtering/sorting
- Validate file uploads
- Validate authentication & authorization

If something is missing:

- Create the missing API
- Refactor incorrect APIs
- Improve naming consistency
- Improve architecture consistency

==================================================
HTTP STATUS CODE RULES
==================================================

Use proper REST standards:

- 200 => Success
- 201 => Created
- 400 => Invalid request format
- 401 => Unauthorized
- 403 => Forbidden
- 404 => Resource not found
- 409 => Conflict
- 422 => Business validation errors
- 500 => Internal server error

Validation errors should return:
{
"success": false,
"message": {
"en": "...",
"ar": "..."
},
"errors": []
}

==================================================
LOCALIZATION REQUIREMENTS
==================================================

Localization is CRITICAL.

The API MUST support localization using request headers.

Example:
Accept-Language: ar
or
culture: ar-EG

Requirements:

- APIs must return translated values based on headers.
- Never return:
  {
  "name": {
  "en": "...",
  "ar": "..."
  }
  }

Instead return:
{
"name": "القيمة بالعربي"
}

when culture is Arabic.

And:
{
"name": "English value"
}

when culture is English.

Apply localization to:

- Validation messages
- Exceptions
- DTOs
- Labels
- Dropdowns
- Enum display names
- Notifications
- Emails
- API responses
- UI text

==================================================
ANGULAR FRONTEND REQUIREMENTS
==================================================

Generate a FULL Angular application using latest Angular best practices.

Requirements:

- Standalone components
- Lazy loading
- Modular scalable architecture
- Reusable shared components
- Reactive forms
- Route guards
- Interceptors
- Strong typing
- Environment configs
- Translation support
- RTL/LTR support
- Responsive UI
- Production-ready structure

==================================================
ANGULAR LOCALIZATION
==================================================

Angular must support:

- Arabic
- English
- Dynamic language switching
- RTL/LTR switching
- Translation files
- Localized validation messages
- Localized menus
- Localized forms
- Localized notifications

The selected language must:

- Be stored
- Persist after refresh
- Be sent automatically in API headers

==================================================
API INTEGRATION REQUIREMENTS
==================================================

Generate:

- API services
- DTO interfaces
- Models
- State management structure if needed
- Error handling
- Loading handling
- Retry handling
- Centralized HTTP interceptor

The interceptor MUST:

- Inject localization headers
- Handle unauthorized responses
- Handle API errors globally
- Handle validation errors globally

==================================================
MIGRATION VALIDATION
==================================================

Create a migration audit checklist.

For EVERY MVC screen/page:

- Verify route exists
- Verify UI exists
- Verify validations exist
- Verify API integration exists
- Verify permissions exist
- Verify localization exists
- Verify business logic exists
- Verify edge cases handled
- Verify workflows behave identically

==================================================
BUSINESS LOGIC EXTRACTION
==================================================

Move ALL business logic into services.

Controllers should ONLY:

- Receive requests
- Call services
- Return responses

Repositories should ONLY:

- Handle database operations

No business logic inside:

- Controllers
- Angular components
- Views
- Repositories

==================================================
CODE QUALITY REQUIREMENTS
==================================================

Apply:

- SOLID principles
- Clean Architecture principles
- Repository pattern
- Service layer pattern
- DTO pattern
- AutoMapper pattern if needed
- Centralized exception handling
- Logging
- Performance optimization
- Caching where appropriate

==================================================
PERFORMANCE REQUIREMENTS
==================================================

Optimize:

- API response times
- Database queries
- Angular rendering
- Change detection
- Lazy loading
- Bundle sizes
- Duplicate requests

Avoid:

- N+1 queries
- Duplicate API calls
- Heavy components
- Unnecessary renders

==================================================
DELIVERABLES
==================================================

Generate:
1- Missing APIs
2- Refactored APIs
3- Angular frontend
4- Localization implementation
5- Migration checklist
6- Folder structure
7- Architecture recommendations
8- Technical debt report
9- Missing feature report
10- MVC vs Angular feature mapping
11- Endpoint documentation
12- Validation mapping
13- Permission mapping
14- Reusable shared components
15- Production-ready scalable structure

==================================================
IMPORTANT
==================================================

DO NOT assume functionality.
TRACE every feature from the MVC project carefully.

If logic exists in:

- JavaScript
- Razor
- jQuery
- AJAX
- Helpers
- Partial views
- HTML attributes
- Inline scripts

it MUST be migrated correctly.

The final Angular + APIs system must behave IDENTICALLY to the original MVC system.

==================================================
CRITICAL SAFETY RULES
==================================================

The existing ASP.NET MVC project is the SOURCE OF TRUTH.

You MUST NOT:

- Modify MVC files
- Delete MVC files
- Refactor MVC code
- Rename MVC files
- Move MVC files
- Replace MVC logic
- Change MVC behavior
- Change MVC routes
- Change MVC views
- Change MVC controllers
- Change MVC business logic

The MVC project is READ-ONLY.

You may ONLY:

- Analyze it
- Read it
- Trace functionality
- Extract business logic understanding
- Compare behavior

==================================================
ANGULAR PROJECT LOCATION
==================================================

Create the Angular application in a COMPLETELY SEPARATE directory.

Example structure:

/ProjectRoot
/LegacyMvcApp <-- EXISTING PROJECT (READ ONLY)
/NewAngularApp <-- CREATE HERE ONLY
/NewBackendApis <-- CREATE HERE ONLY

NEVER generate Angular files inside:

- LegacyMvcApp
- MVC Views
- MVC Scripts
- MVC wwwroot
- MVC Areas
- MVC Content

NEVER overwrite existing files.

==================================================
FILE GENERATION RULES
==================================================

Before creating any file:

- Check if file already exists
- Never overwrite without explicit approval
- Never delete files
- Never modify existing MVC source files

All generated code must be isolated inside:

- /NewAngularApp
- /NewBackendApis

==================================================
MIGRATION STRATEGY
==================================================

The migration must be NON-DESTRUCTIVE.

Meaning:

- Old MVC project continues working normally
- Angular project is developed separately
- APIs are developed separately
- Both systems can run side-by-side during migration

==================================================
ANGULAR GENERATION REQUIREMENTS
==================================================

Generate the Angular app as a completely independent production-ready application.

The Angular app must:

- Consume APIs only
- Not depend on Razor Views
- Not depend on MVC rendering
- Not inject scripts into MVC
- Not share frontend assets with MVC

==================================================
OUTPUT REQUIREMENTS
==================================================

At the end provide:

- Full folder structure
- All created files
- Migration progress report
- Feature parity report
- Missing functionality report
- Safe migration notes

==================================================
IMPORTANT
==================================================

Treat the MVC project as immutable legacy code.
DO NOT TOUCH IT.
DO NOT MODIFY IT.
DO NOT DELETE ANYTHING INSIDE IT.

==================================================
ANGULAR ARCHITECTURE STANDARDS (CRITICAL)
==================================================

Act as a Principal/Lead Angular Architect building a LARGE SCALE ENTERPRISE application.

The Angular application must be:

- Extremely scalable
- Extremely maintainable
- Enterprise-grade
- Modular
- Reusable
- High-performance
- Clean architecture based
- Production-ready

The goal is LONG TERM maintainability and MAXIMUM performance.

==================================================
ANGULAR VERSION & STANDARDS
==================================================

Use:

- Latest stable Angular version
- Standalone components
- Signals where appropriate
- Strict TypeScript mode
- Fully typed APIs
- Modern Angular best practices only

Avoid:

- Deprecated Angular patterns
- Legacy module-heavy architecture
- Any anti-patterns
- Duplicate logic
- Large god components

==================================================
FOLDER STRUCTURE
==================================================

Create a clean scalable structure similar to enterprise frontend architecture.

Example:

/src/app
/core
/services
/guards
/interceptors
/models
/constants
/config
/state
/utils

    /shared
        /components
        /pipes
        /directives
        /ui
        /validators
        /helpers

    /features
        /auth
        /dashboard
        /orders
        /customers
        /etc

Each feature must contain:

- pages
- components
- services
- models
- store
- routes
- enums
- constants

==================================================
REUSABLE COMPONENTS
==================================================

Extract reusable components aggressively.

Avoid duplicated UI.

Create reusable:

- Tables
- Forms
- Inputs
- Dropdowns
- Dialogs
- Cards
- Buttons
- Upload components
- Pagination
- Filters
- Search components
- Empty states
- Error states
- Loaders
- Toast notifications

==================================================
PERFORMANCE REQUIREMENTS (VERY IMPORTANT)
==================================================

The Angular app must be optimized for MAXIMUM performance.

Target mindset:
"Performance score must feel like 100%"

Apply:

- Lazy loading everywhere possible
- Route-level code splitting
- Deferrable views
- OnPush change detection
- Signals where beneficial
- Smart state management
- Memoization
- Virtual scrolling where needed
- TrackBy functions
- Optimized RxJS usage
- Avoid unnecessary subscriptions
- Proper unsubscribe handling
- Image optimization
- Bundle optimization
- Tree shaking
- Dynamic imports
- Shared chunk optimization

Avoid:

- Heavy rendering
- Unnecessary API calls
- Duplicate requests
- Nested subscriptions
- Massive components
- Inline complex logic in templates
- Expensive pipes in templates

==================================================
STATE MANAGEMENT
==================================================

Use clean scalable state management.

Do NOT over-engineer.

Use:

- Signals
- RxJS
- Lightweight store architecture

Only introduce NgRx if complexity truly requires it.

==================================================
API ARCHITECTURE
==================================================

Create:

- Generic base API service
- Typed API responses
- Generic pagination models
- Centralized API error handling
- Retry strategies
- Request caching where appropriate

==================================================
FORM ARCHITECTURE
==================================================

Forms must be:

- Reusable
- Dynamic where beneficial
- Strongly typed
- Fully validated
- Localized

Validation must:

- Match MVC behavior EXACTLY
- Support Arabic & English
- Be reusable

==================================================
LOCALIZATION & RTL
==================================================

The application MUST fully support:

- Arabic
- English
- RTL/LTR switching
- Dynamic language switching

Requirements:

- Direction changes dynamically
- Components fully RTL compatible
- No broken layouts in Arabic
- Fonts optimized for Arabic
- Translation keys organized properly

==================================================
UI/UX REQUIREMENTS
==================================================

The application must feel:

- Fast
- Clean
- Modern
- Responsive
- Enterprise-level

Avoid:

- UI inconsistencies
- Duplicate styling
- Random spacing
- Hardcoded dimensions
- Hardcoded colors

Create:

- Shared design system
- Shared theme variables
- Shared spacing system
- Shared typography system

==================================================
SECURITY REQUIREMENTS
==================================================

Apply frontend security best practices:

- Route guards
- Permission-based rendering
- Secure token handling
- XSS prevention
- Sanitization where needed

==================================================
CODE QUALITY
==================================================

Enforce:

- SOLID principles
- DRY principle
- KISS principle
- Clean code
- Reusable abstractions
- Feature isolation
- Separation of concerns

==================================================
ANGULAR COMPONENT RULES
==================================================

Components should be:

- Small
- Focused
- Reusable
- Testable

Avoid:

- Business logic inside templates
- Massive components
- Repeated API calls
- Tight coupling

==================================================
DELIVERABLE EXPECTATIONS
==================================================

Generate:

- Enterprise-grade Angular architecture
- Reusable shared library structure
- Feature-based architecture
- Optimized routing
- Optimized API integration
- Scalable folder structure
- Performance-first implementation
- Localization-ready architecture
- Production-ready code

==================================================
IMPORTANT
==================================================

- install primeng
- install ngx-carousel if needed

Think like:

- Lead Angular Architect
- Enterprise frontend engineer
- Performance optimization expert

Every decision must prioritize:
1- Scalability
2- Maintainability
3- Reusability
4- Performance
5- Clean architecture
