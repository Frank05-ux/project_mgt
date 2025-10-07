<?php
include 'db_connect.php';

/**
 * Automatically fixes missing car images.
 * If a car's `image` column is empty, it sets a default local image from `images/cars/`.
 */
function auto_fix_car_images($conn) {
    // Map of car names to default local images
    $default_images = [
        'Toyota Corolla'    => 'images/cars/toyota_corolla.jpeg',
        'Honda Civic'       => 'images/cars/honda_civic.jpeg',
        'Nissan Note'       => 'images/cars/nissan_note.jpeg',
        'Mazda Demio'       => 'images/cars/mazda_demio.jpeg',
        'Subaru Impreza'    => 'images/cars/subaru_impreza.jpeg',
        'BMW 3 Series'      => 'images/cars/bmw_3series.jpeg',
        'Mercedes-Benz C-Class' => 'images/cars/mercedes_cclass.jpeg',
        'Audi A4'           => 'images/cars/audi_a4.jpeg',
        'Ford Mustang'      => 'images/cars/ford_mustang.jpeg',
        'Volkswagen Golf'   => 'images/cars/vw_golf.jpeg'
    ];

    $sql = "SELECT id, name, image FROM car";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // If image is empty or file does not exist locally
            if (empty($row['image']) || !file_exists($row['image'])) {
                $car_id = (int)$row['id'];
                $car_name = $row['name'];
                $image = $default_images[$car_name] ?? 'images/cars/no_image.jpesg';

                // Use prepared statement to avoid issues
                $stmt = $conn->prepare("UPDATE car SET image = ? WHERE id = ?");
                $stmt->bind_param("si", $image, $car_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}
?>
