<!DOCTYPE html>

<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../../assets/"
    data-template="horizontal-menu-template-no-customizer"
>
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Chat - Apps | Vuexy - Bootstrap Admin Template</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/fontawesome.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/tabler-icons.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/flag-icons.css')}}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendor/css/rtl/core.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/css/rtl/theme-default.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/demo.css')}}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/node-waves/node-waves.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/typeahead-js/typeahead.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}"/>

    <!-- Page CSS -->

    <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-chat.css')}}"/>
    <!-- Helpers -->
    <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('assets/js/config.js')}}"></script>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card">
                        <div class="row">
                            <div class="col-12">
                                <ul class="list-unstyled chat-contact-list" id="chat-list">
                                    <li class="chat-contact-list-item chat-contact-list-item-title">
                                        <h5 class="text-primary mb-0">Channels</h5>
                                    </li>
                                    @if (session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    @if(count($channels) > 0)
                                    @foreach($channels as $channel)
                                    <li class="chat-contact-list-item">
                                        <a href="{{route('channel page',['id'=>$channel->id])}}" class="d-flex align-items-center">
                                            <div class="flex-shrink-0 avatar">
                                                <img src="{{'https://avindev.com/mahtab/public/'.$channel->profile_path}}" alt="Avatar"
                                                     class="rounded-circle"/>
                                            </div>
                                            <div class="chat-contact-info flex-grow-1 ms-2">
                                                <h6 class="chat-contact-name text-truncate m-0">{{$channel->title}}</h6>
                                                <p class="chat-contact-status text-muted text-truncate mb-0">
                                                    <i class="ti ti-users"></i> {{$channel->members_count}}
                                                </p>
                                            </div>
                                            <small class="text-muted mb-auto">{{count($channel->messages)}} <i class="ti ti-message"></i></small>
                                        </a>
                                    </li>
                                    @endforeach
                                    @else
                                        <p class="text-center text-muted">There are not channels added</p>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-12">
                                        <form action="{{route('add channel')}}" method="POST">
                                            <div class="input-group">
                                                <input type="text" name="id" class="form-control" placeholder="Channel's username" aria-label="Channel's username">
                                                <button class="btn btn-outline-primary waves-effect" type="submit" id="button-addon2">Add Channel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
            </div>
            <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
    </div>

</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>

<!--/ Layout wrapper -->
<style>

    .app-chat {
        height: 90vh !important;
    }

    .app-chat .app-chat-history {
        height: 90vh !important;
    }

    .app-chat .app-chat-contacts {
        height: 90vh !important;
        width: 100% !important;
    }

    .chat-history-wrapper {
        height: 90vh !important;
    }

    .sidebar-body {
        height: 90vh !important;
    }

    .chat-history-body {
        height: 70vh !important;
    }
    .chat-contact-list li.chat-contact-list-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0.75rem;
        margin: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    .chat-contact-list li.chat-contact-list-item a {
        width: 100%;
    }
    .chat-contact-list li.chat-contact-list-item .avatar {
        border: 2px solid transparent;
        border-radius: 50%;
    }
    .chat-contact-list li.chat-contact-list-item .chat-contact-info {
        min-width: 0;
    }
    .chat-contact-list li.chat-contact-list-item .chat-contact-info .chat-contact-name {
        line-height: 1.5;
    }
    .chat-contact-list li.chat-contact-list-item small {
        white-space: nowrap;
    }
</style>
<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('assets/vendor/libs/node-waves/node-waves.js')}}"></script>

<script src="{{asset('assets/vendor/libs/hammer/hammer.js')}}"></script>
<script src="{{asset('assets/vendor/libs/i18n/i18n.js')}}"></script>
<script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>

<script src="{{asset('assets/vendor/js/menu.js')}}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>

<!-- Main JS -->
<script src="{{asset('assets/js/main.js')}}"></script>

<!-- Page JS -->
</body>
</html>
