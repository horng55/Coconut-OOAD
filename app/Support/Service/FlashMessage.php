<?php

namespace App\Support\Service;

class FlashMessage
{
    static function success($message = "ប្រតិបត្តិការជោគជ័យ")
    {
        request()->session()->flash('alert', [
            'type' => "success",
            'message' => $message,
        ]);
    }

    static function warning($message = "ប្រតិបត្តិការមានបញ្ហា")
    {
        request()->session()->flash('alert', [
            'type' => "warning",
            'message' => $message,
        ]);
    }

    static function error($message = "ប្រតិបត្តិការបរាជ័យ")
    {
        request()->session()->flash('alert', [
            'type' => "danger",
            'message' => $message,
        ]);
    }

}
