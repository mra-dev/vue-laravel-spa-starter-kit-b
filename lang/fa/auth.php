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

    'failed' => 'رمز عبور یا نام کاربری یافت نشد. دوباره تلاش کنید',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'تعداد دفعات تلاش غیرمجاز. لطفا تا :seconds ثانیه دیگر تلاش کنید.',
    'sms' => [
        'already_sent' => "کد تایید به این شماره قبلا ارسال شده است. کمی منتظر بمانید.",
        'throttle' => "به تازگی توسط پیامک ، تایید شدید. لطفا از آخرین درخواست حداقل ۵ دقیقه صبر کنید."
    ],
    'login' => [
        'username-exists' => "کاربری با شماره تلفن وارد شده یافت نشد",
        'success' => "وارد شدید! به حساب کاربری خودتان خوش آمدید"
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
