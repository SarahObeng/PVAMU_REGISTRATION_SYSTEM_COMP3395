<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PVAMU Registration Analytics Dashboard</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ============================================================
     SIDEBAR
     ============================================================ -->
<nav class="sidebar" id="sidebar">

  <div class="sidebar-brand">
    <div class="brand-logo-wrap">
      <!-- PVAMU "P" monogram — replace with real logo if available -->
      <span class="brand-icon">P</span>
    </div>
    <div class="brand-text">
      <div class="brand-name">PVAMU</div>
      <div class="brand-sub">Analytics Portal</div>
    </div>
  </div>

  <div class="sidebar-nav">

    <div class="nav-section-label">Main Menu</div>

    <a href="#" class="nav-link active" data-section="dashboard">
      <i class="fas fa-chart-pie"></i>
      <span>Dashboard</span>
    </a>
    <a href="#" class="nav-link" data-section="analytics">
      <i class="fas fa-chart-bar"></i>
      <span>Analytics</span>
    </a>
    <a href="#" class="nav-link" data-section="recommendations">
      <i class="fas fa-graduation-cap"></i>
      <span>Recommendations</span>
      <span class="nav-badge">NEW</span>
    </a>
    <a href="#" class="nav-link" data-section="reports">
      <i class="fas fa-file-chart-column"></i>
      <span>Reports</span>
    </a>

    <div class="nav-section-label">System</div>

    <a href="#" class="nav-link" data-section="settings">
      <i class="fas fa-sliders"></i>
      <span>Settings</span>
    </a>
    <a href="index.php" class="nav-link">
      <i class="fas fa-right-from-bracket"></i>
      <span>Logout</span>
    </a>

  </div>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar">AD</div>
      <div>
        <div class="user-name">Admin</div>
        <div class="user-role">Registrar Office</div>
      </div>
    </div>
  </div>

</nav>

<!-- ============================================================
     TOP BAR
     ============================================================ -->
<header class="topbar">

  <button class="topbar-btn" id="btn-menu" title="Toggle sidebar">
    <i class="fas fa-bars"></i>
  </button>

  <h1 class="topbar-title">Analytics <span>Dashboard</span></h1>

  <div class="topbar-actions">
    <div class="live-indicator">
      <span class="live-dot"></span>
      LIVE
    </div>

    <div class="topbar-divider"></div>

    <button class="topbar-btn" title="Notifications">
      <i class="fas fa-bell"></i>
      <span class="notification-dot"></span>
    </button>

    <button class="topbar-btn" title="Refresh now" id="btn-refresh">
      <i class="fas fa-rotate-right"></i>
    </button>

    <button class="topbar-btn" title="Admin profile">
      <i class="fas fa-user-circle"></i>
    </button>
  </div>

</header>

<!-- ============================================================
     MAIN CONTENT
     ============================================================ -->
<main class="main-content">
<div class="content-wrapper">

  <!-- ==================== DASHBOARD SECTION ==================== -->
  <section class="page-section active" id="section-dashboard">

    <div class="section-header">
      <h2 class="section-title">Registration Overview</h2>
      <p class="section-subtitle">Real-time course enrollment and waitlist metrics &mdash; updated every 10 seconds.</p>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-icon"><i class="fas fa-users-clock"></i></div>
        <div class="kpi-body">
          <div class="kpi-value" id="kpi-waitlisted">—</div>
          <div class="kpi-label">Total Waitlisted</div>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon"><i class="fas fa-gauge-high"></i></div>
        <div class="kpi-body">
          <div class="kpi-value" id="kpi-utilisation">—</div>
          <div class="kpi-label">Avg. Seat Utilisation</div>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="kpi-body">
          <div class="kpi-value" id="kpi-under">—</div>
          <div class="kpi-label">Under-Enrolled Sections</div>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
        <div class="kpi-body">
          <div class="kpi-value" id="kpi-sections">—</div>
          <div class="kpi-label">Active Sections</div>
        </div>
      </div>
    </div>

    <!-- FILTER BAR (shared across charts) -->
    <div class="filter-bar">
      <span class="filter-label"><i class="fas fa-filter" style="margin-right:5px"></i>Filter:</span>
      <select class="filter-select filter-semester" aria-label="Filter by semester">
        <option value="">All Semesters</option>
      </select>
      <select class="filter-select filter-major" aria-label="Filter by major">
        <option value="">All Majors</option>
      </select>
      <button class="filter-btn filter-apply">Apply Filters</button>
    </div>

    <!-- CHARTS ROW 1 -->
    <div class="charts-grid">

      <!-- Chart 1: Waitlist by Course -->
      <div class="chart-card" id="chart-waitlist-wrap">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Courses with Largest Waitlists</div>
            <div class="chart-card-subtitle">Top 10 courses ranked by waitlisted students</div>
          </div>
          <span class="chart-tag"><i class="fas fa-bar-chart"></i> Q1</span>
        </div>
        <div class="chart-wrap" style="height:280px">
          <div class="chart-loading"><div class="spinner"></div></div>
          <canvas id="chart-waitlist"></canvas>
        </div>
      </div>

      <!-- Chart 2: Under-Enrolled -->
      <div class="chart-card" id="chart-under-wrap">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Under-Enrolled Courses (&lt;50% capacity)</div>
            <div class="chart-card-subtitle">Sections with low seat fill rate</div>
          </div>
          <span class="chart-tag"><i class="fas fa-bar-chart"></i> Q2</span>
        </div>
        <div class="chart-wrap" style="height:280px">
          <div class="chart-loading"><div class="spinner"></div></div>
          <canvas id="chart-under"></canvas>
        </div>
      </div>

      <!-- Chart 3: Seat Utilisation (full-width) -->
      <div class="chart-card full-width" id="chart-seats-wrap">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Seat Utilisation per Course</div>
            <div class="chart-card-subtitle">Percentage of seats filled — colour-coded by fill level</div>
          </div>
          <span class="chart-tag"><i class="fas fa-bar-chart"></i> Q3</span>
        </div>
        <div class="chart-wrap" style="height:300px">
          <div class="chart-loading"><div class="spinner"></div></div>
          <canvas id="chart-seats"></canvas>
        </div>
      </div>

      <!-- Chart 4: Waitlist by Major -->
      <div class="chart-card full-width" id="chart-major-wrap">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Waitlist Impact by Major</div>
            <div class="chart-card-subtitle">Which departments have the most students waiting?</div>
          </div>
          <span class="chart-tag"><i class="fas fa-bar-chart"></i> Q4</span>
        </div>
        <div class="chart-wrap" style="height:260px">
          <div class="chart-loading"><div class="spinner"></div></div>
          <canvas id="chart-major"></canvas>
        </div>
      </div>

    </div><!-- /.charts-grid -->

  </section><!-- /#section-dashboard -->


  <!-- ==================== ANALYTICS SECTION ==================== -->
  <section class="page-section" id="section-analytics">
    <div class="section-header">
      <h2 class="section-title">Analytics Deep Dive</h2>
      <p class="section-subtitle">All four business-intelligence charts in one view with independent filters.</p>
    </div>

    <div class="filter-bar">
      <span class="filter-label"><i class="fas fa-filter" style="margin-right:5px"></i>Filter:</span>
      <select class="filter-select filter-semester" aria-label="Filter by semester">
        <option value="">All Semesters</option>
      </select>
      <select class="filter-select filter-major" aria-label="Filter by major">
        <option value="">All Majors</option>
      </select>
      <button class="filter-btn filter-apply">Apply</button>
    </div>

    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Waitlist Volume</div>
            <div class="chart-card-subtitle">By course (top 10)</div>
          </div>
        </div>
        <div class="chart-wrap" style="height:260px">
          <canvas id="chart-waitlist-2"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Under-Enrolled Sections</div>
            <div class="chart-card-subtitle">Fill rate below 50%</div>
          </div>
        </div>
        <div class="chart-wrap" style="height:260px">
          <canvas id="chart-under-2"></canvas>
        </div>
      </div>
    </div>

    <!-- Note: The analytics tab shares the same chart instances via the dashboard. 
         In a full deployment these would be independent Chart.js instances. -->
    <div class="chart-card" style="padding:32px;text-align:center;color:var(--text-muted)">
      <i class="fas fa-info-circle" style="font-size:1.8rem;display:block;margin-bottom:10px;opacity:0.4"></i>
      The Analytics tab references the same live data as the Dashboard tab.<br>
      Use the sidebar to navigate back to the <strong>Dashboard</strong> for the full interactive charts.
    </div>
  </section>


  <!-- ==================== RECOMMENDATIONS SECTION ==================== -->
  <section class="page-section" id="section-recommendations">

    <div class="section-header">
      <h2 class="section-title">Course Recommendations</h2>
      <p class="section-subtitle">Suggested next courses per student based on their degree plan and completed credits.</p>
    </div>

    <div class="filter-bar" style="margin-bottom:16px">
      <span class="filter-label"><i class="fas fa-filter" style="margin-right:5px"></i>Filter:</span>
      <select class="filter-select filter-major" aria-label="Filter recommendations by major">
        <option value="">All Majors</option>
      </select>
      <button class="filter-btn filter-apply">Apply</button>
    </div>

    <div class="rec-card">
      <div class="flex-between" style="margin-bottom:4px">
        <div>
          <div class="chart-card-title">Recommended Courses by Student</div>
          <div class="chart-card-subtitle">Courses not yet completed that appear on the student's degree plan</div>
        </div>
        <div class="rec-search-wrap">
          <i class="fas fa-search"></i>
          <input type="text" id="rec-search" class="rec-search" placeholder="Search student, course…">
        </div>
      </div>

      <div class="rec-table-wrap">
        <table class="rec-table" id="rec-table">
          <thead>
            <tr>
              <th>Student <i class="fas fa-sort"></i></th>
              <th>Class</th>
              <th>Major</th>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Credits</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="rec-tbody">
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <i class="fas fa-spinner fa-spin"></i>
                  Loading recommendations…
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </section><!-- /#section-recommendations -->


  <!-- ==================== REPORTS SECTION ==================== -->
  <section class="page-section" id="section-reports">

    <div class="section-header">
      <h2 class="section-title">Reports &amp; Exports</h2>
      <p class="section-subtitle">Generate and download registration reports for administrative use.</p>
    </div>

    <div class="reports-grid">

      <div class="report-card" onclick="window.open('api/waitlist_by_course.php','_blank')">
        <div class="report-icon"><i class="fas fa-list-ol"></i></div>
        <div class="report-name">Waitlist Report</div>
        <div class="report-desc">Export waitlisted students ranked by course and priority score.</div>
      </div>

      <div class="report-card" onclick="window.open('api/seat_utilisation.php','_blank')">
        <div class="report-icon"><i class="fas fa-chair"></i></div>
        <div class="report-name">Seat Utilisation</div>
        <div class="report-desc">Full breakdown of enrollment capacity vs. actual enrollment per section.</div>
      </div>

      <div class="report-card" onclick="window.open('api/under_enrolled.php','_blank')">
        <div class="report-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="report-name">Under-Enrolled Courses</div>
        <div class="report-desc">Identify sections below 50% capacity for potential consolidation.</div>
      </div>

      <div class="report-card" onclick="window.open('api/waitlist_by_major.php','_blank')">
        <div class="report-icon"><i class="fas fa-building-columns"></i></div>
        <div class="report-name">Waitlist by Major</div>
        <div class="report-desc">See which academic departments are most impacted by registration waitlists.</div>
      </div>

      <div class="report-card" onclick="window.open('api/recommendations.php','_blank')">
        <div class="report-icon"><i class="fas fa-route"></i></div>
        <div class="report-name">Degree Plan Gaps</div>
        <div class="report-desc">Export course recommendations for every active student.</div>
      </div>

      <div class="report-card" onclick="window.open('api/kpi.php','_blank')">
        <div class="report-icon"><i class="fas fa-square-poll-vertical"></i></div>
        <div class="report-name">KPI Snapshot</div>
        <div class="report-desc">JSON snapshot of all key performance indicators for the current moment.</div>
      </div>

    </div>

  </section><!-- /#section-reports -->


  <!-- ==================== SETTINGS SECTION ==================== -->
  <section class="page-section" id="section-settings">

    <div class="section-header">
      <h2 class="section-title">Dashboard Settings</h2>
      <p class="section-subtitle">Adjust display preferences and system behaviour.</p>
    </div>

    <!-- Refresh Settings -->
    <div class="settings-card">
      <div class="settings-title"><i class="fas fa-rotate-right" style="margin-right:8px;color:var(--purple-main)"></i>Data Refresh</div>

      <div class="setting-row">
        <div>
          <div class="setting-label">Auto-Refresh (every 10 seconds)</div>
          <div class="setting-sublabel">Automatically polls all API endpoints for live data.</div>
        </div>
        <label class="toggle">
          <input type="checkbox" id="toggle-autorefresh" checked>
          <span class="toggle-track"></span>
        </label>
      </div>

      <div class="setting-row">
        <div>
          <div class="setting-label">Show Live Indicator</div>
          <div class="setting-sublabel">Animated green dot in the top navigation bar.</div>
        </div>
        <label class="toggle">
          <input type="checkbox" id="toggle-live" checked>
          <span class="toggle-track"></span>
        </label>
      </div>
    </div>

    <!-- Display Settings -->
    <div class="settings-card">
      <div class="settings-title"><i class="fas fa-palette" style="margin-right:8px;color:var(--purple-main)"></i>Display</div>

      <div class="setting-row">
        <div>
          <div class="setting-label">Chart Animations</div>
          <div class="setting-sublabel">Smooth bar entrance animations on load and refresh.</div>
        </div>
        <label class="toggle">
          <input type="checkbox" id="toggle-anim" checked>
          <span class="toggle-track"></span>
        </label>
      </div>

      <div class="setting-row">
        <div>
          <div class="setting-label">Toast Notifications</div>
          <div class="setting-sublabel">Show brief confirmation toasts after each data refresh.</div>
        </div>
        <label class="toggle">
          <input type="checkbox" id="toggle-toast" checked>
          <span class="toggle-track"></span>
        </label>
      </div>
    </div>

    <!-- Database Info -->
    <div class="settings-card">
      <div class="settings-title"><i class="fas fa-database" style="margin-right:8px;color:var(--purple-main)"></i>Database Connection</div>
      <div class="setting-row">
        <div>
          <div class="setting-label">Host</div>
          <div class="setting-sublabel">Configured in config/db.php</div>
        </div>
        <code style="font-size:0.8rem;color:var(--purple-main);background:var(--bg);padding:4px 10px;border-radius:6px">localhost</code>
      </div>
      <div class="setting-row">
        <div>
          <div class="setting-label">Database</div>
          <div class="setting-sublabel">Target schema</div>
        </div>
        <code style="font-size:0.8rem;color:var(--purple-main);background:var(--bg);padding:4px 10px;border-radius:6px">pvamu_registration</code>
      </div>
    </div>

  </section><!-- /#section-settings -->

</div><!-- /.content-wrapper -->

<!-- Footer -->
<footer class="dashboard-footer">
  &copy; <strong>Prairie View A&amp;M University</strong> &mdash; Office of the Registrar &mdash; <?php echo date('Y'); ?>
</footer>

</main><!-- /.main-content -->

<!-- ============================================================
     TOAST CONTAINER
     ============================================================ -->
<div class="toast-container" id="toast-container"></div>

<!-- Dashboard JS -->
<script src="assets/js/dashboard.js"></script>

<!-- Refresh button wiring -->
<script>
  document.getElementById('btn-refresh')?.addEventListener('click', async () => {
    const btn = document.getElementById('btn-refresh');
    btn.querySelector('i').classList.add('fa-spin');
    await Promise.all([
      typeof loadKPIs              === 'function' && loadKPIs(),
      typeof loadWaitlistByCourse  === 'function' && loadWaitlistByCourse(),
      typeof loadUnderEnrolled     === 'function' && loadUnderEnrolled(),
      typeof loadSeatUtilisation   === 'function' && loadSeatUtilisation(),
      typeof loadWaitlistByMajor   === 'function' && loadWaitlistByMajor(),
    ]);
    btn.querySelector('i').classList.remove('fa-spin');
    typeof showToast === 'function' && showToast('Data refreshed manually', 'success');
  });

  // Live indicator toggle
  document.getElementById('toggle-live')?.addEventListener('change', function() {
    document.querySelector('.live-indicator').style.display = this.checked ? '' : 'none';
  });
</script>

</body>
</html>
