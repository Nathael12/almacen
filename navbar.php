<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg mi-navbar-almacen sticky-top">
  <div class="container-fluid px-lg-4">
    <a class="navbar-brand brand-almacen" href="index.php">
      <i class="bi bi-warehouse-fill text-primary"></i>
      <span>Sistema Almacén</span>
    </a>
    
    <button class="navbar-toggler navbar-dark border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
        <li class="nav-item">
          <a class="nav-link nav-link-custom <?= ($current_page=='productos.php') ? 'active' : '' ?>" href="productos.php">
            <i class="bi bi-box me-1"></i>Productos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom <?= ($current_page=='lotes.php') ? 'active' : '' ?>" href="lotes.php">
            <i class="bi bi-layers me-1"></i>Lotes
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom <?= ($current_page=='registro.php') ? 'active' : '' ?>" href="registro.php">
            <i class="bi bi-clipboard-check me-1"></i>Registros
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom <?= ($current_page=='reportes.php') ? 'active' : '' ?>" href="reportes.php">
            <i class="bi bi-file-bar-graph me-1"></i>Reportes
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-3 my-2 my-lg-0 pt-2 pt-lg-0 border-top border-lg-0 border-secondary-subtle">
        <?php if (isset($_SESSION['usuario'])): ?>
          <span class="badge-usuario">
            <i class="bi bi-person-circle text-primary me-1 fs-6"></i>
            <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']) ?>
          </span>
        <?php else: ?>
          <span class="badge-usuario">
            <i class="bi bi-person-circle text-primary me-1 fs-6"></i>
            Administrador
          </span>
        <?php endif; ?>

        <a href="logout.php" class="btn-logout-custom">
          <i class="bi bi-box-arrow-right"></i>
          <span>Cerrar Sesión</span>
        </a>
      </div>

    </div>
  </div>
</nav>

<style>
/* Carga de fuente moderna */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

/* Contenedor principal del Navbar */
.mi-navbar-almacen {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  background: rgba(26, 32, 44, 0.92) !important;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

/* Marca / Logotipo */
.brand-almacen {
  font-weight: 700;
  font-size: 1.25rem;
  color: #ffffff !important;
  display: flex;
  align-items: center;
  gap: 8px;
  letter-spacing: -0.02em;
}

/* Enlaces de Navegación */
.nav-link-custom {
  color: #94a3b8 !important;
  font-weight: 500;
  font-size: 0.95rem;
  padding: 8px 16px !important;
  border-radius: 8px;
  transition: all 0.2s ease;
  position: relative;
}

.nav-link-custom:hover {
  color: #ffffff !important;
  background: rgba(255, 255, 255, 0.05);
}

/* Enlace Activo con indicador azul elegante */
.nav-link-custom.active {
  color: #ffffff !important;
  font-weight: 600;
  background: rgba(13, 110, 253, 0.15) !important;
}

.nav-link-custom.active::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 14px;
  right: 14px;
  height: 3px;
  background-color: #0d6efd;
  border-radius: 3px 3px 0 0;
  box-shadow: 0 0 10px rgba(13, 110, 253, 0.8);
}

/* Badge de Usuario */
.badge-usuario {
  color: #cbd5e1;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.88rem;
  font-weight: 500;
  display: flex;
  align-items: center;
}

/* Botón Cerrar Sesión */
.btn-logout-custom {
  color: #f87171;
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.3);
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.88rem;
  font-weight: 600;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-logout-custom:hover {
  background: #dc3545;
  color: #ffffff;
  border-color: #dc3545;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.35);
}
</style>