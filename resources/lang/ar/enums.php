<?php

declare(strict_types=1);

/**
 * Arabic labels for every backend enum surfaced via `/api/v1/enums`.
 * Keep keys in sync with `resources/lang/en/enums.php`.
 */

return [

    'course_type' => [
        'online'        => 'أونلاين',
        'offline'       => 'حضوري',
        'hybrid'        => 'مدمج',
        'external_link' => 'رابط خارجي',
    ],

    'course_status' => [
        'all'      => 'الكل',
        'pending'  => 'قيد المراجعة',
        'active'   => 'نشطة',
        'upcoming' => 'قادمة',
        'inactive' => 'غير نشطة',
    ],

    'course_level' => [
        'beginner'     => 'مبتدئ',
        'intermediate' => 'متوسط',
        'professional' => 'محترف',
    ],

    'cohort_status' => [
        'scheduled'           => 'مجدولة',
        'open_for_enrollment' => 'متاحة للتسجيل',
        'active'              => 'جارية',
        'completed'           => 'منتهية',
        'inactive'            => 'موقوفة',
    ],

    'module_content_type' => [
        'video'    => 'فيديو',
        'document' => 'مستند',
        'article'  => 'مقال',
        'link'     => 'رابط',
    ],

    'module_learner_scope' => [
        'all'    => 'جميع المجموعات',
        'cohort' => 'مجموعة محددة',
    ],

    'resource_type' => [
        'article' => 'مقال',
        'link'    => 'رابط خارجي',
        'file'    => 'ملف / مستند',
    ],

    'certificate_basis' => [
        'attendance'      => 'بناءً على الحضور',
        'attendance_desc' => 'يتم إصدار الشهادة عند تحقيق نسبة الحضور المطلوبة.',
        'score'           => 'بناءً على الدرجة',
        'score_desc'      => 'يتم إصدار الشهادة عند الوصول للحد الأدنى من الدرجات.',
        'both'            => 'الحضور والدرجة',
        'both_desc'       => 'يجب تحقيق نسبة الحضور والحصول على الحد الأدنى من الدرجات معًا.',
    ],

    'locale' => [
        'en' => 'English',
        'ar' => 'العربية',
    ],

    'cohort_scope' => [
        'all'      => 'جميع المجموعات',
        'specific' => 'مجموعة محددة',
    ],

    'question_type' => [
        'mcq'     => 'اختيار من متعدد',
        'yes_no'  => 'نعم / لا',
        'open'    => 'إجابة قصيرة',
        'reorder' => 'إعادة ترتيب',
    ],

    'dashboard_range' => [
        'week'    => 'أسبوع',
        'month'   => 'شهر',
        'quarter' => 'ربع سنة',
        'year'    => 'سنة',
    ],

    'role_color' => [
        'teal'   => 'تركواز',
        'green'  => 'أخضر',
        'orange' => 'برتقالي',
        'red'    => 'أحمر',
        'blue'   => 'أزرق',
    ],

    'role_guard' => [
        'admin' => 'إداري',
        'web'   => 'ويب',
    ],

    'inbox_tab' => [
        'all'      => 'الكل',
        'unread'   => 'غير مقروء',
        'sent'     => 'المُرسلة',
        'resolved' => 'تم حلها',
    ],

    'user_status' => [
        'active'      => 'نشط',
        'inactive'    => 'غير نشط',
        'deactivated' => 'موقوف',
    ],

    'learner_type' => [
        'online'  => 'أونلاين',
        'offline' => 'حضوري',
        'hybrid'  => 'مدمج',
    ],

    'lifecycle_status' => [
        'draft'  => 'مسودة',
        'active' => 'نشط',
    ],

    'enrollment_status' => [
        'not_started' => 'لم يبدأ',
        'in_progress' => 'قيد التقدم',
        'completed'   => 'مكتمل',
    ],

    'rating_sentiment' => [
        'very_satisfied'     => 'راضي جدًا',
        'satisfied'          => 'راضي',
        'neutral'            => 'محايد',
        'unsatisfied'        => 'غير راضي',
        'unsatisfied_at_all' => 'غير راضي على الإطلاق',
    ],
];
