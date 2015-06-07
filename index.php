<?php

define("API_KEY", "test");

// @todo: Change Connection Credentials
define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "root");
define("DB_NAME", "contacts_api");

define("SELECT", 1);
define("CREATE", 2);
define("UPDATE", 3);
define("DELETE", 4);

function generateResponse($command, $status, $contacts = array())
{
    return array(
        "command" => $command,
        "status" => $status,
        "contacts" => $contacts
    );
}

function generateContact($id, $name, $email, $mobile)
{
    return array(
        "id" => $id,
        "name" => $name,
        "email" => $email,
        "mobile" => $mobile
    );
}

/**
 * @param mysqli $conn
 * @return array
 */
function selectContacts($conn)
{
    $command = "Select";

    $sql = "SELECT * FROM contacts";

    $resultSet = $conn->query($sql);

    if ($resultSet->num_rows > 0) {
        $contacts = array();

        while ($row = $resultSet->fetch_assoc()) {
            array_push($contacts, generateContact(
                $row['id'],
                $row['name'],
                $row['email'],
                $row['mobile']
            ));
        }

        return generateResponse($command, "Success", $contacts);
    }

    return generateResponse($command, "Not Found");
}


/**
 * @param mysqli $conn
 * @return array
 */
function createContacts($conn)
{
    $command = "Create";

    if (isset($_GET['name']) && isset($_GET['email']) && isset($_GET['mobile'])) {
        // Read Parameters
        $name = $_GET['name'];
        $email = $_GET['email'];
        $mobile = $_GET['mobile'];

        // Build Query
        $sql = "INSERT contacts(name, email, mobile) values (" .
            "\"$name\"," .
            "\"$email\"," .
            "\"$mobile\");";

        // Execute Query
        if ($conn->query($sql) === true) {
            $contacts = array(
                generateContact($conn->insert_id, $name, $email, $mobile)
            );

            return generateResponse($command, "Success", $contacts);
        } else {
            return generateResponse($command, "Failed");
        }
    }

    return generateResponse($command, "Parameter Missing");
}

/**
 * @param mysqli $conn
 * @return array
 */
function updateContacts($conn)
{
    $command = "Update";

    if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['email']) && isset($_GET['mobile'])) {
        // Read Parameters
        $id = $_GET['id'];
        $name = $_GET['name'];
        $email = $_GET['email'];
        $mobile = $_GET['mobile'];

        $sql = "UPDATE contacts SET " .
            "name=\"$name\"," .
            "email=\"$email\"," .
            "mobile=\"$mobile\"" .
            " WHERE id=$id;";

        // Execute Query
        if ($conn->query($sql) === true) {
            $contacts = array(
                generateContact($conn->insert_id, $name, $email, $mobile)
            );

            return generateResponse($command, "Success", $contacts);
        } else {
            return generateResponse($command, "Failed");
        }
    }

    return generateResponse($command, "Parameter Missing");
}

/**
 * @param mysqli $conn
 * @return array
 */
function deleteContacts($conn)
{
    $command = "Delete";

    if (isset($_GET['id'])) {
        // Read Parameters
        $id = $_GET['id'];

        // Build Query
        $sql = "DELETE FROM contacts WHERE id=$id";

        // Execute Query
        if ($conn->query($sql) === true) {
            return generateResponse($command, "Success");
        } else {
            return generateResponse($command, "Failed");
        }
    }

    return generateResponse($command, "Parameter Missing");
}


// Check Key
$result = array();

if (isset($_GET['k'])) {
    $key = $_GET['k'];

    if (strcmp($key, API_KEY) == 0) {
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check Connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_GET['c']) {
            switch ($_GET['c']) {
                case SELECT: {
                    $result = selectContacts($conn);

                    break;
                }

                case CREATE: {
                    $result = createContacts($conn);

                    break;
                }

                case UPDATE: {
                    $result = updateContacts($conn);

                    break;
                }

                case DELETE: {
                    $result = deleteContacts($conn);

                    break;
                }
            }

        } else {
            $result = generateResponse("Unknown", "Failed");
        }

    } else {
        $result = generateResponse("-", "Access Denied");
    }

} else {
    $result = generateResponse("-", "Unauthorized");
}

header("Content-Type: application/json");
echo json_encode($result);

?>