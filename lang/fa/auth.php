<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'تعداد دفعات تلاش غیرمجاز. لطفا تا :seconds ثانیه دیگر تلاش کنید.',
    'sms' => [
        'already_sent' => "کد تایید به این شماره قبلا ارسال شده است. کمی منتظر بمانید."
    ],
    'login' => [
        'username-exists' => "کاربری با شماره تلفن وارد شده یافت نشد"
    ],
    'register' => [
        'serial' => "سریال",
        'serial-required' => "وارد کردن :attribute اجباری می‌باشد",
        'serial-exists' => ":attribute وارد شده معتبر نمی‌باشد",
        'serial-used' => "سریال وارد شده قبلا فعال شده است",
        'serial-inactive' => "سریال وارد شده غیرفعال می‌باشد",

        'verify-ok' => "شماره تلفن با موفقیت تایید شد",

        'complete-info' => "لطفا اطلاعات فرم زیر را با دقت پر و ثبت نام خود را نهایی کنید",
        'complete-wrong' => "لطفا اطلاعات فرم را بدقت پر نمایید"
    ]

];
