@extends('layouts.master-auth')
@section('title') Iniciar Grupo Abdul @endsection

@section('content')
    <style>.chat-message-welcome {
            flex:0 0 auto;
            margin:0 -15px auto;
            border-bottom:10px solid #efefef;
            padding:20px;
            text-align:center
        }
        .chat-message-welcome .title {
            font-weight:600
        }
        .chat-message-welcome p {
            margin-bottom:5px
        }
        .chat__footer {
            margin:5px -15px 0
        }
        .chat-attachment {
            display:flex;
            font-weight:600;
            align-items:center;
            padding:10px 15px;
            border-top:1px solid #d9d9d9;
            color:#222!important
        }
        .chat-attachment .svgIcon {
            flex:0 0 auto
        }
        .chat-attachment:last-of-type {
            padding:10px 15px 0
        }
        .chat-attachmentIcon {
            flex:0 0 auto;
            margin-right:10px
        }
        .chat-warning {
            position:absolute;
            top:40px;
            width:100%;
            height:100%;
            background-color:#000000bf
        }
        .chat-warning-content {
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            background-color:#fff;
            padding:25px 14px;
            text-align:center;
            width:285px;
            border-radius:3px;
            box-sizing:border-box
        }
        .chat-warning-close {
            font-size:1.625rem;
            position:absolute;
            top:8px;
            right:15px;
            color:#d9d9d9;
            cursor:pointer
        }
        .chat-warning-close:hover {
            color:#6c6c6c
        }
        .chat-legal {
            font-size:.875rem;
            line-height:1.3125rem
        }
        .chat-legal a {
            display:block;
            color:#8c8c8c;
            text-decoration:underline
        }
        .chat__separator {
            font-size:.875rem;
            line-height:1.3125rem;
            color:#8c8c8c;
            text-align:center;
            margin:15px 0;
            text-transform:uppercase
        }
        .chat__timestamp {
            font-size:.6875rem;
            line-height:1rem;
            color:#8c8c8c;
            text-align:right;
            margin-top:3px
        }
        .chat-message {
            overflow:hidden;
            margin:0 0 15px
        }
        .chat-message .avatar {
            margin-top:5px
        }
        .content+.chat-message {
            margin-top:30px
        }
        .chat-message-avatar {
            width:36px;
            height:36px
        }
        .chat-message-avatar img {
            width:100%;
            height:100%;
            border-radius:50%
        }
        .chat-message-avatar.shape-square {
            height:40px;
            width:40px;
            border-radius:3px;
            position:relative;
            overflow:hidden
        }
        .chat-message-avatar.shape-square img {
            transform:translate(-50%);
            height:40px;
            border-radius:0;
            left:50%;
            display:block;
            position:relative;
            width:auto;
            max-width:inherit
        }
        .chat-message-avatar.avatar-alias {
            min-width:36px
        }
        .chat-message-avatar.avatar-vendor img {
            width:auto;
            max-height:36px;
            height:inherit;
            border-radius:4px
        }
        .chat-message-globe {
            max-width:70%;
            padding:15px;
            background-color:#fff;
            border-radius:6px;
            margin-bottom:5px;
            position:relative;
            display:inline-block;
            word-break:normal;
            word-wrap:break-word
        }
        .chat-message-globe p {
            margin-bottom:0
        }
        .chat-message-globe a {
            color:#0072c5;
            text-decoration:none;
            font-size:inherit!important
        }
        .chat-message-globe a.legal {
            color:#6c6c6c;
            text-decoration:underline
        }
        .chat-message-globe ul,
        .chat-message-globe ol {
            padding-left:15px
        }
        .chat-message-globe ul li {
            list-style-type:disc
        }
        .chat-message-globe ol li {
            list-style-type:decimal
        }
        .chat-message-globe p.chat-message-globe__title {
            margin-bottom:10px;
            font-weight:400;
            text-transform:uppercase;
            letter-spacing:.0625rem
        }
        .chat-message-globe p.chat-message-globe__infoSolic {
            margin-bottom:5px
        }
        .chat-message-globe p.chat-message-globe__infoSolic .svgIcon {
            margin-right:10px
        }
        .chat-message-globe p.chat-message-globe__infoSolic:last-child {
            margin-bottom:10px
        }
        .chat__link {
            color:#0072c5;
            display:block;
            margin:5px 0;
            word-break:break-word
        }
        .chat__figure {
            min-height:150px
        }
        .chat__img {
            width:auto;
            height:150px;
            border-radius:4px
        }
        .chat-message-globe .btn-outline {
            margin:5px 0;
            display:block;
            text-align:center;
            cursor:pointer
        }
        .message-outcome {
            flex:0 0 auto
        }
        .message-outcome .chat-message-avatar,
        .message-outcome .chat-message-globe {
            float:right
        }
        .message-outcome .chat-message-avatar {
            margin-left:13px
        }
        .message-outcome .chat-message-globe {
            border-radius:10px;
            background-color:#fef4f1;
            border:1px solid #0072c5
        }
        .message-outcome .chat-message-globe.note-message {
            background-color:#fff7e1;
            border:1px solid #ffd967
        }
        .note-message .chat-message-avatar {
            display:none
        }
        .note-message .chat-message-globe {
            float:right
        }
        .note-message .chat-message-avatar {
            margin-left:13px
        }
        .note-message .chat-message-globe {
            border-radius:10px 0 10px 10px;
            background-color:#fff7e1
        }
        .message-income {
            flex:0 0 auto
        }
        .message-income .message-income {
            margin-bottom:15px
        }
        .message-income .chat-message-globe,
        .message-income .chat-message-avatar {
            float:left
        }
        .message-income .chat-message-avatar {
            margin-right:13px
        }
        .message-income .chat-message-globe {
            border-radius:10px;
            border:1px solid #d9d9d9
        }
        .message-outcome+.message-income,
        .message-income+.message-outcome {
            flex:0 0 auto
        }
        .message-income.chat-aggregate {
            margin:0 0 15px 49px
        }
        .chat-aggregate .chat-message-globe {
            max-width:82.5%
        }
        .chat-message-send form,
        .chat-btn-new-message {
            padding:1.5rem 1rem
        }
        .chat-message-send form button.btn,
        .chat-btn-new-message button.btn {
            margin:0
        }
        .chat-message-send {
            border-top:1px solid #f8f8f8;
            position:absolute;
            bottom:0;
            width:100%;
            box-sizing:border-box
        }
        .chat-message-send__messageInput {
            vertical-align:middle;
            -webkit-appearance:none;
            appearance:none;
            box-sizing:border-box;
            border:0;
            background-color:#fff;
            resize:none;
            outline:none;
            width:100%;
            margin-right:1rem
        }
        .chat-message-send__messageInput[readonly=readonly]::placeholder {
            opacity:.5
        }
        .chat-message-send__messageInput::-webkit-inner-spin-button,
        .chat-message-send__messageInput::-webkit-outer-spin-button {
            -webkit-appearance:none;
            appearance:none;
            margin:0
        }
        .chat-message-send__messageSubmit {
            background:#0072c5;
            box-sizing:border-box;
            border-radius:3px;
            cursor:pointer;
            color:#fff;
            height:2rem;
            width:2rem
        }
        .chat-message-send__messageSubmit--disabled {
            opacity:.2;
            cursor:not-allowed;
            pointer-events:none
        }
        .chat-message-send input[type=text] {
            width:100%
        }
        .chat-message-send input[type=submit] {
            -webkit-appearance:none;
            appearance:none;
            color:#0072c5;
            font-weight:600;
            background-color:initial;
            border:0;
            padding:0;
            margin:5px 0
        }
        .chat-message-send .alert-error {
            border:none;
            padding:10px 15px;
            font-size:.75rem
        }
        .chat-message-send .composer-textarea-container {
            border:1px solid #d9d9d9;
            border-radius:.5rem;
            padding:12px
        }
        .chat-send-hint {
            font-size:.6875rem;
            line-height:1rem;
            color:#efefef;
            margin-top:5px;
            text-align:right;
            min-height:20px;
            transition:color .4s ease-out
        }
        .chat-send-hint.active {
            color:#8c8c8c
        }
        .chatQuickReply {
            text-align:left;
            padding:0 0 15px
        }
        .chatQuickReply__input {
            display:inline-block;
            margin:5px;
            padding:3px 15px;
            background:#fff;
            border:1px solid #fff;
            border-radius:16px;
            cursor:pointer;
            box-shadow:0 2px 5px #a5a5a580
        }
        .chatQuickReply__input:hover,
        .chatQuickReply__input--selected {
            border:1px solid #0072c5;
            background:#fef4f1
        }
        .loadingMessages {
            background-color:#f8f8f8;
            float:left;
            border-radius:8px 8px 8px 0;
            padding:5px 0;
            width:50px;
            text-align:center;
            margin-bottom:10px
        }
        .loadingMessages__item {
            display:inline-block;
            vertical-align:middle;
            animation:blink 1.4s infinite ease-in-out both;
            width:6px;
            height:6px;
            background-color:#0072c5;
            border-radius:100%;
            margin-right:2px
        }
        .loadingMessages__item:nth-child(2) {
            animation-delay:.2s
        }
        .loadingMessages__item:nth-child(3) {
            animation-delay:.4s
        }
        .pusher-container {
            position:fixed;
            bottom:20px;
            right:20px;
            z-index:1053
        }
        .pusher-container.fadeout {
            pointer-events:none
        }
        .pusher-container.fadeout .chat-launcher-button {
            pointer-events:all
        }
        .pusher-zfix .pusher-container,
        .pusher-zfix .chat-conversation {
            z-index:1030
        }
        .chat-messages {
            position:absolute;
            width:100%;
            padding:0;
            margin-bottom:70px;
            bottom:0;
            top:45px;
            box-sizing:border-box
        }
        .chat-messages__inner {
            overflow-y:auto;
            padding:0 15px;
            display:flex;
            flex-direction:column;
            height:100%
        }
        .chat-messages--chatbot {
            display:flex;
            flex-direction:column;
            justify-content:flex-end;
            padding:0 0 106px;
            margin-bottom:auto;
            bottom:0;
            top:45px
        }
        .chat-header {
            background-color:#fff;
            border-bottom:1px solid #d9d9d9;
            padding:10px 15px;
            text-align:center;
            position:relative
        }
        .chat-name {
            display:block;
            text-overflow:ellipsis;
            overflow:hidden;
            white-space:nowrap;
            width:210px;
            font-weight:600
        }
        .chat-controls {
            position:absolute;
            top:0
        }
        .chat-controls.chat-controls-left {
            left:0
        }
        .chat-controls.chat-controls-right {
            right:0
        }
        .chat-control-btn {
            padding:10px 15px;
            display:inline-block;
            cursor:pointer
        }
        .chat-ui {
            width:16px;
            height:16px;
            background:url(/images/chat-ui.png) no-repeat top left;
            background-size:16px;
            display:inline-block;
            vertical-align:middle
        }
        .chat-ui.chat-max {
            background-position:0 -16px
        }
        .chat-ui.chat-close {
            background-position:0 -32px
        }
        .chat-ui.chat-menu {
            background-position:0 -48px
        }
        .chat-ui.chat-refresh {
            background:url(/images/refresh.svg) no-repeat;
            background-size:14px
        }
        .chat-controls .chat-message-count {
            top:5px;
            right:0;
            height:18px;
            min-width:10px;
            line-height:1.125rem
        }
        .chat-launcher.hidden {
            display:none!important
        }
        .chat-launcher::after {
            content:"";
            display:block;
            clear:both
        }
        .chat-launcher-button {
            position:absolute;
            bottom:0;
            right:0;
            cursor:pointer;
            width:50px;
            height:50px
        }
        .chat-launcher-button img {
            border-radius:50%;
            width:100%;
            height:auto;
            overflow:hidden;
            box-shadow:0 5px 15px #0000004d
        }
        .chat-launcher-button--bottom {
            bottom:60px
        }
        .chat-launcher-button.closed {
            background:#fff url(/images/chat-launcher-button.png) no-repeat center center;
            background-size:cover
        }

        .chat-launcher-preview {
            float:right;
            font-weight:400;
            max-width:240px;
            min-height:22px;
            padding:10px 14px;
            margin-right:70px;
            color:#222;
            border-radius:10px;
            background:#fff;
            box-shadow:0 2px 10px 1px #0000004d;
            cursor:pointer;
            word-break:break-word
        }
        .chat-launcher-preview::after {
            content:"";
            width:10px;
            height:13px;
            background:url(/images/chat-launcher-preview.png) no-repeat center center;
            background-size:10px;
            position:absolute;
            bottom:10px;
            right:60px
        }
        .chat-conversation {
            transform:scale3d(0,0,0);
            transform-origin:bottom right;
            transition:opacity .15s linear .15s;
            opacity:0;
            background-color:#fff;
            border-left:1px solid #d9d9d9;
            z-index:999;
            position:fixed;
            bottom:0;
            right:0;
            width:370px;
            height:100% !important;
            box-shadow:0 0 4px #00000026
        }
        .chat-conversation.active {
            transform:scaleZ(1);
            opacity:1;
            pointer-events:all
        }
        .chat-loader {
            background-color:#efefef;
            z-index:100;
            position:fixed;
            right:0;
            width:100%;
            height:100%;
            overflow:hidden;
            -webkit-overflow-scrolling:touch
        }
        .chat-loader .chat-loader-content {
            position:relative;
            top:50%;
            transform:translateY(-70%)
        }
        .chat-loader .chat-loader-content .animation {
            width:200px;
            height:200px;
            margin:0 auto;
            text-align:center
        }
        .chat-loader .chat-loader-content .animation.default {
            background:url(/images/AR.gif) no-repeat scroll 50% 50% rgba(0,0,0,0);
            background-size:150px
        }
        .chat-loader .chat-loader-content .message {
            font-size:1.125rem;
            line-height:1.6875rem;
            text-align:center
        }
        .chat-history.active {
            background-color:#efefef;
            position:absolute;
            top:42px;
            bottom:0;
            width:100%;
            z-index:10
        }
        .chat-history.active .chat-messages {
            padding:0;
            top:0;
            bottom:67px
        }
        .chat-panel {
            background:#fff;
            border-bottom:1px solid #d9d9d9;
            padding:15px;
            position:relative;
            cursor:pointer
        }
        .chat-panel.chat-message {
            margin:0
        }
        .chat-panel .chat-message-avatar {
            margin-top:5px;
            position:relative
        }
        .chat-message-count {
            font-size:.8125rem;
            line-height:1.1875rem;
            line-height:1.3125rem;
            background:#0072c5;
            border:2px solid #fff;
            border-radius:50px;
            padding:0 4px;
            height:21px;
            min-width:13px;
            color:#fff;
            text-align:center;
            position:absolute
        }
        .chat-launcher .chat-message-count {
            bottom:35px;
            right:-8px
        }
        .chat-panel .chat-message-count {
            top:-10px;
            right:-8px
        }
        .chat-message-name,
        .chat-message-subject {
            display:block;
            color:#6c6c6c
        }
        .chat-message-name {
            font-weight:600;
            text-transform:capitalize
        }
        .chat-message-lastmessage {
            font-size:.875rem;
            line-height:1.3125rem;
            position:absolute;
            top:13px;
            right:20px;
            color:#8c8c8c
        }
        .chat-btn-new-message {
            background-color:#efefef;
            position:absolute;
            bottom:5px;
            left:0;
            width:100%;
            text-align:center;
            box-sizing:border-box
        }
        .transcription-chat {
            background:#fff;
            margin:0;
            padding-top:20px;
            padding-bottom:20px
        }
        .transcription-chat li {
            padding:5px 0 5px 15px
        }
        .transcription-content {
            border-bottom:1px solid #d9d9d9;
            padding:0 20px 5px 0
        }
        .transcription-username {
            font-weight:600;
            margin-bottom:0
        }
        .transcription-timestamp {
            font-size:.6875rem;
            line-height:1rem;
            font-weight:400;
            color:#8c8c8c;
            display:inline-block;
            margin-left:10px
        }
        .app-chat-writing-alert {
            display:none;
            padding:10px;
            text-align:center;
            font-style:italic;
            color:#efefef;
            background:#fff;
            border-bottom:1px solid #efefef
        }
        .modalChat {
            position:absolute;
            inset:0;
            width:100%;
            height:100%;
            z-index:1040;
            overflow:auto;
            outline:0;
            margin:0 auto;
            background:#fff;
            display:none
        }
        .modalChat__header {
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            align-items:center;
            padding:20px 20px 10px
        }
        .modalChat__content {
            position:fixed;
            padding:0 20px 20px;
            height:75%;
            width:100%;
            box-sizing:border-box;
            overflow-y:auto
        }
        .modalChat__close {
            color:#6c6c6c;
            text-decoration:underline;
            cursor:pointer
        }
        .modalChat__title {
            font-size:1.125rem;
            line-height:1.4375rem;
            font-weight:600;
            display:block
        }
        @media (min-width: 768px) {
            .modalChat__title {
                font-size:1.25rem;
                line-height:1.625rem
            }
        }
        .modalChat__input {
            transform:translateZ(0);
            color:#222;
            border:1px solid #8c8c8c;
            border-radius:2px;
            box-sizing:border-box;
            padding:10px;
            width:100%;
            margin-top:15px
        }
        .modalChat__results {
            position:relative
        }
        .modalChat__results ul {
            margin-bottom:0
        }
        .modalChat__results li {
            border-bottom:1px solid #d9d9d9;
            background-color:#fff;
            padding:15px;
            position:relative;
            cursor:pointer
        }
        .modalChat__results li:last-of-type {
            padding-bottom:65px;
            border-bottom:none
        }
        .modalChat__noResults {
            font-size:.8125rem;
            line-height:1.1875rem;
            text-align:center;
            display:block
        }
        .modalChat__resultsTitle {
            display:block;
            text-overflow:ellipsis;
            overflow:hidden;
            white-space:nowrap;
            font-weight:400
        }

        .chat-message-count {
            font-size: .8125rem;
            line-height: 1.1875rem;
            line-height: 1.3125rem;
            background: #0072c5;
            border: 2px solid #fff;
            border-radius: 50px;
            padding: 0 4px;
            height: 21px;
            min-width: 13px;
            color: #fff;
            text-align: center;
            position: absolute;
        }
        .bounce-once {
            -webkit-animation: bounce-once .6s ease-out;
            animation: bounce-once .6s ease-out;
        }

        #app-chat-container *,
        #app-chat-container ::before,
        #app-chat-container ::after {
            box-sizing: content-box;
        }
        .chat-conversation .chat-message-send__messageSubmit {
            display: flex;
            background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDE2IDE2IiBmaWxsPSJub25lIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzE5XzMwMykiPgo8cGF0aCBkPSJNMTUuNTA0MyAwLjQ1NDMxN0wxNS41MjQ0IDAuNDcyMzMyQzE1LjUzMTcgMC40Nzk1OTkgMTUuNTM4NyAwLjQ4NzA1NSAxNS41NDUzIDAuNDk0Njg0TDE1LjUyMzIgMC40NzI1NTZDMTUuNzc0OCAwLjcyMjc5MiAxNS44NjUgMS4wOTI4NSAxNS43NTYxIDEuNDMyOTJMMTEuMzk0NSAxNC44MjMxQzExLjE5NTEgMTUuNDIxNiAxMC42MjgyIDE1LjgxOTYgOS45OTc2MiAxNS44MDQxQzkuMzY3IDE1Ljc4ODYgOC44MjA0NyAxNS4zNjMxIDguNjQ2NzIgMTQuNzM5OUw3LjIxMzA5IDguNzgzOTRMMS4yNDYxNSA3LjM1MjM1QzAuNjM5ODk2IDcuMTgxMzIgMC4yMTU5MTcgNi42MzUxIDAuMjAwNjIyIDYuMDA1MzdDMC4xODUzMjggNS4zNzU2MyAwLjU4MjI5IDQuODA5NDcgMS4xODMwMiA0LjYwODA2TDE0LjU2MzcgMC4yNDQwNDNDMTQuODkyOCAwLjEzNjc4NiAxNS4yNTMzIDAuMjE4MDgzIDE1LjUwNDMgMC40NTQzMTdaTTE0LjY0MTEgMS45ODMyNkw4LjA3MTMxIDguNTUzMDNMOS41MDY4OCAxNC41MTY0QzkuNTcxNDcgMTQuNzQ3NiA5Ljc3OTQ5IDE0LjkwOTYgMTAuMDE5NSAxNC45MTU1QzEwLjI1OTUgMTQuOTIxNCAxMC40NzUzIDE0Ljc2OTkgMTAuNTUwMiAxNC41NDQ5TDE0LjY0MTEgMS45ODMyNlpNMTQuMDA3MyAxLjM2MDE1TDEuNDYyMTIgNS40NTE5OUMxLjIzNDYzIDUuNTI4MjcgMS4wODM0MiA1Ljc0MzkyIDEuMDg5MjUgNS45ODM3OEMxLjA5NTA3IDYuMjIzNjUgMS4yNTY1NyA2LjQzMTcxIDEuNDcwNDkgNi40OTI0Mkw3LjQ0Mjg2IDcuOTI0NTlMMTQuMDA3MyAxLjM2MDE1WiIgZmlsbD0id2hpdGUiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMF8xOV8zMDMiPgo8cmVjdCB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIGZpbGw9IndoaXRlIi8+CjwvY2xpcFBhdGg+CjwvZGVmcz4KPC9zdmc+);
            background-size: 1rem auto;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .chat-message-send .composer-textarea-container {
            border: 1px solid #d9d9d9;
            border-radius: .5rem;
            padding: 12px;
        }

        .chat-message-send__messageSubmit--disabled {
            opacity: .2;
            cursor: not-allowed;
            pointer-events: none;
        }

        .chat-conversation .flex-justify-space-between {
            justify-content: space-between;
        }

        .chat-conversation .flex-va-center {
            display: flex;
            align-items: center;
        }
        .chat-submit{
            background: none !important;
            border: none !important;
        }
    </style>
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Columna izquierda - Formulario -->
            <div class="col-lg-4 col-md-6 d-flex align-items-center justify-content-center bg-white">
                <div class="w-100" style="max-width: 380px; padding: 2rem;">
                    <!-- Logo -->
                    <div class="text-center mb-5">
                        <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="Grupo Abdul" height="60" class="mb-3">
                        <h2 class="fw-bold mb-1" style="color: #0c192c;">¡Bienvenido!</h2>
                        <p class="text-muted">Inicia sesión para continuar</p>
                    </div>

                    <!-- Mensajes de error -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background: linear-gradient(135deg, #ef476f, #d13b5f); color: white;">
                            <div class="d-flex align-items-center">
                                <i class="ri-error-warning-line fs-4 me-2"></i>
                                <div>
                                    <strong>Error de autenticación</strong><br>
                                    {{ $errors->first() }}
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Campo Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-secondary">Correo Electrónico</label>
                            <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;">
                                <i class="ri-mail-line text-primary"></i>
                            </span>
                                <input type="email"
                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="correo@ejemplo.com"
                                       required
                                       autofocus
                                       style="border-radius: 0 10px 10px 0; padding-left: 10px !important;">
                            </div>
                            @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="ri-information-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="password" class="form-label fw-semibold text-secondary">Contraseña</label>

                            </div>
                            <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;">
                                <i class="ri-lock-line text-primary"></i>
                            </span>
                                <input type="password"
                                       class="form-control border-start-0 ps-0 password-input @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required
                                       style="border-radius: 0 10px 10px 0; padding-left: 10px !important;">
                                <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-addon"
                                        type="button"
                                        style="z-index: 10; text-decoration: none;"
                                        onclick="togglePassword()">
                                    <i class="ri-eye-line" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="ri-information-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Recordar sesión -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="border-color: #0072c5;">
                                <label class="form-check-label text-secondary" for="remember">
                                    Recordar mi sesión
                                </label>
                            </div>
                        </div>

                        <!-- Botón de inicio -->
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fw-semibold"
                                style="border-radius: 10px; background: linear-gradient(135deg, #0072c5, #0059a3); border: none; box-shadow: 0 10px 20px rgba(0, 114, 197, 0.2);">
                        <span class="d-flex align-items-center justify-content-center">
                            <i class="ri-login-circle-line me-2 fs-5"></i>
                            Iniciar Sesión
                        </span>
                        </button>

                        <!-- Separador -->
                        <div class="position-relative text-center mb-4">
                            <hr class="text-muted opacity-25">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">o</span>
                        </div>

                    </form>

                    <!-- Footer -->
                    <div class="text-center mt-5">
                        <p class="small text-muted mb-0">
                            <i class="ri-copyright-line align-middle me-1"></i>
                            {{ date('Y') }} Grupo Abdul. Todos los derechos reservados.
                        </p>
                        <p class="small text-muted">
                            Desarrollado por <a href="https://CelisWeb.com.ve" target="_blank" class="text-primary text-decoration-none">CelisWeb</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Columna derecha - Hero/Branding -->
            <div class="col-lg-8 col-md-6 d-none d-md-block" style="background: linear-gradient(135deg, #2f4b9a 0%, #458cca 100%);">
                <div class="h-100 d-flex align-items-center justify-content-center p-5">
                    <div class="text-center text-white" style="max-width: 600px;">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="app-chat-container" class="pusher-container pusher-mobile   "  >
        <div id="app-bot-bot" data-fromtype="bot" data-fromid="bot" data-idconversation="null" data-id-question="1" data-id-flow="null" data-id-categ="null" data-id-sector="null" class="app-chat-container-top">
            <div class="chat-launcher app-chat-launcher"  style="display: block;"> <!-- onclick="activechat()" -->
                <div class="chat-launcher-button">
                    <img class="app-chat-avatar" src="/images/avatar.png" width="50" height="50" alt="">
                    <span class="app-chat-num-messages chat-message-count dnone bounce-once" style="display: none"></span>
                </div>
                <div class="chat-launcher-preview " style="display: none" >
                    <!---->
                    <div class="app-conversation-summary  firstmessage">
                        Hola!! Si deseas algun tipo de informaci&oacute;n puedes escribirnos al
                        <a  target="_blank" href="https://api.whatsapp.com/send/?phone=584125985396&text=Hola+Srs.+de+Sara+quisiera+obtener+informacion+de+sobre:+&type=phone_number&app_absent=0">+58 412 5985396</a>

                    </div>
                </div>
            </div>
            <div class="chat-conversation app-chat-conversation " data-initconversation="0">

                <div class="chat-header">
                    <div class="app-controls-menu chat-controls chat-controls-left">
                        <div class="">

                        </div>
                    </div>
                    <span class="app-chat-name chat-name">Asistente Virtual</span>
                    <div class="chat-controls chat-controls-left">
                        <span class="chat-control-btn app-chat-refresh">
                          <i class="icon icon-refresh-chat"></i>
                        </span>
                    </div>

                    <div class="chat-controls chat-controls-right" onclick="$('.app-chat-conversation').removeClass('active')">
                      <span class="chat-control-btn app-chat-min">
                        <span class="chat-ui chat-min"></span>
                      </span>

                    </div>
                </div>

                <div class="app-chat-history chat-history">
                    <div class="app-chat-conversations chat-messages"></div>
                </div>

                <div class="app-mobile-nel-scrollfix chat-messages chat-messages--chatbot app-scroll-calculate">
                    <div class="app-conversation-parts chat-messages__inner ">
                        <div class="chat-message-welcome">
                            <p>¿Tienes preguntas sobre nuestra empresa o productos? ¡Estoy para ayudarte!</p>
                            <div class="chat-legal">
                                <div class=" ">
                                    <a href="https://instagram.com/ciroenlinea" target="_blank" class="btn btn-primary btn-hover" style="color: white !important;">
                                        <i class="ph-instagram-logo align-middle me-1"></i> Siguenos en Instagram
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="chat__separator">
                            <span>{{\Carbon\Carbon::now()->format('d/m/Y')}}</span>
                        </div>
                        <div class="  app-income-message message-income "
                             id="chatMessages" >

                        </div>
                    </div>
                </div>

                <div class="composer-container chat-message-send app-chat-message-send">
                    <div class="app-chat-writing-alert"> </div>
                    <div class="composer-textarea-container flex-va-center flex-justify-space-between">
                        <input class="app-no-tiny app-chat-textarea chat-message-send__messageInput"
                               name="comment" id="message" placeholder="Escribe tu mensaje..." autocomplete="off"
                               onkeypress="if(event.key === 'Enter') sendMessage()">

                        <button  class="chat-submit"  id="sendButton" onclick="sendMessage()">
                            <span class="app-chat-form-submit chat-message-send__messageSubmit "></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            }
        }

        // Fill demo credentials
        function fillDemoCredentials() {
            document.getElementById('email').value = 'demo@grupoabdul.com';
            document.getElementById('password').value = 'Demo123';

            // Animación simple
            const btn = event.target;
            btn.innerHTML = '<i class="ri-check-line me-2"></i>Credenciales cargadas';
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = '<i class="ri-user-star-line me-2"></i>Usuario de demostración';
                btn.disabled = false;
            }, 2000);
        }

        // Validación del formulario
        (function() {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Efecto de hover en botones
        document.querySelectorAll('.btn-outline-secondary').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'all 0.3s ease';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        /* Animaciones */
        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 114, 197, 0.3) !important;
        }

        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(0, 114, 197, 0.1);
            border-color: #0072c5;
        }

        .input-group-text {
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-group-text {
            border-color: #0072c5;
        }

        /* Loading animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .col-lg-4 {
                padding: 2rem 1rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #0072c5;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0059a3;
        }

        /* Glassmorphism effects */
        .rounded-3 {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .rounded-3:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1) !important;
        }

        /* Checkbox personalizado */
        .form-check-input:checked {
            background-color: #0072c5;
            border-color: #0072c5;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 114, 197, 0.25);
        }

        /* Alert personalizado */
        .alert-danger {
            border: none;
            position: relative;
            overflow: hidden;
        }

        .alert-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-white, [class*="col-"] {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>

    <script>
        const chatMessages   = document.getElementById('chatMessages');
        const messageInput   = document.getElementById('message');
        const sendButton     = document.getElementById('sendButton');
        const csrfToken      = document.querySelector('meta[name="csrf-token"]').content;
        let   isLoading      = false;
        var   counter        = 1;
        var   firstmessage   = 0;
        let   conversationId = null; // Almacenar el ID de conversación

        async function sendMessage() {

            const message = messageInput.value.trim();

            if (!message || isLoading) return;

            // Mostrar mensaje del usuario
            addMessage(message, 'user', null);
            messageInput.value = '';

            // Mostrar indicador de escritura
            const typingId = showTypingIndicator();

            // Deshabilitar input mientras se procesa
            setLoading(true);

            try {
                const response = await fetch('{{ route("chatbot.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message,
                        conversation_id: conversationId
                    })
                });

                removeTypingIndicator(typingId);

                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }

                const data = await response.json();

                // Actualizar conversationId si es nuevo
                if (data.conversation_id && !conversationId) {
                    conversationId = data.conversation_id;
                }

                if (data.success) {
                    addMessage(data.reply, 'bot', data.timestamp);

                } else {
                    addMessage('Error: ' + data.reply, 'bot error-message', null);
                }

            } catch (error) {
                console.error('Error:', error);
                removeTypingIndicator(typingId);
                addMessage('Error de conexión. Por favor verifica tu internet e intenta de nuevo.', 'bot error-message', null);

            } finally {
                setLoading(false);
            }
        }

        function updateMessageCounter() {
            var counter = parseInt($('.bounce-once').html() || 0);
            $('.bounce-once').html(counter + 1);
            $('.bounce-once').show();

        }

        function addMessage(text, type,  timestamp = null) {
            const messageDiv = document.createElement('div');

            // Convertir URLs en links
            var linkedText = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-600 underline">$1</a>');

            // Si no se proporciona timestamp, usar hora actual
            if (!timestamp) {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                timestamp = `${hours}:${minutes}`;
            }

            // Ocultar mensajes de bienvenida por defecto si es necesario

            if (type === 'user') {
                messageDiv.className = 'chat-message message-income';
                messageDiv.innerHTML = `
                <div class="chat-message-globe" style="text-align: right; float: right; background-color: #e3f2fd;">
                    ${linkedText}
                    <div class="chat__timestamp">${timestamp}</div>
                </div>
            `;
            } else {
                messageDiv.className = 'chat-message message-income';
                messageDiv.innerHTML = `
                <div class="chat-message-avatar">
                    <img src="/images/avatar.png" width="50" height="50" alt="Ciro">
                </div>
                <div class="chat-message-globe">
                    ${linkedText}
                    <div class="chat__timestamp">${timestamp}</div>
                </div>
            `;
            }

            chatMessages.appendChild(messageDiv);
            scrollToBottom();
            updateMessageCounter();
        }

        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot-message typing-indicator';
            typingDiv.id = 'typing-' + Date.now();
            typingDiv.innerHTML = '<span class="dot-animation">Escribiendo</span>';
            chatMessages.appendChild(typingDiv);
            scrollToBottom();
            return typingDiv.id;
        }

        function removeTypingIndicator(id) {
            const typingDiv = document.getElementById(id);
            if (typingDiv) {
                typingDiv.remove();
            }
        }

        function scrollToBottom() {
            $('.app-conversation-parts.chat-messages__inner').scrollTop(
                $('.app-conversation-parts.chat-messages__inner')[0]?.scrollHeight || 0
            );

            // Backup para otros contenedores
            $('.app-chat-conversations.chat-messages').scrollTop(
                $('.app-chat-conversations.chat-messages')[0]?.scrollHeight || 0
            );

        }

        function setLoading(loading) {
            isLoading = loading;
            messageInput.disabled = loading;
            sendButton.disabled = loading;
            sendButton.textContent = loading ? 'Enviando...' : 'Enviar';

            if(!loading) {
                $('#sendButton').html('<span class="app-chat-form-submit chat-message-send__messageSubmit "></span>');
                $('#message').focus().select();
            }
        }

        // Agregar animación para el indicador de escritura
        const style = document.createElement('style');
        style.textContent = `
        .dot-animation::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
            display: inline-block;
            width: 0;
            overflow: hidden;
            vertical-align: bottom;
        }

        @keyframes dots {
            0%, 20% { width: 0; }
            40% { width: 0.5em; }
            60% { width: 1em; }
            80%, 100% { width: 1.5em; }
        }
    `;
        document.head.appendChild(style);

        $( document ).ready(function() {
            //initializeChat();

            $('.chat-launcher-preview').show();
        });

        function activechat(){
            $('.app-chat-conversation').addClass('active');
            $('.chat-launcher-preview').hide();
            $('#message').select();
            setTimeout(500,scrollToBottom());

        }

        async function initializeChat() {
            try {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                timestamp = `${hours}:${minutes}`;

                const response = await fetch('{{ route("chat.initialize") }}', {
                    method: 'POST',
                    data:{timestamp:timestamp},
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    conversationId = data.conversation_id;

                    // Si hay historial, cargarlo
                    if (data.history && data.history.length > 0) {
                        // Limpiar mensajes de bienvenida por defecto
                        // $('.chat-message-welcome').hide();

                        // Cargar historial
                        data.history.forEach(msg => {
                            if (msg.sender === 'assistant') {
                                if(firstmessage==0){
                                    $('.firstmessage').html(msg.message);
                                    firstmessage = 1;
                                    $('.chat-launcher-preview').show();
                                }
                                addMessage(msg.message, 'bot', msg.time);
                            } else if (msg.sender === 'user') {
                                addMessage(msg.message, 'user', msg.time);
                            }
                        });


                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error inicializando chat:', error);
            }
        }
    </script>
@endsection
