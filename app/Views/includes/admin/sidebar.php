    <!-- Sidebar -->
    <!-- <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar"> -->
    <ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/profile">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-code"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kepegawaian</sup></div>
      </a>

      <!-- Nav Item - Menus -->
      <!-- <?= strpos($title, 'Menus') !== false ? '<li class="nav-item active">' : '<li class="nav-item">' ?>
      <a class="nav-link py-2" href="<?= base_url('menus') ?>">
        <i class="fas fa-fw fa-user"></i>
        <span>Menus</span></a>
      </li> -->

      <?php

      function get_menuDash($items, $title)
      {
        $db         = \Config\Database::connect();
        $user = $db->table('users')->getWhere(['id' => session('user_id')])->getRow();
        $builder    = $db->table('menus');
        $uri = service('uri');
        $html = '';


        foreach ($items as $key => $value) {

          // ex : main navigation
          if ($value['parent'] == 0) {
            $heading = '<hr class="sidebar-divider mt-3">';
            $heading .= '<div class="sidebar-heading pb-2">' . $value['label'] . '</div>';
            $html .= $heading;
          }

          if ($value['parent'] == 0 && isset($value['child'])) {
            foreach ($value['child'] as $child) {
              $labelChild   = $child['label'];
              $labelTrim     = str_replace(' ', '', $child['label']);
              $slugChild    = $child['link'];
              $parentChild  = $child['parent'];
              $iconChild    = $child['icon'];

              // ex: nav1
              if ($value['id'] == $parentChild && isset($child['child']) == false) {

                $builder->select('id');
                if ($uri->getSegment(1) == 'admin') {
                  $queryUri = $builder->getWhere(array('link' => $uri->getSegment(1) . '/' . $uri->getSegment(2)));
                } else {
                  $queryUri = $builder->getWhere(array('link' => $uri->getSegment(1)));
                }

                if ($builder->where(array('link' => $uri->getSegment(1)))->countAllResults() > 0) {
                  $queryUri = $queryUri->getRow();
                  if ($queryUri->id == $child['id']) {
                    $nav = '<li class="nav-item active">';
                  } else {
                    $nav = '<li class="nav-item">';
                  }
                } else {
                  // ex: uri = [aimer, aimer/create, aimer/edit] , $child['link' = aimer]
                  if (strpos(uri_string(), $slugChild) !== false) {
                    $nav = '<li class="nav-item active">';
                    $class = 'class="collapse show"';
                  } else {
                    $nav = '<li class="nav-item">';
                    $class = 'class="collapse"';
                  }
                }

                if ($slugChild == 'penilaian') {
                  $userPenilai = $db->table('users')->where('id', session('user_id'))->get()->getRow();
                  $jabatanStrukturalUserPenilai = $db->table('pegawai_jabatan_struktural_u_view')->where('id_pegawai', $userPenilai->id_pegawai)->havingIn('nama_jabatan_struktural', ['Kepala', 'Dekan',  'Ketua', 'Wakil Rektor', 'Penanggung Jawab'])->get()->getRow();
                  if ($jabatanStrukturalUserPenilai) {
                    $menu = $nav . '
                    <a class="nav-link py-2" href="' . base_url($slugChild) . '">
                      <i class="' . $iconChild . '"></i>
                      <span>' . $labelChild . '</span>
                    </a>
                    </li>';
                    $html .= $menu;
                  }
                } else {
                  $menu = $nav . '
                  <a class="nav-link py-2" href="' . base_url($slugChild) . '">
                    <i class="' . $iconChild . '"></i>
                    <span>' . $labelChild . '</span>
                  </a>
                  </li>';
                  $html .= $menu;
                }
              }

              // ex: nav2 > nav2a, nav2b
              if (isset($child['child'])) {
                $active = '';

                $builder->select('parent');
                if ($uri->getSegment(1) == 'admin') {
                  $queryUri = $builder->getWhere(array('link' => $uri->getSegment(1) . '/' . $uri->getSegment(2)));
                } else {
                  $queryUri = $builder->getWhere(array('link' => $uri->getSegment(1)));
                }

                // ex:nav2
                $queryUri = $queryUri->getRow();
                if ($queryUri) {
                  if ($child['id'] == $queryUri->parent) {
                    $nav = '<li class="nav-item active">';
                    $class = 'class="collapse show"';
                  } else {
                    $nav = '<li class="nav-item">';
                    $class = 'class="collapse"';
                  }
                } else {
                  // ex: uri = [aimer, aimer/create, aimer/edit] , $child['link' = aimer]
                  $nav = '<li class="nav-item">';
                  $class = 'class="collapse"';
                }

                $submenu = $nav . '
                <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#' . $labelTrim . '"
                  aria-expanded="true" aria-controls="' . $labelChild . '">
                  <i class="' . $iconChild . '"></i>
                  <span>' . $labelChild . '</span>
                </a>

                <div id="' . $labelTrim . '" ' . $class . ' aria-labelledby="heading' . $labelChild . '" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">';

                // ex: nav2a
                foreach ($child['child'] as $subChild) {
                  $builder->select('id, parent');
                  if ($uri->getSegment(1) == 'admin') {
                    $queryUri = $builder->getWhere(array('link' => $uri->getSegment(1) . '/' . $uri->getSegment(2)));
                  } else {
                    $queryUri = $builder->getWhere(array('link' => $uri->getSegment(1)));
                  }

                  if ($queryUri) {
                    $queryUri = $queryUri->getRow();
                    if (isset($queryUri->id) && $queryUri->id == $subChild['id']) {
                      $active = 'active';
                    } else {
                      $active = '';
                    }
                  } else {
                    // ex: uri = [aimer, aimer/create, aimer/edit] , $child['link' = aimer]
                    $active = '';
                  }
                  // var_dump($subChild['link'] == 'presensi-lupa-validasi');

                  // if ($subChild['link'] == 'presensi-lupa-validasi') {
                  //   $resultSubordinate = \getSubordinateID($user->id_pegawai);
                  //   $idPegawai = \array_unique(array_merge($resultSubordinate['idPegawaiStruktual'], $resultSubordinate['idPegawaiBiasa']));
                  //   if (!empty($idPegawai)) {
                  //     $label = '';
                  //     $label .= '<a class="collapse-item ' . $active . '" href="' . base_url($subChild['link']) . '">' . $subChild['label'] . '</a>';
                  //     $submenu .= $label;
                  //   }
                  // } else {
                  //   $label = '';
                  //   $label .= '<a class="collapse-item ' . $active . '" href="' . base_url($subChild['link']) . '">' . $subChild['label'] . '</a>';
                  //   $submenu .= $label;
                  // }

                  $label = '';
                  $label .= '<a class="collapse-item ' . $active . '" href="' . base_url($subChild['link']) . '">' . $subChild['label'] . '</a>';
                  $submenu .= $label;
                }

                $submenu .= '</div> </div> </li>';
                $html .= $submenu;
              }
            }
          }

          if (array_key_exists('child', $value)) {
            $html .= get_menuDash($value['child'], $title);
          }
        }

        return $html;
      }

      $items = dataMenu();
      // var_dump($items);
      print get_menuDash($items, $title);

      ?>

      <li class="nav-item">
        <a class="nav-link py-2" href="<?= base_url('presensi-lupa-pengajuan') ?>">
          <i class="fas fa-fw fa-circle"></i>
          <span>Pengajuan Lupa Presensi</span></a>
      </li>
      <?php if (checkGroupUser([1]) || isset($jabatanUser) && $jabatanUser && $jabatanUser->nama_jabatan == 'Tendik') : ?>
        <li class="nav-item">
          <a class="nav-link py-2" href="<?= base_url('presensi-izin-pengajuan') ?>">
            <i class="fas fa-fw fa-circle"></i>
            <span>Pengajuan Izin Presensi</span></a>
        </li>
      <?php endif ?>

      <!-- Divider -->
      <hr class="sidebar-divider mt-3">

      <!-- Nav Item - Logout -->
      <li class="nav-item">
        <a class="nav-link py-2" href="<?= base_url('logout') ?>" data-toggle="modal" data-target="#logoutModal">
          <i class="fas fa-fw fa-sign-out-alt"></i>
          <span>Logout</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->