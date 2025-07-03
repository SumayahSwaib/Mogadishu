<?php
use App\Models\Utils;
$ent = Utils::ent();
?><style>
    .sidebar {
        background-color: #FFFFFF;
    }

    .content-header {
        background-color: #F9F9F9;
    }

    .sidebar-menu .active {
        border-left: solid 5px {{ $ent->color }} !important;
        color: {{ $ent->color }} !important;
    }


    .navbar,
    .logo,
    .sidebar-toggle,
    .user-header,
    .btn-dropbox,
    .btn-twitter,
    .btn-instagram,
    .btn-primary,
    .navbar-static-top {
        background-color: {{ $ent->color }} !important;
    }

    .dropdown-menu {
        border: none !important;
    }

    .box-success {
        border-top: {{ $ent->color }} .5rem solid !important;
    }

    :root {
        --primary: {{ $ent->color }};
    }

    /* overide all primary colours and success colouof text, bg, buttons to have our primary cooyr variable */
    .btn-primary,
    .bg-primary,
    .text-primary,
    .alert-primary,
    .badge-primary,
    .btn-success,
    .bg-success,
    .text-success,
    .alert-success,
    .badge-success {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #fff !important;
    }

    a.text-primary,
    a.text-success {
        color: var(--primary) !important;
    }

    a.text-primary:hover,
    a.text-success:hover {
        color: #fff !important;
        background-color: var(--primary) !important;
    }

    .btn-primary:hover,
    .btn-success:hover,
    .btn-primary:focus,
    .btn-success:focus {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #fff !important;
    }
    /* Override border colors for primary and success elements */
    .border-primary,
    .border-success,
    .alert-primary,
    .alert-success,
    .badge-primary,
    .badge-success {
        border-color: var(--primary) !important;
        color: #fff !important;
    }
</style>
