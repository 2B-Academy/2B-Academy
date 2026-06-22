<?php

return [
    // Auth
    'login_success'       => 'Logged in successfully.',
    'logout_success'      => 'Logged out successfully.',
    'logout_all_success'  => 'Logged out from all devices.',
    'invalid_credentials' => 'Invalid email or password.',
    'unauthenticated'     => 'Unauthenticated.',
    'forbidden'           => 'You do not have permission to perform this action.',
    'token_expired'       => 'Your session has expired. Please log in again.',

    // Mobile employee identity (token-less mobile auth)
    'mobile_employee_code_required' => 'Employee-Code header is required.',
    'mobile_employee_not_found'     => 'No employee found with the supplied code.',

    // CRUD
    'retrieved'           => 'Data retrieved successfully.',
    'created'             => 'Created successfully.',
    'updated'             => 'Updated successfully.',
    'deleted'             => 'Deleted successfully.',
    'not_found'           => 'Resource not found.',
    'server_error'        => 'An unexpected error occurred.',

    // Business logic
    'exam_already_submitted'  => 'You have already submitted this exam.',
    'already_evaluated'       => 'You have already submitted an evaluation for this course.',
    'form_already_submitted'  => 'You have already submitted this form.',
    'attendance_complete'     => 'You have already attended all sessions for this course.',
    'attendance_recorded'     => 'Attendance recorded successfully.',
    'rate_added'              => 'Thanks! Your feedback has been recorded.',
    'validation_failed'       => 'The given data was invalid.',
    'conflict'                => 'A conflict occurred with the current state of the resource.',
    'course_not_enrolled'     => 'You are not enrolled in this course.',
    'course_not_evaluatable'  => 'This course is not available for evaluation.',

    // Certificates (first-class entity)
    'certificate_issued'      => 'Certificate issued successfully.',
    'certificate_revoked'     => 'Certificate revoked successfully.',
    'certificate_not_found'   => 'Certificate not found.',

    // Dashboard — Session Passcode widget
    'passcode' => [
        'generated'         => 'Passcode generated.',
        'no_live_session'   => 'No live session right now. A passcode can only be generated while a session is in progress.',
        'session_started'   => 'Session started and passcode generated.',
        'session_title'     => 'Live Session — :date',
        'cohort_unavailable' => 'This cohort is not available to start a session (wrong course or already ended).',
        'session_ended'     => 'Session ended.',
    ],

    // Mobile — Academy & Enrolment (S-01 → S-04)
    'mobile' => [
        'academy_summary'             => 'Academy summary retrieved.',
        'academy_scopes'              => 'Academy scopes retrieved.',
        'scope_all'                   => 'All',
        'scope_special'               => 'Special Courses',
        'scope_general'               => 'General Courses',
        'academy_courses'             => 'Academy courses retrieved.',
        'academy_course_detail'       => 'Course detail retrieved.',
        'academy_course_unavailable'  => 'This course is no longer available. The cohort may have filled up or enrolment may have closed.',
        'enrolment_success'           => 'You have a confirmed seat.',
        'enrolment_cohort_full'       => 'Enrolment failed — this cohort just filled up.',
        'enrolment_closed'            => 'Enrolment for this cohort has closed.',
        'enrolment_no_cohort'         => 'No upcoming cohort is open for enrolment.',
        'enrolment_already'           => 'You are already enrolled in this cohort. Open it from My Learning.',

        // Mobile — My Learning (S-05)
        'my_learning_overview'        => 'My Learning overview retrieved.',
        'my_learning_courses'         => 'My active courses retrieved.',
        'my_learning_qualifications'  => 'Qualifications progress retrieved.',
        'my_learning_certificates'    => 'Certificates retrieved.',

        // Mobile — Attendance (S-06)
        'attendance_marked'           => 'Your attendance has been recorded.',
        'attendance_invalid_code'     => 'That code doesn\'t match. Check with your instructor and try again.',
        'attendance_expired_code'     => 'This code has expired. Ask your instructor to reissue it.',
        'attendance_no_open_window'   => 'There is no open attendance window for this course right now.',
        'attendance_already_marked'   => 'You have already marked attendance for this session.',
        'attendance_session_active'   => 'Active session retrieved.',
        'attendance_no_session'       => 'No live or in-person session is scheduled for you today.',

        // Mobile — Certificates (S-07)
        'certificate_download_ready'  => 'Certificate download ready.',
        'certificate_not_found'       => 'No certificate has been issued for this course yet.',
    ],
];
