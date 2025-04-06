<?php
include 'config.php';

if (isset($_GET['action']) && $_GET['action'] === 'get_data') {
    $query = "
        SELECT p.*, u.file_path, u.drive_url
        FROM participants p
        LEFT JOIN (
            SELECT team_code, student_id, file_path, drive_url
            FROM uploads
            WHERE (team_code, student_id, upload_date) IN (
                SELECT team_code, student_id, MAX(upload_date)
                FROM uploads
                GROUP BY team_code, student_id
            )
        ) u 
            ON p.team_code = u.team_code 
            AND p.student_id = u.student_id;
    ";
    $result = $db->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {

        $participant_data = [
            'Team Code' => $row['team_code'],
            'First Name' => $row['first_name'],
            'Last Name' => $row['last_name'],
            'Email' => $row['email'],

            'PDF File Path' => $row['file_path'] ?? 'No File',
            'Google Drive URL' => $row['drive_url'] ?? 'No Link'
        ];

        foreach ($row as $key => $value) {
            if (!in_array($key, ['team_code', 'first_name', 'last_name', 'email', 'file_path', 'drive_url'])) {
                $participant_data[$key] = $value;
            }
        }

        $data[] = $participant_data;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <link rel="stylesheet" href="display_style.css">
</head>
<body>
    <div class="table-container">
        <div class="header-section fade-in">
            <h1 class="text-4xl font-bold mb-6 text-center">Participants Data</h1>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <div class="search-bar w-full sm:w-auto">
                    <input type="text" id="search" placeholder="Search by name, email, or team code..."
                        class="focus:ring-0">
                    <button id="search-btn" type="button">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button id="clear-btn" type="button"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
                <button id="export-btn" class="export-btn">
                    <i class="fas fa-file-export"></i> Export to Excel
                </button>
            </div>
        </div>
        <div class="table-wrapper fade-in">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="participants-table">
                    <thead>
                        <tr class="table-header">
                            <th class="p-4 text-left">Team Code</th>
                            <th class="p-4 text-left">First Name</th>
                            <th class="p-4 text-left">Last Name</th>
                            <th class="p-4 text-left">Email</th>
                            <th class="p-4 text-left">PDF File</th>
                            <th class="p-4 text-left">Google Drive Link</th>
                        </tr>
                    </thead>
                    <tbody class="table-scroll">
                        <?php
                        include 'config.php';

                        $query = "
                            SELECT p.*, u.file_path, u.drive_url
                            FROM participants p
                            LEFT JOIN (
                                SELECT team_code, student_id, file_path, drive_url
                                FROM uploads
                                WHERE (team_code, student_id, upload_date) IN (
                                    SELECT team_code, student_id, MAX(upload_date)
                                    FROM uploads
                                    GROUP BY team_code, student_id
                                )
                            ) u 
                                ON p.team_code = u.team_code 
                                AND p.student_id = u.student_id;";
                        $result = $db->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $team_code = htmlspecialchars($row['team_code']);
                                $first_name = htmlspecialchars($row['first_name']);
                                $last_name = htmlspecialchars($row['last_name']);
                                $email = htmlspecialchars($row['email']);
                                $file_path = $row['file_path'];
                                $drive_url = $row['drive_url'];

                                $file_link = $file_path && file_exists($file_path)
                                    ? "<a href='$file_path' class='download-btn' download><i class='fas fa-download'></i> Download PDF</a>"
                                    : "No File";

                                $drive_link = $drive_url
                                    ? "<a href='$drive_url' class='drive-link' target='_blank'><i class='fas fa-link'></i> View Drive</a>"
                                    : "No Link";

                                echo "
                                    <tr class='table-row'>
                                        <td class='p-4'>$team_code</td>
                                        <td class='p-4'>$first_name</td>
                                        <td class='p-4'>$last_name</td>
                                        <td class='p-4'>$email</td>
                                        <td class='p-4'>$file_link</td>
                                        <td class='p-4'>$drive_link</td>
                                    </tr>
                                ";
                            }
                        } else {
                            echo '<tr><td colspan="6" class="no-data">No participants found.</td></tr>';
                        }

                        $db->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('search-btn');
        const clearBtn = document.getElementById('clear-btn');
        const exportBtn = document.getElementById('export-btn');
        const rows = document.querySelectorAll('.table-row');
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            performSearch();
        });
        exportBtn.addEventListener('click', async function () {
            try {
                exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
                exportBtn.disabled = true;
                const response = await fetch('display.php?action=get_data');
                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }
                const data = await response.json();

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.json_to_sheet(data);

                const pdfColumnIndex = Object.keys(data[0]).indexOf('PDF File Path');
                for (let i = 0; i < data.length; i++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: i + 1, c: pdfColumnIndex });
                    const pdfPath = data[i]['PDF File Path'];
                    if (pdfPath && pdfPath !== 'No File') {
                        ws[cellAddress].l = { Target: pdfPath, Tooltip: 'Click to open PDF' };
                    }
                }

                ws['!cols'] = [
                    { wch: 15 },
                    { wch: 15 },
                    { wch: 15 },
                    { wch: 30 },
                    { wch: 50 },
                    { wch: 50 }
                ];
                XLSX.utils.book_append_sheet(wb, ws, 'Participants');
                const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
                const blob = new Blob([wbout], { type: 'application/octet-stream' });
                saveAs(blob, 'participants_data_' + new Date().toISOString().replace(/[-:T]/g, '').split('.')[0] + '.xlsx');
                exportBtn.innerHTML = '<i class="fas fa-file-export"></i> Export to Excel';
                exportBtn.disabled = false;
                alert('Excel file exported successfully!');
            } catch (error) {
                console.error('Export failed:', error);
                exportBtn.innerHTML = '<i class="fas fa-file-export"></i> Export to Excel';
                exportBtn.disabled = false;
                alert('Failed to export Excel file: ' + error.message);
            }
        });
    </script>
</body>

</html>