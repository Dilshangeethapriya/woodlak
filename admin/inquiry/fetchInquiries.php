<?php
include "../../config/dbconnect.php";

// -----inquiry search, sort, filter ------------
$searchQuery = '';
$sortQuery = '';
$filterQuery = '';

if (isset($_POST['searchInquiry']) && !empty($_POST['searchInquiry'])) {
    $searchTerm = $conn->real_escape_string($_POST['searchInquiry']);
    $searchQuery = "WHERE name LIKE '%$searchTerm%'";
}

if (isset($_POST['sortInquiry'])) {
    if ($_POST['sortInquiry'] == 'name_asc') {
        $sortQuery = "ORDER BY name ASC";
    } elseif ($_POST['sortInquiry'] == 'name_desc') {
        $sortQuery = "ORDER BY name DESC";
    } elseif ($_POST['sortInquiry'] == 'date_asc') {
        $sortQuery = "ORDER BY created_at ASC";
    } elseif ($_POST['sortInquiry'] == 'date_desc') {
        $sortQuery = "ORDER BY created_at DESC";
    }
} else {
    $sortQuery = "ORDER BY created_at ASC";
}

if (isset($_POST['statusFilter']) && $_POST['statusFilter'] != '') {
    $status = $conn->real_escape_string($_POST['statusFilter']);
    $filterQuery = empty($searchQuery) ? "WHERE ticketStatus = '$status'" : "AND ticketStatus = '$status'";
}

$inquiryQuery = "SELECT * FROM tickets $searchQuery $filterQuery $sortQuery";
$inquiries = $conn->query($inquiryQuery);

if ($inquiries->num_rows > 0) {
    while ($inquiry = $inquiries->fetch_assoc()) {
        echo '<a href="viewInquiry.php?id=' . htmlspecialchars($inquiry['ticketID']) . '">
        <div class="grid grid-cols-4 gap-4 p-3 border-b border-gray-300 text-gray-700 hover:bg-gray-100">
            <div class="text-left">' . htmlspecialchars($inquiry['name']) . '</div>
            <div class="text-left">' . htmlspecialchars($inquiry['subject']) . '</div>
            <div class="text-left">' . htmlspecialchars($inquiry['created_at']) . '</div>
            <div class="text-left font-semibold';
  
  if ($inquiry['ticketStatus'] == 'New') {
      echo ' text-blue-600';
  } elseif ($inquiry['ticketStatus'] == 'In Progress') {
      echo ' text-yellow-500';
  } elseif ($inquiry['ticketStatus'] == 'Closed') {
      echo ' text-green-600';
  }
  
  echo '">' . htmlspecialchars($inquiry['ticketStatus']) . '</div>
        </div>
  </a>';   
    }
} else {
    echo '<p class="text-center text-gray-500">No inquiries found.</p>';
}

$conn->close();

?>
