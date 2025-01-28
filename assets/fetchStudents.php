<?php

include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    $program = $data['program']; // Equivalent to class
    $level = $data['level'];     // Equivalent to section
    $name = $data['name'];       // Search parameter

    $query = "";
    $resultOutput = array();

    if ($name == "") {
        $query = "SELECT * FROM students WHERE program=? AND level=? ORDER BY full_name ASC;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $program, $level);
    } else {
        if (stripos($name, " ") !== false) {
            $array = explode(' ', $name, 2);

            $query = "SELECT *
            FROM (
                SELECT *
                FROM students
                WHERE program=? AND level=?
            ) AS temp_table
            WHERE (full_name LIKE ? OR full_name LIKE ?)
            ORDER BY full_name ASC;";

            $stmt = mysqli_prepare($conn, $query);
            $param1 = $array[0] . '%';
            $param2 = '%' . $array[1] . '%';
            mysqli_stmt_bind_param($stmt, "ssss", $program, $level, $param1, $param2);
        } else {
            $query = "SELECT *
            FROM students
            WHERE program=? AND level=?
                AND full_name LIKE ?
            ORDER BY full_name ASC;";

            $stmt = mysqli_prepare($conn, $query);
            $param = '%' . $name . '%';
            mysqli_stmt_bind_param($stmt, "sss", $program, $level, $param);
        }
    }

    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $count = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $fullName = $row["full_name"];
                $id = $row['id'];
                $image = '../studentUploads/' . $row['id'] . '.jpg'; // Assuming images are stored with IDs as filenames
                $image = file_exists($image) ? $image : "../images/user.png";

                $resultOutput[$count - 1] = "<tr>
                    <td>&nbsp;&nbsp;" . $count . ".&nbsp;&nbsp;</td>
                    <td>" . $id . "</td>
                    <td class='user'>
                        <img src='" . $image . "'>
                        <p>" . ucfirst(strtolower($fullName)) . "</p>
                    </td>
                    <td class='flex-center'>
                        <div class='edit-delete'>
                            <a onclick='editStudent(`" . $id . "`)' class='edit'>
                                <i class='bx bxs-edit'></i>
                                <span>&nbsp;Edit</span>
                            </a>
                            <a onclick='deleteStudentWithId(`" . $id . "`)' class='delete'>
                                &nbsp;&nbsp;<i class='bx bxs-trash'></i>
                                <span>&nbsp;Delete</span>
                                &nbsp;&nbsp;
                            </a>
                        </div>
                    </td>
                </tr>";

                $count++;
            }
        } else {
            $resultOutput[0] = "No_Record";
        }

        mysqli_stmt_close($stmt);
    } else {
        $resultOutput[0] = "Error in preparing statement";
    }
} else {
    $resultOutput[0] = "Error";
}

$jsonData = json_encode($resultOutput);
echo $jsonData;

?>
