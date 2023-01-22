<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/x-icon" href="https://presensi.unw.my.id/assets/img/logo-unw-favicon.png">
  <title><?= $title ?></title>

  <!-- <link href="/public/css/style.css" rel="stylesheet"> -->
  <link href="<?= base_url() ?>/assets/frontend/css/style.css" rel="stylesheet">
  <link href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/flowbite@1.4.7/dist/flowbite.min.css" />
  <!-- Append style -->
  <?= $this->renderSection('append-style'); ?>
  <!-- Ai css-->
  <link href="<?= base_url() ?>/assets/frontend/css/ai.css?version=0.1" rel="stylesheet">
</head>

<body>
  <?php
  $uri = service('uri');
  ?>
  <!-- Navbar -->
  <section class="shadow-sm">
    <nav class="container mx-auto p-5">
      <div class="flex items-center justify-between">
        <!-- Logo  -->
        <div>
          <a href="#" class="flex items-center">
            <img class="w-11 mr-2" src="<?= base_url() ?>/assets/img/unw.png" alt="">
            <h1 class="text-xl font-bold">PRESENSI UNW</h1>
          </a>
        </div>
        <!-- End Logo -->

        <?php if (logged_in()) : ?>
          <!-- User & Notification -->
          <?php
          $db      = \Config\Database::connect();
          $builder = $db->table('users');
          $user = $builder->getWhere(['id' => session('user_id')])->getRow();
          ?>
          <div class="flex items-center">
            <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar" class="flex items-center justify-between w-full py-2 pl-3 pr-4 font-medium text-gray-700">
              <!-- If User Login -->
              <span class="font-bold hidden lg:block"><?= $user->name ?></span>
              <!-- End If User Login -->
              <svg class="w-6 h-6 cursor-pointer ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
              </svg>
            </button>
            <!-- Dropdown menu -->
            <div id="dropdownNavbar" class="z-10 hidden bg-white divide-y divide-gray-100 rounded shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
              <ul class="py-1 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="<?= base_url('/profile') ?>" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Profil</a>
                </li>
              </ul>
              <div class="py-1">
                <a href="<?= base_url('/logout') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                  Logout
                </a>
              </div>
            </div>
          </div>
          <!-- End User & Notification -->
        <?php endif ?>
      </div>
    </nav>
  </section>
  <!-- End Navbar -->

  <!-- Background -->
  <section>
    <div class="h-72 bg-indigo-600">
      <div class="h-72 bg-right bg-no-repeat bg-50 lg:bg-contain">
        <div class="container mx-auto p-5">
          <div class="w-full flex flex-col items-center justify-between mx-auto lg:flex-row">
            <!-- Text Left -->
            <div class="">
              <h1 class="text-3xl text-center lg:text-5xl lg:text-left mt-5 font-semibold text-white"><?= $title ?></h1>
              <h1 class="text-sm text-center lg:text-md lg:text-left mt-3 text-white"><?= $subtitle ?></h1>
            </div>
            <!-- End Text Left -->
            <!-- Text Right -->
            <div class="">
              <h1 id="time" class="text-xl text-center lg:text-5xl lg:text-left mt-5 font-semibold text-white z-10">
              </h1>
              <!-- <h1 class="text-sm text-center lg:text-md lg:text-left mt-3 text-white">Hallo, Zack!</h1> -->
            </div>
            <!-- End Text Right -->
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End Background -->

  <!-- Content -->
  <section class="md:mb-20 lg:mb-5 -mt-32">
    <div class="container mx-auto p-5 w-full flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
      <!-- Sidebar -->
      <aside class="hidden md:block h-1/6 md:w-1/3 lg:w-1/4 rounded-lg border border-slate-300 bg-white">
        <h1 class="flex p-5 text-xl font-semibold">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7">
            </path>
          </svg>
          Menu
        </h1>
        <hr class="pt-2">
        <a href="<?= base_url('/') ?>" class="p-5 flex items-center text-sm  <?= $uri->getSegment(1) == '' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
          </svg>
          Presensi
        </a>
        <!-- <a href="<?= base_url('/presensi/scan') ?>" class="p-5 flex items-center text-sm  <?= $uri->getTotalSegments() > 1 && $uri->getSegment(2) == 'scan' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
          </svg>
          Scan Presensi
        </a> -->
        <a href="<?= base_url('/presensi/data') ?>" class="p-5 flex items-center text-sm  <?= $uri->getSegment(1) == 'presensi' && $uri->getSegment(2) == 'data' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
            </path>
          </svg>
          Data
        </a>
        <?php if (logged_in() && $isPicket) : ?>
          <a href="<?= base_url('/presensi-piket') ?>" class="p-5 flex items-center text-sm  <?= $uri->getSegment(1) == 'presensi-piket' && $uri->getSegment(2) == '' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
              </path>
            </svg>
            Presensi Piket
          </a>
          <a href="<?= base_url('/presensi-piket/data') ?>" class="p-5 flex items-center text-sm  <?= $uri->getSegment(1) == 'presensi-piket' && $uri->getSegment(2) == 'data' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
              </path>
            </svg>
            Data Piket
          </a>
        <?php endif ?>
        <!--<a href="<?= base_url('/presensi-acara') ?>" class="p-5 flex items-center text-sm  <?= $uri->getSegment(1) == 'presensi-acara' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">-->
        <!--  <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">-->
        <!--    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">-->
        <!--    </path>-->
        <!--  </svg>-->
        <!--  Presensi Acara-->
        <!--</a>-->
        <!--<a href="<?= base_url('/presensi-acara/data') ?>" class="p-5 flex items-center text-sm  <?= $uri->getSegment(1) == 'presensi-acara' && $uri->getSegment(2) == 'data' ? 'text-slate-900 font-semibold border-l-2 border-indigo-700' : 'text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700' ?> ">-->
        <!--  <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">-->
        <!--    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">-->
        <!--    </path>-->
        <!--  </svg>-->
        <!--  Data Acara-->
        <!--</a>-->
        <hr class="pt-2">
        <?php if (!logged_in()) : ?>
          <a href="<?= base_url('/login') ?>" class="p-5 flex items-center text-sm text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
              </path>
            </svg>
            Login
          </a>
        <?php else : ?>
          <a href="<?= base_url('/logout') ?>" class="p-5 flex items-center text-sm text-slate-500 hover:text-slate-900 hover:font-semibold hover:border-l-2 hover:border-indigo-700">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
              </path>
            </svg>
            Logout
          </a>
        <?php endif ?>
      </aside>
      <!-- Sidebar -->

      <!-- Content -->
      <?= $this->renderSection('content'); ?>
      <!-- End Main Content -->
    </div>
  </section>
  <!-- End Content -->

  <!-- Mobile Footer -->
  <section id="bottom-navigation" class="pt-2 md:hidden block fixed inset-x-0 bottom-0 z-10 bg-white shadow">
    <!-- <section id="bottom-navigation" class="block fixed inset-x-0 bottom-0 z-10 bg-white shadow"> -->
    <div id="tabs" class="flex justify-between">
      <a href="<?= base_url('/') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">
        <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
          </path>
        </svg>
        <span class="tab tab-home block text-xs">Presensi</span>
      </a>
      <a href="<?= base_url('/presensi/data') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">
        <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
          </path>
        </svg>
        <span class="tab tab-kategori block text-xs">Data</span>
      </a>
      <?php if (logged_in() && $isPicket) : ?>
        <a href="<?= base_url('/presensi-piket') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">
          <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
          </svg>
          <span class="tab tab-home block text-xs">Presensi Piket</span>
        </a>
        <a href="<?= base_url('/presensi-piket/data') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">
          <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
            </path>
          </svg>
          <span class="tab tab-kategori block text-xs">Data Piket</span>
        </a>
        <!--<a href="<?= base_url('/presensi-acara') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">-->
        <!--  <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">-->
        <!--    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">-->
        <!--    </path>-->
        <!--  </svg>-->
        <!--  <span class="tab tab-home block text-xs">Presensi Acara</span>-->
        <!--</a>-->
        <!--<a href="<?= base_url('/presensi-acara/data') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">-->
        <!--  <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">-->
        <!--    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">-->
        <!--    </path>-->
        <!--  </svg>-->
        <!--  <span class="tab tab-kategori block text-xs">Data Acara</span>-->
        <!--</a>-->
      <?php endif ?>
      <?php if (!logged_in()) : ?>
        <a href="<?= base_url('/login') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">
          <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
            </path>
          </svg>
          <span class="tab tab-explore block text-xs">Login</span>
        </a>
      <?php else : ?>
        <a href="<?= base_url('/logout') ?>" class="w-full focus:text-indigo-600 hover:text-indigo-600 justify-center inline-block text-center pt-2 pb-1">
          <svg class="w-6 h-6 inline-block mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
            </path>
          </svg>
          <span class="tab tab-explore block text-xs">Logout</span>
        </a>
      <?php endif ?>
    </div>
  </section>
  <!-- End Mobile Footer -->



  <!-- Main Modal -->
  <div id="small-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden rounded-lg fixed top-0 right-0 left-0 z-50 w-full md:inset-0
        h-modal">
    <div class="relative p-4 w-full max-w-xl h-modal">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            Tips
          </h3>
          <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="small-modal">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd">
              </path>
            </svg>
          </button>
        </div>
        <!-- Modal body -->
        <div class="p-5 space-y-6 rounded-lg">
          <h5 class="font-semibold text-lg">Perangkat terdeteksi diluar UNW</h5>
          <p class="text-sm">Jika presensi gagal karena perangkat terdeteksi diluar UNW buka terlebih
            dahulu
            maps lalu
            pastikan titik lokasi sudah berada di UNW
          </p>
          <img class="w-full" src="<?= base_url() ?>/assets/img/tips1.png" alt="">
        </div>
      </div>
    </div>
  </div>
  <!-- End Main Modal -->


  <!-- Footer -->
  <footer class="md:hidden lg:block p-4 bg-white sm:p-6 ">
    <hr class="border-indigo-100 sm:mx-auto lg:my-8" />
    <div class="items-center sm:flex sm:items-center sm:justify-between">
      <span class="text-sm text-gray-500 font-bold sm:text-center dark:text-gray-400">© 2022 <a href="#" target="_blank" class="hover:underline">E-Presensi UNW™</a>. All Rights Reserved.
      </span>
      <div class="flex mt-4 space-x-6 items-center sm:justify-center sm:mt-0">
        <a href="#" class="text-gray-500 hover:text-gray-900">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
          </svg>
        </a>
        <a href="#" class="text-gray-500 hover:text-gray-900">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
          </svg>
        </a>
      </div>
    </div>
  </footer>
  <!-- End Footer -->

  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
  <script src="<?= base_url() ?>/assets/frontend/js/script.js"></script>
  <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>
  <!-- Sweet ALert -->
  <script src="<?= base_url() ?>/assets/frontend/vendor/sweetalert/sweetalert2.all.min.js"></script>
  <!-- Append script -->
  <?= $this->renderSection('append-script'); ?>
  <!-- Ai Js -->
  <script src="<?= base_url() ?>/assets/frontend/js/ai.js"></script>
</body>

</html>