<?php
// function/authFunction.php

// Start sessions if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include main.php (DB connection lives here)
include_once('main.php');

class Auth extends Main {

    /**
     * Authenticate user by email & password and set session on success.
     * Returns a JSON encoded response with consistent keys:
     * { ok: bool, message: string, path?: string }
     */
    public function authentication($email, $password) {
        // ensure header consumer gets JSON even if caller forgets to set header
        // (the route already sets header but safe here too)
        // header('Content-Type: application/json');

        // basic validation
        if ($email === '' || $password === '') {
            return json_encode([
                'ok' => false,
                'message' => 'Please fill all inputs'
            ]);
        }

        // prepare statement to avoid SQL injection
        $sql = "SELECT loginId, loginPassword, loginStatus, loginRole FROM login_tbl WHERE loginEmail = ? LIMIT 1";
        if (!$stmt = $this->dbResult->prepare($sql)) {
            // log error server-side if needed
            return json_encode([
                'ok' => false,
                'message' => 'Server error (prepare failed)'
            ]);
        }

        $stmt->bind_param('s', $email);

        if (!$stmt->execute()) {
            $stmt->close();
            return json_encode([
                'ok' => false,
                'message' => 'Server error (execute failed)'
            ]);
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            return json_encode([
                'ok' => false,
                'message' => 'Wrong email'
            ]);
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        $dbPassword = $row['loginPassword'];
        $loginStatus = (int)$row['loginStatus'];
        $loginRole = $row['loginRole'];
        $userId = $row['loginId'];

        // Support modern password_hash() verification, fall back to md5 if DB stores md5.
        $passwordOk = false;

        // If DB value appears to be a bcrypt/argon2 (starts with $2y$ or $2a$ or $argon$) use password_verify
        if (substr($dbPassword, 0, 4) === '$2y$' || substr($dbPassword, 0, 4) === '$2a$' || strpos($dbPassword, 'argon') === 0) {
            if (password_verify($password, $dbPassword)) {
                $passwordOk = true;
            }
        } else {
            // fallback: if DB used md5 historically
            if (md5($password) === $dbPassword) {
                $passwordOk = true;
            }
        }

        if (!$passwordOk) {
            return json_encode([
                'ok' => false,
                'message' => 'Wrong password'
            ]);
        }

        if ($loginStatus !== 1) {
            return json_encode([
                'ok' => false,
                'message' => 'Account is deactivated'
            ]);
        }

        // At this point, authentication succeeded. Set session and return role-based path.
        // Regenerate session id after login for security
       // At this point, authentication succeeded
        session_regenerate_id(true);
        $_SESSION['user'] = $userId;
        $_SESSION['usertype'] = ucfirst($loginRole);

        // Auto-detect project base folder (first folder in URL)
        $baseFolder = dirname($_SERVER['SCRIPT_NAME']);
        $parts = explode('/', trim($baseFolder, '/'));
        $projectFolder = '/' . $parts[0]; // ex = "/abc"

        // Construct redirect paths dynamically
        $redirectPath = $projectFolder . '/lib/view/index.php'; // default admin

        if (strtolower($loginRole) === 'customer') {
            $redirectPath = $projectFolder . '/lib/view/index2.php';
        }

        // Return JSON
        return json_encode([
            'ok'     => true,
            'message'=> 'Login successful',
            'path'   => $redirectPath
        ]);

    }


    public function verifyPin($pin){

        $sqlSelect = "SELECT 
        employer_tbl.emp_Id,
        employer_tbl.emp_FirstName,
        employer_tbl.emp_SecondName,
        login_tbl.loginPin
        FROM login_tbl
        JOIN employer_tbl
        ON employer_tbl.emp_Id = login_tbl.loginId
        WHERE login_tbl.d_status = 0";

        $stmt = $this->dbResult->prepare($sqlSelect);
        $stmt->execute();
        $result = $stmt->get_result();
        while($rec = $result->fetch_assoc()){
            if(password_verify($pin,$rec['loginPin'])){
                return json_encode([
                "status"=>"success",
                "id"=>$rec['emp_Id'],
                "name"=>$rec['emp_FirstName'].' '.$rec['emp_SecondName']
                ]);
            }
        }
        return json_encode([
        "status"=>"error"
        ]);
    }
}
