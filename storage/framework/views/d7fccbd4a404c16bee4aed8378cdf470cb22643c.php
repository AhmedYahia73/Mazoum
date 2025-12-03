<!DOCTYPE html>

<html lang="ar" class="light-style customizer-hide" dir="rtl" data-theme="theme-default" data-assets-path="<?php echo e(asset('admin/assets')); ?>/" data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title> تسجيل الدخول </title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('admin/assets')); ?>/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->

    

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />

    <link rel="stylesheet" href="<?php echo e(asset('bootstrap-sweetalert/sweetalert.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('arabic_font')); ?>/droidarabickufi.css"/>

    <style>

        body, html, h1, h2, h3, h4, h5, h6, p, a, li, .m-portlet__head-text, .btn-primary {
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }

        .select2-container {
            z-index: 1 !important;
        }

        #template-customizer {
            display: none !important;
        }

        .sweet-alert h2 {
            color: #777;
        }

        .sweet-alert .cancel {
            background: red;
            color: #fff;
        }

        .card-footer {
            padding-top: 5px !important;
        }

        .card-title {
            margin-bottom: 0 !important;
        }

        .card-header:first-child {
            padding-bottom: 0 !important;
        }

        .ticket_table td {
            border-bottom: 0 !important;
            padding: 6px 0px !important;
        }

        .select2-container {
            z-index: 1 !important;
        }

        #template-customizer {
            display: none
        }

        /*
        body { background: #f3f4f4 }
        */
    </style>


    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo e(asset('admin/assets')); ?>/js/config.js"></script>

  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="<?php echo e(asset('assistant_panel')); ?>" class="app-brand-link gap-2">
                    <img src="<?php echo e(asset('logo.png')); ?>" style="height: 100px">
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2"> مرحبا بك في لوحة التحكم </h4>

              <p class="mb-4">
                الرجاء تسجيل الدخول إلى حسابك وبدء المغامرة
              </p>

              <?php echo Form::open(array('url' => 'assistant_panel/login')); ?>


                <div class="mb-3">
                  <label for="email" class="form-label">
                    البريد الألكتروني
                  </label>
                  <input type="text" class="form-control" id="email" name="email" value="<?php echo e(old('email')); ?>" required placeholder="البريد الألكتروني" autofocus />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">كلمة المرور</label>
                    <a style="display: none" href="<?php echo e(asset('forgot/password')); ?>">
                      <small>Forgot Password?</small>
                    </a>
                  </div>
                  <div class="input-group input-group-merge">
                    <input type="password" id="password" class="form-control" name="password" required placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                    <span class="input-group-text cursor-pointer">
                        <i class="bx bx-hide"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" checked id="remember-me" />
                    <label class="form-check-label" for="remember-me"> تذكرني </label>
                  </div>
                </div>
                <div class="mb-3">
                  <button type="submit" class="btn btn-primary d-grid w-100" type="submit">
                    تسجيل الدخول
                </button>
                </div>

                <?php echo Form::close(); ?>



            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/jquery/jquery.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/popper/popper.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/js/bootstrap.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/hammer/hammer.js"></script>

    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/i18n/i18n.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="<?php echo e(asset('admin/assets')); ?>/js/main.js"></script>

    <!-- Page JS -->
    <script src="<?php echo e(asset('admin/assets')); ?>/js/pages-auth.js"></script>
  </body>
</html>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/layouts/login.blade.php ENDPATH**/ ?>