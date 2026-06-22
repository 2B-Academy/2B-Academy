<?php

declare(strict_types=1);

/**
 * Localized labels (and optional descriptions) for every backend enum
 * exposed through the public `/api/v1/enums` endpoint.
 *
 * The keys here MUST match the values registered in
 * {@see \App\Enums\EnumRegistry}. To add a new option:
 *   1. Append the value to `EnumRegistry::MAP`.
 *   2. Add an EN entry here and the matching AR entry in `ar/enums.php`.
 */

return [

    // ── Courses ────────────────────────────────────────────────────────
    'course_type' => [
        'online'        => 'Online',
        'offline'       => 'Offline',
        'hybrid'        => 'Hybrid',
        'external_link' => 'External Link',
    ],

    'course_status' => [
        'all'      => 'All',
        'pending'  => 'Pending',
        'active'   => 'Active',
        'upcoming' => 'Up Coming',
        'inactive' => 'Inactive',
    ],

    'course_level' => [
        'beginner'     => 'Beginner',
        'intermediate' => 'Intermediate',
        'professional' => 'Professional',
    ],

    'cohort_status' => [
        'scheduled'           => 'Scheduled',
        'open_for_enrollment' => 'Open for Enrollment',
        'active'              => 'Active',
        'completed'           => 'Completed',
        'inactive'            => 'Inactive',
    ],

    'module_content_type' => [
        'video'    => 'Video',
        'document' => 'Document',
        'article'  => 'Article',
        'link'     => 'Link',
    ],

    'module_learner_scope' => [
        'all'    => 'All cohorts',
        'cohort' => 'Specific Cohort',
    ],

    // ── Resources ──────────────────────────────────────────────────────
    'resource_type' => [
        'article' => 'Article',
        'link'    => 'External Link',
        'file'    => 'File / Document',
    ],

    // ── Platform settings ──────────────────────────────────────────────
    'certificate_basis' => [
        'attendance'      => 'Based on Attendance',
        'attendance_desc' => 'Issue a certificate when the learner meets the attendance threshold.',
        'score'           => 'Based on Score',
        'score_desc'      => 'Issue a certificate when the learner reaches the minimum passing score.',
        'both'            => 'Attendance & Score',
        'both_desc'       => 'Require both the attendance threshold and the minimum score.',
    ],

    'locale' => [
        'en' => 'English',
        'ar' => 'العربية',
    ],

    // ── Quizzes / assignments ──────────────────────────────────────────
    'cohort_scope' => [
        'all'      => 'All cohorts',
        'specific' => 'Specific cohort',
    ],

    'question_type' => [
        'mcq'     => 'Multiple Choice',
        'yes_no'  => 'Yes / No',
        'open'    => 'Short Answer',
        'reorder' => 'Reorder',
    ],

    // ── Dashboard ──────────────────────────────────────────────────────
    'dashboard_range' => [
        'week'    => 'Week',
        'month'   => 'Month',
        'quarter' => 'Quarter',
        'year'    => 'Year',
    ],

    // ── Roles ──────────────────────────────────────────────────────────
    'role_color' => [
        'teal'   => 'Teal',
        'green'  => 'Green',
        'orange' => 'Orange',
        'red'    => 'Red',
        'blue'   => 'Blue',
    ],

    'role_guard' => [
        'admin' => 'Admin',
        'web'   => 'Web',
    ],

    // ── Inbox / Messages ───────────────────────────────────────────────
    'inbox_tab' => [
        'all'      => 'All',
        'unread'   => 'Unread',
        'sent'     => 'Sent',
        'resolved' => 'Resolved',
    ],

    // ── Users (admin) ──────────────────────────────────────────────────
    'user_status' => [
        'active'      => 'Active',
        'inactive'    => 'Inactive',
        'deactivated' => 'Deactivated',
    ],

    'learner_type' => [
        'online'  => 'Online',
        'offline' => 'Offline',
        'hybrid'  => 'Hybrid',
    ],

    // ── Quiz / assignment lifecycle ────────────────────────────────────
    'lifecycle_status' => [
        'draft'  => 'Draft',
        'active' => 'Active',
    ],

    // ── Enrollments / Learners ─────────────────────────────────────────
    'enrollment_status' => [
        'not_started' => 'Not Started',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ],

    // ── Course rating sentiment (5-point scale) ────────────────────────
    'rating_sentiment' => [
        'very_satisfied'     => 'Very satisfied',
        'satisfied'          => 'Satisfied',
        'neutral'            => 'Neutral',
        'unsatisfied'        => 'Unsatisfied',
        'unsatisfied_at_all' => 'Unsatisfied at all',
    ],
];
