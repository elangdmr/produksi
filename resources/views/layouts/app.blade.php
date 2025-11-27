@php 
    $user   = auth()->user();
    $avatar = strtoupper(substr($user->name ?? 'U', 0, 2));
    $role   = strtolower($user->role ?? '');

    $isAdmin     = in_array($role, ['admin','administrator','superadmin'], true);
    $isProduksi  = ($role === 'produksi');
    $isPPIC      = ($role === 'ppic');
    $isQA        = ($role === 'qa');
    $isQC        = ($role === 'qc');

    // Brand click tujuan -> langsung ke Dashboard
    $brandHome = route('dashboard');

    // Helper: cek apakah salah satu route produksi sedang aktif
    $isProduksiMenuActive = request()->routeIs('show-permintaan')
        || request()->routeIs('mixing.*')
        || request()->routeIs('capsule-filling.*')
        || request()->routeIs('tableting.*')
        || request()->routeIs('coating.*')
        || request()->routeIs('primary-secondary.*')
        || request()->routeIs('qc-release.*');

    // Helper: cek menu After Secondary Pack
    // (Qty Batch + Job Sheet QC + Sampling + COA + Review + Release)
    $isAfterPackMenuActive = request()->is('qty-batch*')
        || request()->routeIs('qc-jobsheet.*')
        || request()->routeIs('sampling.*')
        || request()->routeIs('coa.*')
        || request()->routeIs('review.*')
        || request()->routeIs('release.*');
@endphp

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui" />
  <title>PT. SAMCO Farma</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/logo/logo.png') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    .main-menu .navigation > li.active > a,
    .main-menu .navigation > li > a:hover,
    .main-menu .navigation > li.open > a{
      background: linear-gradient(118deg,#dc3545,rgba(220,53,69,.7)) !important;
      box-shadow: 0 0 10px 1px rgba(220,53,69,.5) !important;
      color:#fff !important;
    }
    .main-menu .navigation > li.active > a i,
    .main-menu .navigation > li > a:hover i,
    .main-menu .navigation > li.open > a i{
      color:#fff !important;
    }
    .main-menu .navigation .menu-content > li.active > a{
      color:#dc3545 !important;
    }
    .main-menu .navigation .menu-content > li.active > a:before{
      background:#dc3545 !important;
    }
    .navbar-header .brand-text,
    .main-menu .brand-text{
      color:#dc3545 !important;
    }
  </style>

  {{-- Vendor CSS --}}
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
  {{-- DataTables --}}
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">

  {{-- Theme --}}
  <link rel="stylesheet" href="{{ asset('app-assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/bootstrap-extended.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/colors.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/components.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/themes/dark-layout.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/themes/bordered-layout.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/themes/semi-dark-layout.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

  {{-- Page --}}
  <link rel="stylesheet" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/pages/dashboard-ecommerce.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/charts/chart-apex.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/extensions/ext-component-toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/forms/form-validation.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-user.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/forms/pickers/form-pickadate.min.css') }}">

  {{-- Custom --}}
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern">
  {{-- Header --}}
  <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
      <div class="bookmark-wrapper d-flex align-items-center">
        <ul class="nav navbar-nav d-xl-none">
          <li class="nav-item">
            <a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a>
          </li>
        </ul>
        <ul class="nav navbar-nav bookmark-icons">
          {{-- Shortcut icon di header --}}
          @if($isAdmin || $isProduksi || $isPPIC)
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link"
                 href="{{ route('mixing.index') }}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="bottom"
                 title="Mixing">
                <i class="ficon" data-feather="sliders"></i>
              </a>
            </li>
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link"
                 href="{{ route('capsule-filling.index') }}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="bottom"
                 title="Capsule Filling">
                <i class="ficon" data-feather="droplet"></i>
              </a>
            </li>
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link"
                 href="{{ route('tableting.index') }}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="bottom"
                 title="Tableting">
                <i class="ficon" data-feather="layers"></i>
              </a>
            </li>
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link"
                 href="{{ route('coating.index') }}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="bottom"
                 title="Coating">
                <i class="ficon" data-feather="shield"></i>
              </a>
            </li>
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link"
                 href="{{ route('primary-secondary.index') }}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="bottom"
                 title="Primary &amp; Secondary Pack">
                <i class="ficon" data-feather="package"></i>
              </a>
            </li>
          @endif

          @if($isAdmin || $isQA)
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link"
                 href="{{ route('qc-release.index') }}"
                 data-bs-toggle="tooltip"
                 data-bs-placement="bottom"
                 title="QC Release">
                <i class="ficon" data-feather="check-circle"></i>
              </a>
            </li>
          @endif
        </ul>
      </div>

      <ul class="nav navbar-nav align-items-center ms-auto">
        <li class="nav-item dropdown dropdown-user">
          <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user"
             href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="user-nav d-sm-flex d-none">
              <span class="user-name fw-bolder">{{ $user->name }}</span>
              <span class="user-status">{{ $user->role }}</span>
            </div>
            <span class="avatar bg-light-primary">
              <div class="avatar-content">{{ $avatar }}</div>
            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
            <a class="dropdown-item" href="{{ route('show-profile') }}">
              <i class="me-50" data-feather="user"></i> Profile
            </a>
            <div class="dropdown-divider"></div>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item">
                <i class="me-50" data-feather="power"></i> Logout
              </button>
            </form>
          </div>
        </li>
      </ul>
    </div>
  </nav>

  {{-- Sidebar --}}
  <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
      <ul class="nav navbar-nav flex-row">
        <li class="nav-item me-auto">
          <a class="navbar-brand" href="{{ $brandHome }}">
            <span class="brand-logo">
              <img src="{{ asset('app-assets/images/logo/logo.png') }}" alt="">
            </span>
            <h2 class="brand-text">Samco Farma</h2>
          </a>
        </li>
      </ul>
    </div>
    <div class="shadow-bottom"></div>

    <div class="main-menu-content">
      <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

        {{-- DASHBOARD --}}
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
            <i data-feather="home"></i>
            <span class="menu-title text-truncate">Dashboard</span>
          </a>
        </li>

        {{-- ===================== MENU ADMIN ===================== --}}
        @if ($isAdmin)
          {{-- USER MANAGEMENT --}}
          <li class="navigation-header">
            <span>User Management</span><i data-feather="more-horizontal"></i>
          </li>

          <li class="nav-item">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="user"></i>
              <span class="menu-title text-truncate">User</span>
            </a>
            <ul class="menu-content">
              {{-- PRODUKSI --}}
              <li class="{{ request()->is('show-produksi*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-produksi') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Produksi</span>
                </a>
              </li>
              {{-- PPIC --}}
              <li class="{{ request()->is('show-ppic*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-ppic') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">PPIC</span>
                </a>
              </li>
              {{-- QC --}}
              <li class="{{ request()->is('show-qc*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-qc') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">QC</span>
                </a>
              </li>
              {{-- QA --}}
              <li class="{{ request()->is('show-qa*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-qa') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">QA</span>
                </a>
              </li>
            </ul>
          </li>

          {{-- MASTER DATA --}}
          <li class="navigation-header">
            <span>Master Data</span><i data-feather="more-horizontal"></i>
          </li>

          {{-- Master Produk --}}
          <li class="nav-item {{ request()->routeIs('produksi.*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{{ route('produksi.index') }}">
              <i data-feather="box"></i>
              <span class="menu-title text-truncate">Master Produk Produksi</span>
            </a>
          </li>

          {{-- PRODUKSI --}}
          <li class="navigation-header">
            <span>Produksi</span><i data-feather="more-horizontal"></i>
          </li>

          {{-- Proses Produksi (submenu) --}}
          <li class="nav-item {{ $isProduksiMenuActive ? 'active open' : '' }}">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="activity"></i>
              <span class="menu-title text-truncate">Proses Produksi</span>
            </a>
            <ul class="menu-content">
              {{-- Jadwal Produksi --}}
              <li class="{{ request()->routeIs('show-permintaan') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-permintaan') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Weighing (WO)</span>
                </a>
              </li>

              {{-- Mixing --}}
              <li class="{{ request()->routeIs('mixing.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('mixing.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Mixing</span>
                </a>
              </li>

              {{-- Capsule Filling --}}
              <li class="{{ request()->routeIs('capsule-filling.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('capsule-filling.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Capsule Filling</span>
                </a>
              </li>

              {{-- Tableting --}}
              <li class="{{ request()->routeIs('tableting.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('tableting.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Tableting</span>
                </a>
              </li>

              {{-- Coating --}}
              <li class="{{ request()->routeIs('coating.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('coating.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Coating</span>
                </a>
              </li>

              {{-- Primary & Secondary Pack --}}
              <li class="{{ request()->routeIs('primary-secondary.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('primary-secondary.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Primary &amp; Secondary Pack</span>
                </a>
              </li>

              {{-- QC Release --}}
              <li class="{{ request()->routeIs('qc-release.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qc-release.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">QC Release</span>
                </a>
              </li>
            </ul>
          </li>

          {{-- BARANG SETELAH PACK --}}
          <li class="navigation-header">
            <span>Barang Setelah Pack</span><i data-feather="more-horizontal"></i>
          </li>

          <li class="nav-item {{ $isAfterPackMenuActive ? 'active open' : '' }}">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="archive"></i>
              <span class="menu-title text-truncate">After Secondary Pack</span>
            </a>
            <ul class="menu-content">
              {{-- Qty Batch --}}
              <li class="{{ request()->routeIs('qty-batch.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qty-batch.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Qty Batch</span>
                </a>
              </li>

              {{-- Job Sheet QC --}}
              <li class="{{ request()->routeIs('qc-jobsheet.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qc-jobsheet.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Job Sheet QC</span>
                </a>
              </li>

              {{-- Sampling --}}
              <li class="{{ request()->routeIs('sampling.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('sampling.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Sampling</span>
                </a>
              </li>

              {{-- COA QC/QA --}}
              <li class="{{ request()->routeIs('coa.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('coa.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">COA QC/QA</span>
                </a>
              </li>

              {{-- Review --}}
              <li class="{{ request()->routeIs('review.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('review.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Review &amp; Release</span>
                </a>
              </li>

              {{-- Release (Logsheet) --}}
              <li class="{{ request()->routeIs('release.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('release.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Release</span>
                </a>
              </li>
            </ul>
          </li>
        @endif {{-- end Admin --}}

        {{-- ===================== MENU PRODUKSI (non-admin) ===================== --}}
        @if ($isProduksi && !$isAdmin)
          {{-- PRODUKSI --}}
          <li class="navigation-header">
            <span>Produksi</span><i data-feather="more-horizontal"></i>
          </li>

          {{-- Proses Produksi (submenu) --}}
          <li class="nav-item {{ $isProduksiMenuActive ? 'active open' : '' }}">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="activity"></i>
              <span class="menu-title text-truncate">Proses Produksi</span>
            </a>
            <ul class="menu-content">
              {{-- Jadwal Produksi --}}
              <li class="{{ request()->routeIs('show-permintaan') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-permintaan') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Jadwal Produksi (WO)</span>
                </a>
              </li>

              {{-- Mixing --}}
              <li class="{{ request()->routeIs('mixing.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('mixing.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Mixing</span>
                </a>
              </li>

              {{-- Capsule Filling --}}
              <li class="{{ request()->routeIs('capsule-filling.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('capsule-filling.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Capsule Filling</span>
                </a>
              </li>

              {{-- Tableting --}}
              <li class="{{ request()->routeIs('tableting.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('tableting.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Tableting</span>
                </a>
              </li>

              {{-- Coating --}}
              <li class="{{ request()->routeIs('coating.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('coating.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Coating</span>
                </a>
              </li>

              {{-- Primary & Secondary Pack --}}
              <li class="{{ request()->routeIs('primary-secondary.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('primary-secondary.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Primary &amp; Secondary Pack</span>
                </a>
              </li>
            </ul>
          </li>

          {{-- BARANG SETELAH PACK (Produksi) --}}
          <li class="navigation-header">
            <span>Barang Setelah Pack</span><i data-feather="more-horizontal"></i>
          </li>

          <li class="nav-item {{ $isAfterPackMenuActive ? 'active open' : '' }}">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="archive"></i>
              <span class="menu-title text-truncate">After Secondary Pack</span>
            </a>
            <ul class="menu-content">
              {{-- Qty Batch --}}
              <li class="{{ request()->routeIs('qty-batch.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qty-batch.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Qty Batch</span>
                </a>
              </li>

              {{-- Job Sheet QC --}}
              <li class="{{ request()->routeIs('qc-jobsheet.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qc-jobsheet.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Job Sheet QC</span>
                </a>
              </li>

              {{-- Sampling --}}
              <li class="{{ request()->routeIs('sampling.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('sampling.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Sampling</span>
                </a>
              </li>

              {{-- COA QC/QA --}}
              <li class="{{ request()->routeIs('coa.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('coa.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">COA QC/QA</span>
                </a>
              </li>

              {{-- Review --}}
              <li class="{{ request()->routeIs('review.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('review.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Review &amp; Release</span>
                </a>
              </li>

              {{-- Release --}}
              <li class="{{ request()->routeIs('release.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('release.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Release</span>
                </a>
              </li>
            </ul>
          </li>
        @endif

        {{-- ===================== MENU PPIC (non-admin) ===================== --}}
        @if ($isPPIC && !$isAdmin)
          <li class="navigation-header">
            <span>PPIC</span><i data-feather="more-horizontal"></i>
          </li>

          {{-- Proses Produksi (PPIC fokus ke WO + monitoring) --}}
          <li class="nav-item {{ $isProduksiMenuActive ? 'active open' : '' }}">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="activity"></i>
              <span class="menu-title text-truncate">Proses Produksi</span>
            </a>
            <ul class="menu-content">
              {{-- Jadwal Produksi / WO --}}
              <li class="{{ request()->routeIs('show-permintaan') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('show-permintaan') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Jadwal Produksi (WO)</span>
                </a>
              </li>

              {{-- Kalau PPIC juga diizinkan akses module lain, bisa pakai yang di bawah:
                   pastikan middleware di web.php sudah ditambah "PPIC" juga
              --}}
              <li class="{{ request()->routeIs('mixing.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('mixing.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Mixing</span>
                </a>
              </li>

              <li class="{{ request()->routeIs('capsule-filling.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('capsule-filling.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Capsule Filling</span>
                </a>
              </li>

              <li class="{{ request()->routeIs('tableting.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('tableting.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Tableting</span>
                </a>
              </li>

              <li class="{{ request()->routeIs('coating.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('coating.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Coating</span>
                </a>
              </li>

              <li class="{{ request()->routeIs('primary-secondary.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('primary-secondary.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Primary &amp; Secondary Pack</span>
                </a>
              </li>
            </ul>
          </li>

          <li class="navigation-header">
            <span>Barang Setelah Pack</span><i data-feather="more-horizontal"></i>
          </li>

          <li class="nav-item {{ $isAfterPackMenuActive ? 'active open' : '' }}">
            <a class="d-flex align-items-center" href="#">
              <i data-feather="archive"></i>
              <span class="menu-title text-truncate">After Secondary Pack</span>
            </a>
            <ul class="menu-content">
              <li class="{{ request()->routeIs('qty-batch.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qty-batch.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Qty Batch</span>
                </a>
              </li>
              <li class="{{ request()->routeIs('qc-jobsheet.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('qc-jobsheet.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Job Sheet QC</span>
                </a>
              </li>
              <li class="{{ request()->routeIs('sampling.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('sampling.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Sampling</span>
                </a>
              </li>
              <li class="{{ request()->routeIs('coa.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('coa.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">COA QC/QA</span>
                </a>
              </li>
              <li class="{{ request()->routeIs('review.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('review.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Review &amp; Release</span>
                </a>
              </li>
              <li class="{{ request()->routeIs('release.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('release.index') }}">
                  <i data-feather="circle"></i>
                  <span class="menu-item text-truncate">Release</span>
                </a>
              </li>
            </ul>
          </li>
        @endif

        {{-- ===================== MENU QA (non-admin) ===================== --}}
        @if ($isQA && !$isAdmin)
          <li class="navigation-header">
            <span>Produksi</span><i data-feather="more-horizontal"></i>
          </li>

          {{-- QC Release --}}
          <li class="nav-item {{ request()->routeIs('qc-release.*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{{ route('qc-release.index') }}">
              <i data-feather="check-circle"></i>
              <span class="menu-title text-truncate">QC Release</span>
            </a>
          </li>

          {{-- Dokumen QA / COA / Review / Release --}}
          <li class="navigation-header">
            <span>Dokumen QA</span><i data-feather="more-horizontal"></i>
          </li>

          <li class="nav-item {{ request()->routeIs('coa.*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{{ route('coa.index') }}">
              <i data-feather="file-text"></i>
              <span class="menu-title text-truncate">COA QC/QA</span>
            </a>
          </li>

          <li class="nav-item {{ request()->routeIs('review.*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{{ route('review.index') }}">
              <i data-feather="check-square"></i>
              <span class="menu-title text-truncate">Review</span>
            </a>
          </li>

          <li class="nav-item {{ request()->routeIs('release.*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{{ route('release.index') }}">
              <i data-feather="check-circle"></i>
              <span class="menu-title text-truncate">Release</span>
            </a>
          </li>
        @endif

      </ul>
    </div>
  </div>

  {{-- Content --}}
  <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
      <div class="content-header row"></div>
      <div class="content-body">
        @yield('content')

        <button class="btn btn-primary btn-icon scroll-top" type="button">
          <i data-feather="arrow-up"></i>
        </button>
      </div>
    </div>
  </div>

  {{-- Vendor JS --}}
  <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script -->
  <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>

  {{-- Theme JS --}}
  <script src="{{ asset('app-assets/js/core/app-menu.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/core/app.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/customizer.min.js') }}"></script>

  {{-- Page JS --}}
  <script src="{{ asset('app-assets/js/scripts/cards/card-analytics.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/forms/form-select2.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/pages/app-user-list.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/tables/table-datatables-advanced.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/forms/pickers/form-pickers.min.js') }}"></script>

  <script>
    $(window).on('load', function () {
      if (feather) {
        feather.replace({ width: 14, height: 14 });
      }
    });
  </script>
</body>
</html>
