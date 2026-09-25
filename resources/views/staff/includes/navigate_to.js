// =========================================================================
//  โมดูลระบบนำทาง และ ค้นหาสมาชิก (Recycle Module)
// =========================================================================
function navigateTo(moduleName) {
    if (moduleName === 'recycle') {
        document.getElementById('mainScreen').classList.add('is-hidden');
        document.getElementById('recycleScreen').classList.remove('is-hidden');
        document.getElementById('searchMemberInput').value = "";
        loadMembersFromServer();
        renderMemberList(allMembers);
    }
    else if (moduleName === 'settings') {
        document.getElementById('mainScreen').classList.add('is-hidden');
        document.getElementById('depositScreen').classList.add('is-hidden');
        document.getElementById('settingsScreen').classList.remove('is-hidden');

        checkBluetoothStatus();
    }
    else if (moduleName === 'inventory') {
        document.getElementById('mainScreen').classList.add('is-hidden');
        document.getElementById('depositScreen').classList.add('is-hidden');
        document.getElementById('settingsScreen').classList.add('is-hidden');
        document.getElementById('inventoryScreen').classList.remove('is-hidden');

    }
    else if (moduleName === 'water') {
        document.getElementById('mainScreen').classList.add('is-hidden');
        document.getElementById('waterScreen').classList.remove('is-hidden');

    }
    // --- เพิ่มเงื่อนไขสำหรับจดมิเตอร์ประปาตรงนี้ ---
    else if (moduleName === 'water-tabwater-record') {
        document.getElementById('waterScreen').classList.add('is-hidden');
        document.getElementById('waterRecordScreen').classList.remove('is-hidden');

        // หากมีฟังก์ชันโหลดข้อมูลมิเตอร์เดิม ให้เรียกตรงนี้ เช่น loadWaterMeters();
        loadWaterRecordDashboard();
    }
    else if (moduleName === 'water-members-list') {
        console.log('water-members-list')
        document.getElementById('waterRecordScreen').classList.add('is-hidden');
        document.getElementById('waterMembersListScreen').classList.remove('is-hidden');

        // เรียกดึงข้อมูลรายชื่อสมาชิกใน Subzone นั้นทันที
        if (typeof loadWaterMembersList === 'function') {
            loadWaterMembersList();
        }
    }
    // 🟢 2. เพิ่มหน้าแก้ไขรายชื่อสมาชิก/เลขมิเตอร์
    else if (moduleName === 'water-members-edit-list') {
        document.getElementById('waterRecordScreen').classList.add('is-hidden');
        document.getElementById('waterMembersEditListScreen').classList.remove('is-hidden');

        if (typeof loadWaterMembersEditList === 'function') {
            loadWaterMembersEditList();
        }
    }
    else if (moduleName === 'water-complain') {
        // 1. เปิด Modal
        const staffModal = new bootstrap.Modal(document.getElementById('staffDashboardModal'));
        staffModal.show();

        // 2. ดึงเนื้อหาจาก Route staff/dashboard ผ่าน Fetch API
        fetch('staff/dashboard')
            .then(response => response.text())
            .then(html => {
                // นำ HTML ที่ได้มาใส่ใน modal-body โดยไม่ Refresh หน้า
                document.getElementById('modal-staff-content').innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading staff dashboard:', error);
                document.getElementById('modal-staff-content').innerHTML =
                    '<div class="alert alert-danger">ไม่สามารถโหลดข้อมูลได้</div>';
            });
    }
    else if (moduleName === 'main') {
        document.getElementById('mainScreen').classList.remove('is-hidden');
        document.getElementById('waterScreen').classList.add('is-hidden');
        document.getElementById('settingsScreen').classList.add('is-hidden');
        document.getElementById('recycleScreen').classList.add('is-hidden');

        checkBluetoothStatus();
    }


    updateGlobalPrinterStatus();
}