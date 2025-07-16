<?php
    $dsn = 'sqlite:' . __DIR__ . '/./database.db';
    $db_user = null;
    $db_pass = null;

    try {
        $connection = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        if (newTable($connection, 'tbl_task')) {
            //gut
        } else {
            //schlecht
        }
    } catch (PDOException $e) {
        exit;
    }

    
    function newTable(PDO $con) {
        $sql = "CREATE TABLE IF NOT EXISTS tbl_task " .
        "(id INTEGER PRIMARY KEY, title TEXT, description TEXT, deadline TEXT, ".
        "estimatedWorkload INTEGER, currentWorkload INTEGER, state INTEGER, uuid TEXT)";

        $stmt = $con->prepare($sql);

        $stmt->execute();

        if ($stmt) {
            return true;
        }

        return false;
    }



    function getTask(PDO $con, string $id): array | null {
        $sql = "SELECT * FROM tbl_task WHERE id = :id";

		$stmt = $con->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        $stmt->execute();

        $obj = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($obj){
			return $obj;
		}

		return null;
	}

    function getTasks(PDO $con): array | null {
        $sql = "SELECT * FROM tbl_task WHERE 1";

		$stmt = $con->prepare($sql);

        $stmt->execute();

        $obj = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($obj){
			return $obj;
		}

		return null;
    }

    function addTask($con, array $task): bool {
        if ($task) {
            $title = $task['title'] ?? null;
            $description = $task['description'] ?? null;
            $deadline = $task['deadline'] ?? null;
            $estimatedWorkload = $task['estimatedWorkload'] ?? null;
            $currentWorkload = $task['currentWorkload'] ?? null;
            $state = $task['state'] ?? null;
            $uuid = $task['uuid'] ?? null;
        } else {
            return false;
        }

        if ($title !== null && $description !== null && $deadline !== null && $estimatedWorkload !== null && $currentWorkload !== null && $state !== null) {
            $sql = "INSERT INTO `tbl_task` " .
            "(`title`, `description`, `deadline`, `estimatedWorkload`, `currentWorkload`, `state`, `uuid`)" .
            "VALUES (:title, :description, :deadline, :estimatedWorkload, :currentWorkload, :state, :uuid)";

            $stmt = $con->prepare($sql);

            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':deadline', $deadline, PDO::PARAM_STR);
            $stmt->bindParam(':estimatedWorkload', $estimatedWorkload, PDO::PARAM_STR);
            $stmt->bindParam(':currentWorkload', $currentWorkload, PDO::PARAM_STR);
            $stmt->bindParam(':state', $state, PDO::PARAM_STR);
            $temp = "gibbet noch nicht";
            $stmt->bindParam(':uuid', $temp, PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt){
                return true;
            }
        }

		return false;
    }

    function editTask($con, array $task, int $id) {
        if ($task) {
            $title = $task['title'] ?? null;
            $description = $task['description'] ?? null;
            $deadline = $task['deadline'] ?? null;
            $estimatedWorkload = $task['estimatedWorkload'] ?? null;
            $currentWorkload = $task['currentWorkload'] ?? null;
            $state = $task['state'] ?? null;
            $uuid = $task['uuid'] ?? null;
        } else {
            return false;
        }

        $vars_sql = "";
        if ($title !== null) {
            $vars_sql .= "`title`= :title, ";
        }
        if ($description !== null) {
            $vars_sql .= "`description`= :description, ";
        }
        if ($deadline !== null) {
            $vars_sql .= "`deadline`= :deadline, ";
        }
        if ($estimatedWorkload !== null) {
            $vars_sql .= "`estimatedWorkload`= :estimatedWorkload, ";
        }
        if ($currentWorkload !== null) {
            $vars_sql .= "`currentWorkload`= :currentWorkload, ";
        }
        if ($state !== null) {
            $vars_sql .= "`state`= :state, ";
        }
        if ($uuid !== null) {
            $vars_sql .= "`uuid`= :uuid, ";
        }

        if ($vars_sql !== "") {
            $vars_sql = substr($vars_sql, 0, - 2);
        } else {
            return false;
        }

        $sql = "UPDATE `tbl_task` SET " .
        $vars_sql .
        " WHERE id = :id";

        $stmt = $con->prepare($sql);

        $stmt->bindParam(':title', $task['title'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $task['description'], PDO::PARAM_STR);
        $stmt->bindParam(':deadline', $task['deadline'], PDO::PARAM_STR);
        $stmt->bindParam(':estimatedWorkload', $task['estimatedWorkload'], PDO::PARAM_STR);
        $stmt->bindParam(':currentWorkload', $task['currentWorkload'], PDO::PARAM_STR);
        $stmt->bindParam(':state', $task['state'], PDO::PARAM_STR);

        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt){
			return true;
		}

		return false;
    }

    function deleteTask($con, int $id) {
        $sql = "DELETE FROM `tbl_task` WHERE id = :id";

        $stmt = $con->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        $stmt->execute();

		if ($stmt){
			return true;
		}

		return false;
    }
?>