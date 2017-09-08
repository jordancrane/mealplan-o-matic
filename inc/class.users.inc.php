<?php

/**
 * Handles user interactions within the app
 *
 * PHP version 5
 *
 * @author Jordan Crane
 * @copyright 2017 Jordan Crane
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 */

class MpomUsers
{
    /**
     * The database object
     *
     * @var object
     */
    private $_db;

    /**
     * Checks for a database object and creates one if none is
     found
     *
     * @param object $db
     * @return void
     */
    public function __construct($db=NULL)
    {
        if(is_object($db))
        {
            $this->_db = $db;
        }
        else
        {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    }

    /**
     * Checks and inserts a new account email into the database
     *
     * @return string    a message indicating the action status
     */
    public function createAccount()
    {
        $u = trim($_POST['username']);
        $v = sha1(time());

        $sql = "SELECT COUNT(username) AS theCount
                FROM app_users
                WHERE username=:email";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $u, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if($row['theCount']!=0) {
                return "<h2> Error </h2>"
                     . "<p> Sorry, that email is already in use. "
                     . "Please try again. </p>";
            }
            if(!$this->sendVerificationEmail($u, $v)) {
                return "<h2> Error </h2>"
                     . "<p> There was an error sending your"
                     . " verification email. Please "
                     . "<a href='mailto:help@mealplan-o-matic.com'>contact "
                     . "us</a> for support. We apologize for the "
                     . "inconvenience. </p>";
            }
            $stmt->closeCursor();
        }

        $sql = "INSERT INTO app_users(username, ver_code)
                VALUES(:email, :ver)";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $u, PDO::PARAM_STR);
            $stmt->bindParam(":ver", $v, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $user_id = $this->_db->lastInsertId();
            $url = dechex($user_id);

            /*
             * If the user_id was successfully
             * retrieved, create a default mealplan.
             */
            $sql = "INSERT INTO meal_plans (user_id, meal_plan_url)
                    VALUES ($user_id, $url)";
            if(!$this->_db->query($sql)) {
                return "<h2> Error </h2>"
                     . "<p> Your account was created, but "
                     . "creating your first mealplan failed. </p>";
            } else {
                return "<h2> Success! </h2>"
                     . "<p> Your account was successfully "
                     . "created with the username <strong>$u</strong>."
                     . " Check your email!";
            }
        } else {
            return "<h2> Error </h2><p> Couldn't insert the "
                 . "user information into the database. </p>";
        }
    }

    /**
     * Sends an email to a user with a link to verify their new account
     *
     * @param string $email    The user's email address
     * @param string $ver    The random verification code for the user
     * @return boolean        TRUE on successful send and FALSE on failure
     */
    private function sendVerificationEmail($email, $ver)
    {
        $e = sha1($email); // For verification purposes
        $to = trim($email);

        $subject = "[MealPlan-O-Matic] Please Verify Your Account";

        $headers = <<<MESSAGE
From: MealPlan-O-Matic <donotreply@mealplan-o-matic.com>
Content-Type: text/plain;
MESSAGE;

        $msg = <<<EMAIL
You have a new account at MealPlan-O-Matic

To get started, please activate your account and choose a
password by following the link below.

Your Username: $email

Activate your account: http://mealplan-o-matic.com/accountverify.php?v=$ver&e=$e

If you have any questions, please contact help@mealplan-o-matic.com.

--
Thanks!

Jordan
www.mealplan-o-matic.com
EMAIL;

        return mail($to, $subject, $msg, $headers);
    }

    // Class properties and other methods omitted to save space

    /**
     * Checks credentials and verifies a user account
     *
     * @return array    an array containing a status code and status message
     */
    public function verifyAccount()
    {
        $sql = "SELECT username
                FROM app_users
                WHERE ver_code=:ver
                AND SHA1(username)=:user
                AND verified=0";

        if($stmt = $this->_db->prepare($sql))
        {
            $stmt->bindParam(':ver', $_GET['v'], PDO::PARAM_STR);
            $stmt->bindParam(':user', $_GET['e'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if(isset($row['username']))
            {
                // Logs the user in if verification is successful
                $_SESSION['username'] = $row['username'];
                $_SESSION['logged_in'] = 1;
            }
            else
            {
                return array(4, "<h2>Verification Error</h2>n"
                              . "<p>This account has already been verified. "
                              . "Did you <a href='/password.php'>forget "
                              . "your password?</a>");
            }
            $stmt->closeCursor();

            // No error message is required if verification is successful
            return array(0, NULL);
        }
        else
        {
            return array(2, "<h2>Error</h2>n<p>Database error.</p>");
        }
    }

    /**
     * Changes the user's password
     *
     * @return boolean    TRUE on success and FALSE on failure
     */
    public function updatePassword()
    {
        if(isset($_POST['p']) && isset($_POST['r']) && $_POST['p']==$_POST['r']) {
            $sql = "UPDATE users
                SET Password=MD5(:pass), verified=1
                WHERE ver_code=:ver
                LIMIT 1";
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":pass", $_POST['p'], PDO::PARAM_STR);
                $stmt->bindParam(":ver", $_POST['v'], PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();

                return TRUE;
            }
            catch(PDOException $e) {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Checks credentials and logs in the user
     *
     * @return boolean    TRUE on success and FALSE on failure
     */
    public function accountLogin()
    {
        $sql = "SELECT username
            FROM app_users
            WHERE username=:user
            AND password=MD5(:pass)
            LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $_POST['username'], PDO::PARAM_STR);
            $stmt->bindParam(':pass', $_POST['password'], PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1)
            {
                $_SESSION['username'] = htmlentities($_POST['username'], ENT_QUOTES);
                $_SESSION['logged_in'] = 1;
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
}

?>
