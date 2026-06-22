<?php

return [
    // Auth
    'login_success'       => 'تم تسجيل الدخول بنجاح.',
    'logout_success'      => 'تم تسجيل الخروج بنجاح.',
    'logout_all_success'  => 'تم تسجيل الخروج من جميع الأجهزة.',
    'invalid_credentials' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
    'unauthenticated'     => 'غير مصادق عليه.',
    'forbidden'           => 'ليس لديك صلاحية لتنفيذ هذا الإجراء.',
    'token_expired'       => 'انتهت صلاحية الجلسة. يرجى تسجيل الدخول مجددًا.',

    // Mobile employee identity (token-less mobile auth)
    'mobile_employee_code_required' => 'رأس Employee-Code مطلوب.',
    'mobile_employee_not_found'     => 'لا يوجد موظف بهذا الكود.',

    // CRUD
    'retrieved'           => 'تم استرجاع البيانات بنجاح.',
    'created'             => 'تم الإنشاء بنجاح.',
    'updated'             => 'تم التحديث بنجاح.',
    'deleted'             => 'تم الحذف بنجاح.',
    'not_found'           => 'المورد غير موجود.',
    'server_error'        => 'حدث خطأ غير متوقع.',

    // Business logic
    'exam_already_submitted'  => 'لقد أرسلت هذا الاختبار مسبقًا.',
    'already_evaluated'       => 'لقد أرسلت تقييمًا لهذه الدورة مسبقًا.',
    'form_already_submitted'  => 'لقد أرسلت هذا النموذج مسبقًا.',
    'attendance_complete'     => 'لقد حضرت جميع جلسات هذه الدورة مسبقًا.',
    'attendance_recorded'     => 'تم تسجيل الحضور بنجاح.',
    'rate_added'              => 'شكرًا لك! تم تسجيل تقييمك.',
    'validation_failed'       => 'البيانات المدخلة غير صالحة.',
    'conflict'                => 'حدث تعارض مع الحالة الحالية للمورد.',
    'course_not_enrolled'     => 'أنت غير مسجل في هذه الدورة.',
    'course_not_evaluatable'  => 'هذه الدورة غير متاحة للتقييم.',

    // Certificates (first-class entity)
    'certificate_issued'      => 'تم إصدار الشهادة بنجاح.',
    'certificate_revoked'     => 'تم إلغاء الشهادة بنجاح.',
    'certificate_not_found'   => 'الشهادة غير موجودة.',

    // لوحة التحكم — أداة رمز الحضور
    'passcode' => [
        'generated'         => 'تم إنشاء رمز الحضور.',
        'no_live_session'   => 'لا توجد جلسة مباشرة الآن. يمكن إنشاء رمز الحضور فقط أثناء انعقاد جلسة.',
        'session_started'   => 'تم بدء الجلسة وإنشاء رمز الحضور.',
        'session_title'     => 'جلسة مباشرة — :date',
        'cohort_unavailable' => 'لا يمكن بدء جلسة لهذه المجموعة (دورة غير صحيحة أو منتهية).',
        'session_ended'     => 'تم إنهاء الجلسة.',
    ],

    // الموبايل — الأكاديمية والتسجيل (S-01 → S-04)
    'mobile' => [
        'academy_summary'             => 'تم استرجاع ملخّص الأكاديمية.',
        'academy_scopes'              => 'تم استرجاع تبويبات الأكاديمية.',
        'scope_all'                   => 'الكل',
        'scope_special'               => 'دورات تخصصية',
        'scope_general'               => 'دورات عامة',
        'academy_courses'             => 'تم استرجاع دورات الأكاديمية.',
        'academy_course_detail'       => 'تم استرجاع تفاصيل الدورة.',
        'academy_course_unavailable'  => 'هذه الدورة لم تعد متاحة. ربما اكتملت الدفعة أو أُغلق التسجيل.',
        'enrolment_success'           => 'تم تأكيد مقعدك بنجاح.',
        'enrolment_cohort_full'       => 'فشل التسجيل — اكتملت هذه الدفعة الآن.',
        'enrolment_closed'            => 'تم إغلاق التسجيل لهذه الدفعة.',
        'enrolment_no_cohort'         => 'لا توجد دفعة قادمة مفتوحة للتسجيل.',
        'enrolment_already'           => 'أنت مسجل بالفعل في هذه الدفعة. افتحها من قسم "تعلّمي".',

        // الموبايل — تعلّمي (S-05)
        'my_learning_overview'        => 'تم استرجاع نظرة عامة على تعلّمي.',
        'my_learning_courses'         => 'تم استرجاع دوراتي النشطة.',
        'my_learning_qualifications'  => 'تم استرجاع تقدّم المؤهلات.',
        'my_learning_certificates'    => 'تم استرجاع الشهادات.',

        // الموبايل — الحضور (S-06)
        'attendance_marked'           => 'تم تسجيل حضورك بنجاح.',
        'attendance_invalid_code'     => 'هذا الكود غير صحيح. تحقّق من المدرّب وحاول مرة أخرى.',
        'attendance_expired_code'     => 'انتهت صلاحية هذا الكود. اطلب من المدرّب إصدار كود جديد.',
        'attendance_no_open_window'   => 'لا توجد نافذة حضور مفتوحة لهذه الدورة الآن.',
        'attendance_already_marked'   => 'لقد سجّلت حضورك لهذه الجلسة بالفعل.',
        'attendance_session_active'   => 'تم استرجاع الجلسة النشطة.',
        'attendance_no_session'       => 'لا توجد جلسة مباشرة أو حضورية مجدولة لك اليوم.',

        // الموبايل — الشهادات (S-07)
        'certificate_download_ready'  => 'الشهادة جاهزة للتنزيل.',
        'certificate_not_found'       => 'لم تُصدَر شهادة لهذه الدورة بعد.',
    ],
];
