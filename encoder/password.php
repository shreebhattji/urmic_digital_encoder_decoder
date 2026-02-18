<?php
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhattji/Urmi/blob/main/licence.md
*/

declare(strict_types=1);
include 'header.php';
?>
<?php
$usersFile    = '/var/www/users.json';
function load_json(string $file): array
{
    return is_file($file) ? json_decode(file_get_contents($file), true) ?: [] : [];
}
function save_json(string $file, array $data): void
{
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

$currentUser = $_SESSION['user'];
$users = load_json($usersFile);

if (!isset($users[$currentUser])) {
    // Safety fallback
    session_destroy();
    header('Location: /login.php');
    exit;
}

/* ---------- POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die('Invalid request');
    }

    $newUsername = strtolower(trim($_POST['new_username'] ?? ''));
    $currentPass = $_POST['current_password'] ?? '';
    $newPass     = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    // Verify current password
    if (!password_verify($currentPass, $users[$currentUser]['password'])) {
        $error = 'Current password is incorrect.';
    }

    // Validate new password if provided
    if (!$error && $newPass !== '') {
        if (strlen($newPass) < 8) {
            $error = 'New password must be at least 8 characters.';
        } elseif ($newPass !== $confirmPass) {
            $error = 'New passwords do not match.';
        }
    }

    // Validate new username if provided
    if (!$error && $newUsername !== '' && $newUsername !== $currentUser) {
        if (!preg_match('/^[a-z0-9_]{3,32}$/', $newUsername)) {
            $error = 'Username must be 3–32 chars (a–z, 0–9, underscore).';
        } elseif (isset($users[$newUsername])) {
            $error = 'Username already exists.';
        }
    }

    if (!$error) {
        // Apply changes
        $updatedUser = $currentUser;

        if ($newPass !== '') {
            $users[$currentUser]['password'] =
                password_hash($newPass, PASSWORD_DEFAULT);
        }

        if ($newUsername !== '' && $newUsername !== $currentUser) {
            $users[$newUsername] = $users[$currentUser];
            unset($users[$currentUser]);
            $updatedUser = $newUsername;
        }

        save_json($usersFile, $users);

        // Update session safely
        session_regenerate_id(true);
        $_SESSION['user'] = $updatedUser;

        $success = 'Credentials updated successfully.';
    }
}

?>

<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            <h3>Change Username / Password</h3>

            <?php if ($error): ?>
                <p style="color:#dc2626"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p style="color:#16a34a"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="post" class="password-form" autocomplete="off">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

                <div class="field">
                    <label>New Username (optional)</label>
                    <input type="text" name="new_username" placeholder="leave blank to keep current">
                </div>

                <div class="field">
                    <label>Current Password</label>
                    <div class="pass-wrap">
                        <input type="password" name="current_password" required>
                        <span class="pass-toggle" onclick="togglePass(this)">Show</span>
                    </div>
                </div>

                <div class="field">
                    <label>New Password</label>
                    <div class="pass-wrap">
                        <input type="password" id="newpass" name="new_password" oninput="checkStrength(this.value)">
                        <span class="pass-toggle" onclick="togglePass(this)">Show</span>
                    </div>

                    <div class="strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Password strength</div>
                </div>

                <div class="field">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password">
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</div>
<script>
    function togglePass(el) {
        const input = el.parentElement.querySelector("input");
        if (input.type === "password") {
            input.type = "text";
            el.textContent = "Hide";
        } else {
            input.type = "password";
            el.textContent = "Show";
        }
    }

    function checkStrength(p) {
        let s = 0;
        if (p.length > 5) s++;
        if (p.length > 9) s++;
        if (/[A-Z]/.test(p)) s++;
        if (/[0-9]/.test(p)) s++;
        if (/[^A-Za-z0-9]/.test(p)) s++;

        const bar = document.getElementById("strengthBar");
        const txt = document.getElementById("strengthText");

        const lvl = [
            ["0%", ""],
            ["20%", "strength-weak", "Weak"],
            ["40%", "strength-weak", "Weak"],
            ["60%", "strength-medium", "Medium"],
            ["80%", "strength-good", "Good"],
            ["100%", "strength-strong", "Strong"]
        ][s];

        bar.style.width = lvl[0];
        bar.className = "strength-bar " + (lvl[1] || "");
        txt.textContent = lvl[2] || "Password strength";
    }
</script>

<?php include 'footer.php'; ?>