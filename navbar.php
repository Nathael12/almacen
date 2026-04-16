<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-4" href="index.php">
      <i class="bi bi-warehouse-fill text-primary me-2"></i>
      Sistema Almacén
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='productos.php') ? 'active' : '' ?>" href="productos.php">
            <i class="bi bi-box me-1"></i>Productos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='lotes.php') ? 'active' : '' ?>" href="lotes.php">
            <i class="bi bi-layers me-1"></i>Lotes
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='registro.php') ? 'active' : '' ?>" href="registro.php">
            <i class="bi bi-clipboard-check me-1"></i>Registros
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='reportes.php') ? 'active' : '' ?>" href="reportes.php">
            <i class="bi bi-file-bar-graph me-1"></i>Reportes
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
.navbar-nav .nav-link.active {
    color: #0d6efd !important;
    font-weight: 500;
    border-bottom: 2px solid #0d6efd;
}
</style>