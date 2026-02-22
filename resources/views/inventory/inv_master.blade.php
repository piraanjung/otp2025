<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory System')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* --- Material Design Custom Style --- */
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f5f7fa; /* สีพื้นหลังเทาอ่อนสบายตา */
        }

        /* Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 100;
        }

        .nav-link {
            color: #6c757d;
            border-radius: 8px;
            margin-bottom: 5px;
            padding: 10px 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #e3f2fd; /* สีฟ้าจางๆ เวลามีเมาส์ชี้ */
            color: #1976d2; /* สีน้ำเงิน Material */
            font-weight: 500;
        }

        .nav-link .material-icons-round {
            margin-right: 10px;
            font-size: 22px;
        }

        /* Card Material Style */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            background: #fff;
            transition: transform 0.2s;
        }
        
        /* ปุ่มแบบ Material */
        .btn-material {
            border-radius: 50px; /* ปุ่มมน */
            padding: 8px 20px;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Navbar ด้านบน */
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 30px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3 d-none d-md-block">
            <h4 class="text-primary fw-bold text-center mb-4 mt-2">
                <span class="material-icons-round align-middle">inventory_2</span> INV-SYS
            </h4>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('inventory.dashboard') }}" class="nav-link text-white active">
                        <i class="material-icons-round align-middle me-2">dashboard</i> ภาพรวมระบบ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inventory.items.index') }}">
                        <span class="material-icons-round">list_alt</span> รายการพัสดุ
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('inventory.history') }}" class="nav-link">
                        <i class="material-icons-round align-middle me-2">history</i> ประวัติการเบิกจ่าย
                    </a>
                </li>
            
                
                <li class="nav-item mt-3">
                    <small class="text-uppercase text-muted ps-3 fw-bold" style="font-size: 0.75rem;">การตั้งค่า (Settings)</small>
                </li>

                <li class="nav-item">
                    <a href="{{ route('inventory.categories.index') }}" class="nav-link text-dark">
                        <i class="material-icons-round align-middle me-2 text-secondary">category</i> หมวดหมู่พัสดุ
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('inventory.units.index') }}" class="nav-link text-dark">
                        <i class="material-icons-round align-middle me-2 text-secondary">straighten</i> หน่วยนับ
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('inventory.hazards.index') }}" class="nav-link text-dark">
                        <i class="material-icons-round align-middle me-2 text-secondary">straighten</i> hazards
                    </a>
                </li>


            </ul>
        </div>

        <div class="col-md-10 p-0">
            <div class="top-navbar d-flex justify-content-between align-items-center">
                <h5 class="m-0 text-dark">@yield('header_title', 'ระบบจัดการพัสดุ')</h5>
                <div class="user-profile">
                    <span class="me-2">Admin User</span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff&rounded=true" width="40" alt="user">
                </div>
            </div>

            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')

</body>
</html>