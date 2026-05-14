<?php


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('StrLimit')) {
    function strLimit($attribute, $limit)
    {
        return \Illuminate\Support\Str::limit($attribute, $limit, $end='...');
    }
}

if (!function_exists('days')) {
    function days()
    {
        $timestamp = strtotime('next Sunday');
        $days = array();
        for ($i = 0; $i < 7; $i++) {
            $days[] = strftime('%A', $timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        return $days;
    }
}

if (!function_exists('switcher')) {
    function switcher()
    {
        return (session()->get('locale') == 'ar') ? 'en' : 'ar';
    }
}

if (!function_exists('currentLanguage')) {
    function currentLanguage()
    {
        return session()->get('locale');
    }
}

if (!function_exists('arabicSlug')) {
    function arabicSlug($string, $separator = '-') {
        if (is_null($string)) {
            return "";
        }
        $string = trim($string);
        $string = mb_strtolower($string, "UTF-8");;
        $string = preg_replace("/[^a-z0-9_\sءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", $separator, $string);
        return $string;
    }
}

//if (!function_exists('settings')) {
//    function settings($key)
//    {
//        return \App\Models\Setting::where('key',$key)->first()?->value;
//    }
//}

if (!function_exists('getFullPath')) {
    function getFullPath($item)
    {
        if ($item == '') {
            return false;
        }
        if (config('filesystems.default') == 's3') {
            return Storage::disk('s3')->url($item);
        } else {
            return url(Storage::disk('public')->url($item));
        }
    }
}

if (!function_exists('countries_select_options')) {
    function countries_select_options()
    {
        $json_file = file_get_contents('countries.json');
        foreach (json_decode($json_file, true) as $item)
        {
            $county_names[] = (currentLanguage() == 'ar') ? $item['arabic_name'] : $item['english_name'];
        }
        return $county_names;
    }
}

if (!function_exists('getBrowser')) {
    function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            $browser = 'Opera';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        } else {
            $browser = 'Unknown';
        }
        return $browser;
    }

}

if (!function_exists('getDevice')) {
    function getDevice($userAgent)
    {
        // Check for mobile devices
        if (strpos($userAgent, 'Mobile') !== false) {
            $device = 'Mobile';
        }
        // Check for tablet devices
        elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            $device = 'Tablet';
        }
        // Otherwise, assume desktop
        else {
            $device = 'Desktop';
        }

        return $device;
    }
}

if (!function_exists('getBase64Url')) {
    function getBase64Url($base64)
    {
        return $base64;
//        if(is_null($base64))
//        {
//            return '';
//        }
//        // Check for PNG
//        if (substr($base64, 0, 8) == 'iVBORw0KGgo') {
//            return 'data:image/png;base64,'.$base64;
//        }
//        // Check for JPEG
//        elseif (substr($base64, 0, 8) == '/9j/4AAQ') {
//            return 'data:image/jpeg;base64,'.$base64;
//        }
//        // Check for GIF
//        elseif (substr($base64, 0, 8) == 'R0lGODlh') {
//            return 'data:image/gif;base64,'.$base64;
//        }
//        // Check for SVG
//        elseif (substr($base64, 0, 6) == 'PHN2Zy'
//        || substr($base64, 0, 24) == 'PD94bWwgdmVyc2lvbj0iMS4w') {
//            return 'data:image/svg+xml;base64,'.$base64;
//        }
//        // Check for WebP
//        elseif (substr($base64, 0, 5) == 'UklGR') {
//            return 'data:image/webp;base64,'.$base64;
//        }
//        // Check for BMP
//        elseif (substr($base64, 0, 3) == 'Qk0') {
//            return 'data:image/bmp;base64,'.$base64;
//        }
//        // Check for TIFF
//        elseif (substr($base64, 0, 4) == 'SUkq') {
//            return 'data:image/tiff;base64,'.$base64;
//        }
//        else {
//             return 'data:image/png;base64,'.$base64;
//        }
    }
}




if (!function_exists('getCustomerProfilePicture')) {
    function getCustomerProfilePicture($base64)
    {
        if(\Illuminate\Support\Facades\Session::has('odoo_customer_data'))
        {
            // Check for PNG
            if (substr($base64, 0, 8) == 'iVBORw0KGgo') {
                return 'data:image/png;base64,'.$base64;
            }
            // Check for JPEG
            elseif (substr($base64, 0, 8) == '/9j/4AAQ') {
                return 'data:image/jpeg;base64,'.$base64;
            }
            // Check for GIF
            elseif (substr($base64, 0, 8) == 'R0lGODlh') {
                return 'data:image/gif;base64,'.$base64;
            }
            // Check for SVG
            elseif (substr($base64, 0, 6) == 'PHN2Zy'
            || substr($base64, 0, 24) == 'PD94bWwgdmVyc2lvbj0iMS4w') {
                return 'data:image/svg+xml;base64,'.$base64;
            }
            // Check for WebP
            elseif (substr($base64, 0, 5) == 'UklGR') {
                return 'data:image/webp;base64,'.$base64;
            }
            // Check for BMP
            elseif (substr($base64, 0, 3) == 'Qk0') {
                return 'data:image/bmp;base64,'.$base64;
            }
            // Check for TIFF
            elseif (substr($base64, 0, 4) == 'SUkq') {
                return 'data:image/tiff;base64,'.$base64;
            }
            else {
                 return 'data:image/png;base64,'.$base64;
            }
        }
        else
        {
          return asset('front/assets/images/avatar.png');
        }
    }
}


if (!function_exists('is_youtube_video')) {
    function is_youtube_video($url)
    {
        if (!Str::contains($url, 'youtube.com') && !Str::contains($url, 'youtu.be')) {
            return null;
        }
        $videoId = null;
        if (Str::contains($url, 'youtube.com')) {
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            $videoId = $query['v'] ?? null;
        }
        if (Str::contains($url, 'youtu.be')) {
            $path = parse_url($url, PHP_URL_PATH);
            $videoId = trim($path, '/');
        }
        if (!$videoId) {
            return null;
        }
        return $videoId;
    }
}
