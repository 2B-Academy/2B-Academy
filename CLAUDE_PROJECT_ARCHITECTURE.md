# Laravel Enterprise Migration & API Transformation Task

You are a senior enterprise Laravel architect and backend modernization expert.

You must use:

- laravel-specialist skill
- jeffallan/claude-skills
- api-designer/claude-skills

Apply all Laravel best practices, clean architecture principles, SOLID principles, scalable API architecture, and enterprise migration safety standards.

---

# Project Goals

The project is currently:

- Legacy Laravel MVC application
- Monolithic structure
- Server-rendered Blade architecture
- Single language database structure
- Not API-first

The target architecture is:

# Architecture Reference Repository

You must use the following GitHub repository as the primary architecture reference and implementation standard for this project:

https://github.com/AhmedKhaledabdelkader/Inventory-Management/tree/main/store-management

You must carefully analyze and follow the exact architectural patterns used in that repository.

---

# Required Areas To Mirror

You must follow the same:

- Folder structure
- Module organization
- API architecture
- Service layer structure
- Repository patterns
- Base controller patterns
- Request validation approach
- Resource/Transformer structure
- Response format
- Exception handling structure
- Localization approach
- Authentication flow
- Route organization
- Naming conventions
- Traits usage patterns
- Helper organization
- DTO patterns (if used)
- Event/listener patterns
- Queue architecture
- Testing structure
- Config organization
- Environment handling
- Middleware strategy
- Coding style
- Laravel best practices

---

# Critical Instructions

DO NOT:

- Invent a different architecture
- Introduce inconsistent patterns
- Mix multiple architectures
- Use random folder structures
- Generate code outside the established project conventions

You MUST:

- Study the reference repository first
- Explain the detected architecture before implementation
- Reuse the same design philosophy
- Maintain consistency with the reference repository
- Reuse naming conventions from the reference repository
- Follow the same scalability patterns

---

# Before Writing Code

You must first analyze and explain:

1. The architecture pattern used
2. The folder/module structure
3. The API design approach
4. The localization implementation approach
5. The service layer design
6. The database strategy
7. The response structure
8. The authentication strategy
9. The coding conventions

Do not generate implementation code before completing the architecture analysis.

---

# Implementation Goal

The current project must be transformed to follow the same architecture and engineering standards as the reference repository while preserving existing business logic.

1. Upgrade the entire project safely to Laravel 11
2. Convert the entire system into RESTful APIs
3. Preserve all existing business logic
4. Generate structured API collections
5. Implement scalable multilingual database architecture
6. Support localization for:
    - Arabic (ar)
    - English (en)
7. Improve maintainability and scalability
8. Avoid breaking existing functionality
9. Follow enterprise-grade architecture

---

# Critical Rules

DO NOT:

- Rewrite the entire project blindly
- Remove existing business logic
- Introduce breaking database changes without migration strategy
- Modify production-sensitive logic without analysis
- Create unstructured APIs
- Use shortcuts that reduce maintainability

You MUST:

- Analyze before modifying
- Explain all architectural decisions
- Refactor incrementally
- Keep backward compatibility whenever possible
- Generate clean migration plans
- Maintain clean separation of concerns
- Use service layers where needed
- Use Form Requests for validation
- Use API Resources/Transformers
- Use Repository pattern only where beneficial
- Follow Laravel 11 conventions

---

# Required Workflow

## Phase 1 — Full Project Audit

Analyze:

- Laravel version
- Package compatibility
- Folder structure
- Controllers
- Models
- Middleware
- Authentication system
- Database schema
- Localization limitations
- Blade dependencies
- JavaScript dependencies
- Business logic coupling
- Technical debt

Generate:

- Upgrade risk report
- Refactor roadmap
- Breaking changes report
- Dependency compatibility report

DO NOT modify code yet.

---

## Phase 2 — Laravel 11 Upgrade Plan

Create:

- Step-by-step Laravel 11 upgrade strategy
- Composer dependency migration plan
- Deprecated code replacement plan
- Package replacement recommendations

Then execute incrementally.

---

## Phase 3 — API Architecture Transformation

Convert the system to:

- RESTful APIs
- Versioned APIs (/api/v1)

Requirements:

- Proper status codes
- Consistent JSON response structure
- API Resources
- Validation layers
- Authentication APIs
- Pagination
- Filtering
- Sorting
- Error handling
- Rate limiting
- Token authentication
- API documentation

Generate:

- Postman Collection
- OpenAPI/Swagger specification

---

## Phase 4 — Database Localization Architecture

Current problem:
The database stores translatable content in single columns.

Target:
Support multilingual content cleanly.

Preferred approaches:

- Translation tables
  OR
- JSON translation columns

Claude must:

- Analyze best approach for each table
- Generate migration strategy
- Preserve old data
- Avoid downtime risks

Support:

- Arabic
- English

Examples:
title => {
"en": "Product",
"ar": "منتج"
}

or dedicated translation tables.

---

## Phase 5 — Refactoring Standards

Apply:

- SOLID principles
- Service layer architecture
- DTOs where useful
- Clean controller logic
- Reusable traits only when justified
- Queue support where appropriate
- Event-driven patterns if beneficial

Avoid:

- Fat controllers
- Duplicated logic
- Business logic inside views
- Direct DB queries inside controllers

---

## Phase 6 — Testing & Validation

Generate:

- Feature tests
- API tests
- Migration validation tests

Verify:

- Existing functionality still works
- API responses are stable
- Localization works correctly

---

# Output Expectations

For every major task:

1. Explain analysis first
2. Explain risks
3. Explain proposed solution
4. Then generate code
5. Then explain migration impact

Never perform destructive actions automatically.

Always prioritize maintainability, scalability, and production safety.

# Database Migration Safety

Never:

- Drop columns immediately
- Rename columns destructively
- Remove old localized data before migration verification

Always:

- Use phased migrations
- Preserve backward compatibility
- Create rollback-safe migrations
- Validate migrated data integrity

# Documentation Requirements

Generate and maintain:

- API Architecture Documentation
- Database Migration Documentation
- Localization Strategy Documentation
- Module Dependency Documentation
- Upgrade Changelog
- Refactor Logs

# Performance Requirements

Maintain or improve performance during migration.

Avoid:

- N+1 queries
- over-fetching
- excessive eager loading
- unnecessary abstractions

Use:

- query optimization
- pagination
- caching where appropriate
- resource-efficient API responses

# Project Libraries

for localization :
use spatie/laravel-translatable

for APIs :
Laravel Sanctum
Laravel API Resources
OpenAPI/Swagger
Dedicated API Exception Handler
