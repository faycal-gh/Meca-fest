<?php

header("X-Frame-Options: SAMEORIGIN");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Content-Type-Options: nosniff");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';
header('Content-Type: application/json');

function verify_csrf_token() {
    if (
        !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        return false;
    }
    return true;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_file($file) {
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024;

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_type = $finfo->file($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        return false;
    }

    if ($file['size'] > $max_size) {
        return false;
    }

    return true;
}

function log_security_event($message, $severity = 'INFO') {
    $log_file = 'security_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    file_put_contents(
        $log_file,
        "$timestamp [$severity] IP: $ip - $message" . PHP_EOL,
        FILE_APPEND
    );
}

function send_response($success, $message, $redirect = null) {
    if ($success && $redirect) {
        header("Location: $redirect");
        exit;
    } else {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        echo json_encode($response);
        exit;
    }
}

function ensure_team_exists($db, $team_code) {
    $stmt = $db->prepare("INSERT INTO teams (team_code) VALUES (?) ON DUPLICATE KEY UPDATE team_id = team_id");
    if (!$stmt) {
        throw new Exception("Database preparation error");
    }
    $stmt->bind_param("s", $team_code);
    if (!$stmt->execute()) {
        throw new Exception("Database execution error");
    }
    $stmt->close();
}

function check_team_size($db, $team_code, $max_members = 5) {
    $stmt = $db->prepare("SELECT COUNT(*) AS member_count FROM participants WHERE team_code = ?");
    if (!$stmt) {
        throw new Exception("Database preparation error");
    }
    $stmt->bind_param("s", $team_code);
    if (!$stmt->execute()) {
        throw new Exception("Database execution error");
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $member_count = (int) $row['member_count'];
    $stmt->close();

    if ($member_count >= $max_members) {
        throw new Exception("This team has reached the maximum of $max_members members.");
    }

    return $member_count;
}

function check_email_exists($db, $email) {
    $stmt = $db->prepare("SELECT COUNT(*) AS email_count FROM participants WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Database preparation error");
    }
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Database execution error");
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $email_count = (int) $row['email_count'];
    $stmt->close();

    if ($email_count > 0) {
        throw new Exception("This email is already registered.");
    }
}

function check_student_id_exists($db, $student_id) {
    if (empty($student_id)) {
        return;
    }

    $stmt = $db->prepare("SELECT COUNT(*) AS id_count FROM participants WHERE student_id = ?");
    if (!$stmt) {
        throw new Exception("Database preparation error");
    }
    $stmt->bind_param("s", $student_id);
    if (!$stmt->execute()) {
        throw new Exception("Database execution error");
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $id_count = (int) $row['id_count'];
    $stmt->close();

    if ($id_count > 0) {
        throw new Exception("This student ID is already registered.");
    }
}

function insert_participant($db, $team_code, $email, $first_name, $last_name, $discord_id, $phone_number, $date_of_birth, $student_id, $university, $field_of_study) {
    $stmt = $db->prepare("INSERT INTO participants (team_code, email, first_name, last_name, discord_id, phone_number, date_of_birth, student_id, university, field_of_study) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Database preparation error");
    }
    $stmt->bind_param("ssssssssss", $team_code, $email, $first_name, $last_name, $discord_id, $phone_number, $date_of_birth, $student_id, $university, $field_of_study);
    if (!$stmt->execute()) {
        throw new Exception("Database execution error");
    }
    $participant_id = $db->insert_id;
    $stmt->close();

    return $participant_id;
}

function handle_file_upload($file, $first_name, $last_name, $team_code) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!validate_file($file)) {
        throw new Exception("Invalid file type or size. Please upload a PDF or Word document under 5MB.");
    }

    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Failed to create upload directory");
        }
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    $safe_first_name = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $first_name));
    $safe_last_name = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $last_name));
    $safe_team_code = preg_replace('/[^a-zA-Z0-9_]/', '', $team_code);

    $file_name = sprintf(
        "%s_%s_%s_%s.%s",
        $safe_first_name,
        $safe_last_name,
        $safe_team_code,
        bin2hex(random_bytes(8)),
        $file_extension
    );

    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception("Failed to upload file");
    }

    chmod($file_path, 0644);

    return $file_path;
}

function insert_upload($db, $team_code, $student_id, $file_path, $drive_url) {
    if (!$file_path && !$drive_url) {
        return null;
    }

    $stmt = $db->prepare("INSERT INTO uploads (team_code, student_id, file_path, drive_url) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Database preparation error");
    }
    $stmt->bind_param("ssss", $team_code, $student_id, $file_path, $drive_url);
    if (!$stmt->execute()) {
        throw new Exception("Database execution error");
    }
    $stmt->close();

    return true;
}

function validate_required_fields($team_code, $email, $first_name, $last_name) {
    if (empty($team_code) || strlen($team_code) > 20) {
        throw new Exception("Invalid team code. Please use 1-20 characters.");
    }

    if (!validate_email($email)) {
        throw new Exception("Please enter a valid email address.");
    }

    if (empty($first_name) || empty($last_name)) {
        throw new Exception("Please enter your full name.");
    }
}

function validate_drive_url($drive_url) {
    if ($drive_url && !filter_var($drive_url, FILTER_VALIDATE_URL)) {
        throw new Exception("Please enter a valid URL for the drive link.");
    }
}

function validate_upload_requirement($file, $drive_url) {
    $has_file = isset($file) && $file['error'] === UPLOAD_ERR_OK;
    $has_drive_url = !empty($drive_url);

    if (!$has_file && !$drive_url) {
        throw new Exception("Please upload a document or provide a drive link before submitting.");
    }
}

if (isset($_POST['register-btn'])) {
    if (!verify_csrf_token()) {
        log_security_event("CSRF token validation failed", "CRITICAL");
        send_response(false, "Security validation failed. Please try again.");
    }

    $team_code = sanitize_input($_POST['team_code'] ?? '');
    $team_code = strtoupper($team_code);
    $email = sanitize_input($_POST['email'] ?? '');
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $discord_id = isset($_POST['discord_id']) ? sanitize_input($_POST['discord_id']) : null;
    $phone_number = isset($_POST['phone_number']) ? sanitize_input($_POST['phone_number']) : null;
    $date_of_birth = !empty($_POST['date_of_birth']) ? sanitize_input($_POST['date_of_birth']) : null;
    $student_id = isset($_POST['student_id']) ? sanitize_input($_POST['student_id']) : null;
    $university = isset($_POST['university']) ? sanitize_input($_POST['university']) : null;
    $field_of_study = isset($_POST['field_of_study']) ? sanitize_input($_POST['field_of_study']) : null;
    $drive_url = isset($_POST['drive_link']) ? sanitize_input($_POST['drive_link']) : null;
    $file_path = null;

    try {
        validate_upload_requirement($_FILES['document'] ?? null, $drive_url);
        validate_required_fields($team_code, $email, $first_name, $last_name);
        validate_drive_url($drive_url);

        $db->begin_transaction();
        ensure_team_exists($db, $team_code);
        check_team_size($db, $team_code);
        check_email_exists($db, $email);
        check_student_id_exists($db, $student_id);

        $participant_id = insert_participant($db, $team_code, $email, $first_name, $last_name, $discord_id, $phone_number, $date_of_birth, $student_id, $university, $field_of_study);

        if (isset($_FILES['document']) && $_FILES['document']['error'] == UPLOAD_ERR_OK) {
            $file_path = handle_file_upload($_FILES['document'], $first_name, $last_name, $team_code);
        }

        $upload_success = insert_upload($db, $team_code, $student_id, $file_path, $drive_url);
        if ($upload_success === null) {
            throw new Exception("No upload information provided. Please upload a document or provide a drive link.");
        }

        $db->commit();
        log_security_event("Successful registration for team: $team_code, email: $email");
        send_response(true, "Registration successful!", "success.php");
    } catch (Exception $e) {
        if (isset($db) && $db->ping()) {
            $db->rollback();
        }
        log_security_event("Registration failed: " . $e->getMessage(), "ERROR");
        send_response(false, $e->getMessage());
    }
    exit;
}

if (isset($_POST['register_data'])) {
    if (!verify_csrf_token()) {
        log_security_event("AJAX CSRF token validation failed", "CRITICAL");
        send_response(false, "Security validation failed. Please try again.");
    }

    $data = json_decode($_POST['register_data'], true);

    if (!$data || !is_array($data)) {
        send_response(false, "Invalid data format");
    }

    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $data[$key] = sanitize_input($value);
        }
    }

    try {
        validate_required_fields($data['team_code'], $data['email'], $data['first_name'], $data['last_name']);

        $drive_url = isset($data['drive_link']) ? $data['drive_link'] : null;

        if (isset($data['is_final_step']) && $data['is_final_step']) {
            $has_file = isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK;
            if (!$has_file && empty($drive_url)) {
                throw new Exception("Please upload a document or provide a drive link before submitting.");
            }
        }

        validate_drive_url($drive_url);
    } catch (Exception $e) {
        send_response(false, $e->getMessage());
    }

    $_SESSION['registration_data'] = $data;
    $_SESSION['registration_timestamp'] = time();

    send_response(true, "Data saved temporarily");
}

if (isset($_POST['complete_registration'])) {
    if (!verify_csrf_token()) {
        log_security_event("Complete registration CSRF token validation failed", "CRITICAL");
        send_response(false, "Security validation failed. Please try again.");
    }

    if (
        !isset($_SESSION['registration_data']) ||
        !isset($_SESSION['registration_timestamp']) ||
        (time() - $_SESSION['registration_timestamp'] > 1800)
    ) {
        send_response(false, "Registration session expired. Please start again.");
    }

    $data = $_SESSION['registration_data'];

    $team_code = strtoupper(trim($data['team_code']));
    $email = trim($data['email']);
    $first_name = trim($data['first_name']);
    $last_name = trim($data['last_name']);
    $discord_id = isset($data['discord_id']) ? trim($data['discord_id']) : '';
    $phone_number = isset($data['phone_number']) ? trim($data['phone_number']) : '';
    $date_of_birth = !empty($data['date_of_birth']) ? trim($data['date_of_birth']) : null;
    $student_id = isset($data['student_id']) ? trim($data['student_id']) : '';
    $university = isset($data['university']) ? trim($data['university']) : '';
    $field_of_study = isset($data['field_of_study']) ? trim($data['field_of_study']) : '';
    $drive_url = isset($data['drive_link']) ? trim($data['drive_link']) : null;

    if ($date_of_birth && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) {
        send_response(false, "Invalid date format. Please use YYYY-MM-DD.");
    }

    try {
        validate_upload_requirement($_FILES['document'] ?? null, $drive_url);
        $db->begin_transaction();
        validate_required_fields($team_code, $email, $first_name, $last_name);
        validate_drive_url($drive_url);
        ensure_team_exists($db, $team_code);
        check_team_size($db, $team_code);
        check_email_exists($db, $email);
        check_student_id_exists($db, $student_id);

        insert_participant($db, $team_code, $email, $first_name, $last_name, $discord_id, $phone_number, $date_of_birth, $student_id, $university, $field_of_study);

        $file_path = null;
        if (isset($_FILES['document']) && $_FILES['document']['error'] == UPLOAD_ERR_OK) {
            $file_path = handle_file_upload($_FILES['document'], $first_name, $last_name, $team_code);
        }
        
        $upload_success = insert_upload($db, $team_code, $student_id, $file_path, $drive_url);
        if ($upload_success === null) {
            throw new Exception("No upload information provided. Please upload a document or provide a drive link.");
        }
        
        $db->commit();
        unset($_SESSION['registration_data']);
        unset($_SESSION['registration_timestamp']);

        log_security_event("Successful complete registration for team: $team_code, email: $email");
        send_response(true, "Registration completed successfully!");
    } catch (Exception $e) {
        if (isset($db) && $db->ping()) {
            $db->rollback();
        }
        log_security_event("Complete registration failed: " . $e->getMessage(), "ERROR");
        send_response(false, $e->getMessage());
    }
    exit;
}

if (isset($db) && $db instanceof mysqli) {
    $db->close();
}