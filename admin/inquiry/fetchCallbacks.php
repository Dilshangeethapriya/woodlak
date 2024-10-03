<?php
include "../../config/dbconnect.php";

// -----callback search, sort, filter ------------
$searchQuery = '';
$sortQuery = '';
$filterQuery = '';

if (isset($_POST['searchCallback']) && !empty($_POST['searchCallback'])) {
    $searchTerm = $conn->real_escape_string($_POST['searchCallback']);
    $searchQuery = "WHERE name LIKE '%$searchTerm%'";
}

if (isset($_POST['sortCallback'])) {
    if ($_POST['sortCallback'] == 'date_asc') {
        $sortQuery = "ORDER BY created_at ASC";
    } elseif ($_POST['sortCallback'] == 'date_desc') {
        $sortQuery = "ORDER BY created_at DESC";
    }
    if ($_POST['sortCallback'] == 'name_asc') {
        $sortQuery = "ORDER BY name ASC";
    } elseif ($_POST['sortCallback'] == 'name_desc') {
        $sortQuery = "ORDER BY name DESC";
    }
} else {
    $sortQuery = "ORDER BY created_at ASC";
}

if (isset($_POST['callbackStatusFilter']) && $_POST['callbackStatusFilter'] != '') {
    $callbackStatus = $conn->real_escape_string($_POST['callbackStatusFilter']);
    $filterQuery = empty($searchQuery) ? "WHERE status = '$callbackStatus'" : "AND status = '$callbackStatus'";
}

$callbackQuery = "SELECT * FROM callback_requests $searchQuery $filterQuery $sortQuery";
$callbacks = $conn->query($callbackQuery);

if ($callbacks->num_rows > 0) {
    while ($callback = $callbacks->fetch_assoc()) {
        echo '<a href="viewCallback.php?id=' . $callback['id'] . '">
      <div class="grid grid-cols-4 gap-4 p-3 border-b border-gray-300 text-gray-700 hover:bg-gray-100">
          <div class="text-left">' . htmlspecialchars($callback['name']) . '</div>
          <div class="text-left">' . htmlspecialchars($callback['phone']) . '</div>
          <div class="text-left">' . htmlspecialchars($callback['created_at']) . '</div>
          <div class="text-left font-bold';

if ($callback['status'] == 'Pending') {
    echo ' text-blue-500';
} elseif ($callback['status'] == 'In Progress') {
    echo ' text-yellow-500';
} elseif ($callback['status'] == 'Failed') {
    echo ' text-red-500';
} elseif ($callback['status'] == 'Completed') {
    echo ' text-green-600';
}

echo '">' . htmlspecialchars($callback['status']) . '</div>
      </div>
</a>';
    }
} else {
    echo '<p class="text-center text-gray-500">No callback requests found.</p>';
}

$conn->close();
?>
