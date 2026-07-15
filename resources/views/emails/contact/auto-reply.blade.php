@php($ar = $locale === 'ar')
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;background:#f4f6f6;font-family:Arial,Helvetica,sans-serif;color:#171819;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f6;padding:24px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;max-width:560px;width:100%;">
        <tr><td style="background:#0c2427;padding:24px 32px;color:#ffffff;font-size:20px;font-weight:bold;">
          NAS LMS
        </td></tr>
        <tr><td style="padding:32px;">
          <h1 style="margin:0 0 12px;font-size:22px;color:#0c2427;">
            {{ $ar ? 'شكرًا لتواصلك معنا' : 'Thanks for reaching out' }}
          </h1>
          <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#454b52;">
            {{ $ar ? 'مرحبًا' : 'Hi' }} {{ $contact->name }},
          </p>
          <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#454b52;">
            {{ $ar
              ? 'لقد استلمنا طلبك لحجز عرض توضيحي لمنصة NAS LMS. سيتواصل معك فريقنا قريبًا لتحديد الموعد المناسب.'
              : 'We have received your request to book a NAS LMS demo. Our team will contact you shortly to arrange a convenient time.' }}
          </p>
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f2fbfa;border-radius:12px;margin:8px 0 4px;">
            <tr><td style="padding:16px 20px;">
              <p style="margin:0 0 8px;font-size:13px;color:#225f63;font-weight:bold;text-transform:uppercase;">
                {{ $ar ? 'ملخص طلبك' : 'Your request' }}
              </p>
              <p style="margin:0;font-size:14px;line-height:1.7;color:#454b52;">
                <strong>{{ $ar ? 'الاسم' : 'Name' }}:</strong> {{ $contact->name }}<br>
                <strong>{{ $ar ? 'البريد الإلكتروني' : 'Email' }}:</strong> {{ $contact->email }}<br>
                @if($contact->phone)<strong>{{ $ar ? 'الهاتف' : 'Phone' }}:</strong> {{ $contact->phone }}<br>@endif
                @if($contact->job_title)<strong>{{ $ar ? 'المسمى الوظيفي' : 'Job title' }}:</strong> {{ $contact->job_title }}<br>@endif
                @if($contact->company_name)<strong>{{ $ar ? 'الشركة' : 'Company' }}:</strong> {{ $contact->company_name }}@endif
              </p>
            </td></tr>
          </table>
          <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#8a9096;">
            {{ $ar ? 'مع تحيات فريق NAS LMS' : '— The NAS LMS Team' }}
          </p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
