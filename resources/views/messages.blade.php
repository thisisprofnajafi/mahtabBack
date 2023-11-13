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
                    <div class="app-chat card overflow-hidden">
                        <div class="row g-0">

                            <!-- Chat History -->
                            <div class="col app-chat-history bg-body">
                                <div class="chat-history-wrapper">
                                    <div class="chat-history-header border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex overflow-hidden align-items-center">

                                                <div class="flex-shrink-0 avatar">
                                                    <img
                                                        src="{{'https://avindev.com/mahtab/public/'.$channel->profile_path}}"
                                                        alt="Avatar"
                                                        class="rounded-circle"
                                                        data-bs-toggle="sidebar"
                                                        data-overlay
                                                        data-target="#app-chat-sidebar-right"
                                                    />
                                                </div>
                                                <div class="chat-contact-info flex-grow-1 ms-2">
                                                    <h6 class="m-0">{{$channel->title}}</h6>
                                                    <small
                                                        class="user-status text-muted">{{$channel->members_count}}</small>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="chat-history-body bg-body">
                                        <ul class="list-unstyled  chat-history">
                                            @foreach($messages as $message)
                                                <li class="chat-message">
                                                    <div class="d-flex overflow-hidden">
                                                        <div class="chat-message-wrapper flex-grow-1">
                                                            <div class="chat-message-text">
                                                                @if($message->type == "text")
                                                                    <span>{{ $message->text }}</span>
                                                                @elseif($message->type == "photo")
                                                                    <img src="{{ asset($message->path) }}" alt="Photo"
                                                                         style="max-width: 100%">
                                                                    @if($message->caption)
                                                                        <span>{{ $message->caption }}</span>
                                                                    @endif
                                                                @elseif($message->type == "gif" || $message->type == "video" || $message->type == "audio" || $message->type == "voice" || $message->type == "sticker" || $message->type == "document")
                                                                    @if($message->type == "video")
                                                                        <video controls style="max-width: 100%">
                                                                            <source src="{{ asset($message->path) }}"
                                                                                    type="video/mp4">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                    @elseif($message->type == "audio" || $message->type == "voice")
                                                                        <audio controls style="max-width: 100%">
                                                                            <source src="{{ asset($message->path) }}"
                                                                                    type="audio/mpeg">
                                                                            Your browser does not support the audio tag.
                                                                        </audio>
                                                                    @elseif($message->type == "gif")
                                                                        <video controls style="max-width: 100%">
                                                                            <source src="{{ asset($message->path) }}"
                                                                                    type="video/mp4">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                    @elseif($message->type == "sticker")
                                                                        @if(pathinfo($message->path, PATHINFO_EXTENSION) == 'mp4')
                                                                            <video controls style="max-width: 100%">
                                                                                <source
                                                                                    src="{{ asset($message->path) }}"
                                                                                    type="video/mp4">
                                                                                Your browser does not support the video
                                                                                tag.
                                                                            </video>
                                                                        @else
                                                                            <img src="{{ asset($message->path) }}"
                                                                                 alt="Sticker" style="max-width: 100%">
                                                                        @endif
                                                                    @elseif($message->type == "document")
                                                                        <a href="{{asset($message->path)}}" download>Download
                                                                            Document</a>

                                                                    @else
                                                                        <a href="{{ asset($message->path) }}"
                                                                           target="_blank" rel="noopener noreferrer">
                                                                            {{ ucfirst($message->type) }}
                                                                        </a>
                                                                    @endif
                                                                    @if($message->caption)
                                                                        <span>{{ $message->caption }}</span>
                                                                    @endif
                                                                @endif
                                                            </div>

                                                            <div class="d-flex justify-content-between mt-2">
                                                                <div class="text-muted mt-1 mx-1">
                                                                    <small>{{$message->created_at->diffForHumans(null, true)}}
                                                                        ago</small>
                                                                </div>
                                                                <div class="forms d-flex g-3 mx-2">
                                                                    <i class="ti ti-trash"
                                                                       onclick="deleteMessage({{$message->id}} , {{$channel->id}})"></i>
                                                                    <i class="ti ti-refresh"
                                                                       onclick="refreshPage({{$message->id}} , {{$channel->id}})"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <!-- Chat message form -->
                                    <div class="chat-history-footer shadow-sm">
                                        <form
                                            class="form-send-message d-flex justify-content-between align-items-center">
                                            <input
                                                class="form-control message-input border-0 me-3 shadow-none"
                                                placeholder="Type your message here"
                                            />
                                            <div class="message-actions d-flex align-items-center">
                                                <label for="attach-doc" class="form-label mb-0">
                                                    <i class="ti ti-photo ti-sm cursor-pointer mx-3"></i>
                                                    <input type="file" id="attach-doc" hidden/>
                                                </label>
                                                <button class="btn btn-primary d-flex send-msg-btn">
                                                    <i class="ti ti-send me-md-1 me-0"></i>
                                                    <span class="align-middle d-md-inline-block d-none">Send</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /Chat History -->


                            <div class="app-overlay"></div>
                        </div>
                    </div>
                </div>
                <!--/ Content -->

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
<script src="{{asset('assets/js/app-chat.js')}}"></script>


<script>
    function deleteMessage(messageId, channelId) {
        fetch(`/public/api/channel/${channelId}/delete/${messageId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                // Add any other headers you may need, such as authorization
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    alert(data.message); // Show a success message
                    // Optionally update the UI to reflect the deleted message, e.g., remove the message element
                    // document.getElementById(`message-${messageId}`).remove();
                    refreshPage(); // Optionally refresh the page after deletion
                } else {
                    alert(data.message); // Show an error message
                }
            })
            .catch(error => console.error('Error:', error));
    }


    function refreshPage(messageId, channelId) {
        fetch(`/public/api/channel/${channelId}/restore/${messageId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Add any other headers you may need, such as authorization
            },
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Show a message to the user (you can replace this with any UI update logic)
                // Optionally update the UI to reflect the deleted message, e.g., remove the message element
                // document.getElementById(`message-${messageId}`).remove();
            })
            .catch(error => console.error('Error:', error));
    }
</script>


</body>
</html>
