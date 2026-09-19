<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use App\Models\Utils;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Auth;
use App\Admin\Extensions\Nav\Shortcut;
use App\Admin\Extensions\Nav\Dropdown;
use Encore\Admin\Form;

Utils::system_boot();


Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {

    $u = Auth::user();
    $navbar->left(view('admin.search-bar', [
        'u' => $u
    ]));

    $navbar->left(Shortcut::make([
        'New Renting' => 'rentings/create',
        'New Tenant' => 'tenants/create',
        //'New Apartment' => 'rooms/create',
        // 'New house' => 'houses/create',
        // 'New landlord' => 'landloads/create',
        /*  'Products or Services' => 'products/create',
        'Jobs and Opportunities' => 'jobs/create',
        'Event' => 'events/create', */
    ], 'fa-plus')->title('CREATE NEW'));
    /*     $navbar->left(Shortcut::make([
        'Candidate' => 'people/create', 
    ], 'fa-wpforms')->title('Register new')); */



    /*     $navbar->right(Shortcut::make([
        'How to register a new candidate' => '',
        'How to change  candidate\'s status' => '',
    ], 'fa-question')->title('HELP')); */
});


Form::init(function (Form $form) {
    //$form->disableEditingCheck();
    // $form->disableCreatingCheck();
    $form->disableViewCheck();
    $form->disableReset();
    //$form->disableCreatingCheck();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
    });

    // Security: reject uploads of executable/script files on every admin form.
    // The upload dirs also deny PHP execution at the web-server level (.htaccess),
    // so this is defence-in-depth against a polyglot-image upload being stored
    // with a .php extension (the July 2026 incident vector).
    $form->saving(function (Form $form) {
        $blocked = [
            'php', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'php8',
            'phtml', 'pht', 'phar', 'phps', 'phpt', 'phtm',
            'cgi', 'pl', 'py', 'sh', 'bash', 'asp', 'aspx', 'jsp', 'jspx',
            'exe', 'com', 'htaccess', 'htpasswd', 'ini', 'shtml', 'svg',
        ];
        $flatten = function ($files) use (&$flatten) {
            $out = [];
            foreach ((array) $files as $f) {
                if (is_array($f)) {
                    $out = array_merge($out, $flatten($f));
                } elseif ($f) {
                    $out[] = $f;
                }
            }
            return $out;
        };
        foreach ($flatten(request()->allFiles()) as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, $blocked, true)) {
                throw new \Exception('Upload blocked: files of type ".' . $ext . '" are not permitted.');
            }
        }
    });
});


Encore\Admin\Form::forget(['map', 'editor']);
Admin::css(url('/assets/css/bootstrap.css'));
Admin::css('/assets/css/styles.css');
Admin::css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
