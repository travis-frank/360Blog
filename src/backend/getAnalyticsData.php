<?php
include('../frontend/php/DBConnect.php');
header('Content-Type: application/json');

// Line Chart: Posts over time
$dateQuery = "SELECT DATE(created_at) AS date, COUNT(*) AS count FROM posts WHERE is_deleted = 0 GROUP BY DATE(created_at) ORDER BY date";
$dateResult = $conn->query($dateQuery);
$postDates = ['labels' => [], 'counts' => []];
while ($row = $dateResult->fetch_assoc()) {
  $postDates['labels'][] = $row['date'];
  $postDates['counts'][] = (int) $row['count'];
}

// Pie Chart: Posts by category
$catQuery = "SELECT category, COUNT(*) AS count FROM posts WHERE is_deleted = 0 GROUP BY category";
$catResult = $conn->query($catQuery);
$categories = ['labels' => [], 'counts' => []];
while ($row = $catResult->fetch_assoc()) {
  $categories['labels'][] = $row['category'] ?: 'Uncategorized';
  $categories['counts'][] = (int) $row['count'];
}


echo json_encode([
  'postDates' => $postDates,
  'categories' => $categories,
]);

$conn->close();
?>