<?php
// Base URLs
$generation_api_url = "https://api.together.xyz/v1/images/generations";
$imgbb_api_url = "https://api.imgbb.com/1/upload?expiration=600&key=5e398eff8ed4083b99404454ceeec6ec";

// Get the prompt from the URL
if (!isset($_GET['prompt']) || empty($_GET['prompt'])) {
    header('Content-Type: application/json');
        echo json_encode(["error" => "Prompt parameter is required. API By @VipShree"]);
            exit;
            }

            // Check if 'url' mode is set
            $is_url_mode = isset($_GET['url']);

            // Prepare the payload for the generation API
            $payload = [
                "model" => "black-forest-labs/FLUX.1-schnell-Free",
                    "steps" => 4,
                        "n" => 1, // Generate one image
                            "height" => 1024,
                                "width" => 1024,
                                    "prompt" => $_GET['prompt']
                                    ];

                                    // Convert the payload to JSON
                                    $json_payload = json_encode($payload);

                                    // Headers for the generation API
                                    $headers = [
                                        "accept: application/json",
                                            "content-type: application/json",
                                                "authorization: Bearer 870b91dfdb6050f4b1d6fc01bfc8bdcdc1c206f75a90f4c38c477bb004bd4d02"
                                                ];

                                                // Initialize cURL for generation API
                                                $ch = curl_init($generation_api_url);

                                                // Set options for generation API
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_POST, true); // Specify a POST request
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload); // Attach JSON payload
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                                // Execute the request
                                                $response = curl_exec($ch);

                                                // Handle errors
                                                if (curl_errno($ch)) {
                                                    header('Content-Type: application/json');
                                                        echo json_encode(["error" => "Timeout! Try After 2-3 Seconds. API By @dorabita520"]);
                                                            curl_close($ch);
                                                                exit;
                                                                }

                                                                // Close cURL
                                                                curl_close($ch);

                                                                // Decode the JSON response from the generation API
                                                                $data = json_decode($response, true);

                                                                // Check if the API returned an image URL
                                                                if (isset($data['data'][0]['url'])) {
                                                                    $image_url = $data['data'][0]['url'];

                                                                        // Fetch the image data
                                                                            $image_data = file_get_contents($image_url);
                                                                                if ($image_data === false) {
                                                                                        header('Content-Type: application/json');
                                                                                                echo json_encode(["error" => "Failed to fetch the generated image. API By @VipShree"]);
                                                                                                        exit;
                                                                                                            }

                                                                                                                // If 'url' mode is set, upload the image to imgbb
                                                                                                                    if ($is_url_mode) {
                                                                                                                            // Prepare the POST request to upload the image to imgbb
                                                                                                                                    $ch = curl_init($imgbb_api_url);

                                                                                                                                            // Set options for imgbb API
                                                                                                                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                            curl_setopt($ch, CURLOPT_POST, true);
                                                                                                                                                                    curl_setopt($ch, CURLOPT_POSTFIELDS, ['image' => base64_encode($image_data)]);

                                                                                                                                                                            // Execute the request to imgbb
                                                                                                                                                                                    $imgbb_response = curl_exec($ch);

                                                                                                                                                                                            // Handle errors
                                                                                                                                                                                                    if (curl_errno($ch)) {
                                                                                                                                                                                                                header('Content-Type: application/json');
                                                                                                                                                                                                                            echo json_encode(["error" => "Failed to upload to imgbb. API By @VipShree"]);
                                                                                                                                                                                                                                        curl_close($ch);
                                                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                    // Close cURL
                                                                                                                                                                                                                                                                            curl_close($ch);

                                                                                                                                                                                                                                                                                    // Decode the JSON response from imgbb
                                                                                                                                                                                                                                                                                            $imgbb_data = json_decode($imgbb_response, true);

                                                                                                                                                                                                                                                                                                    // Check if the upload was successful
                                                                                                                                                                                                                                                                                                            if (isset($imgbb_data['data']['url'])) {
                                                                                                                                                                                                                                                                                                                        $uploaded_url = $imgbb_data['data']['url'];

                                                                                                                                                                                                                                                                                                                                    // Return the response in the required format
                                                                                                                                                                                                                                                                                                                                                header('Content-Type: application/json');
                                                                                                                                                                                                                                                                                                                                                            echo json_encode([
                                                                                                                                                                                                                                                                                                                                                                            "status" => "success",
                                                                                                                                                                                                                                                                                                                                                                                            "url" => $uploaded_url,
                                                                                                                                                                                                                                                                                                                                                                                                            "by" => "@dorabita520"
                                                                                                                                                                                                                                                                                                                                                                                                                        ]);
                                                                                                                                                                                                                                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        // Handle imgbb upload failure
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    header('Content-Type: application/json');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo json_encode(["error" => "Failed to upload the image to imgbb. API By @VipShree"]);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            exit;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            // Default mode: Display the image directly
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                header("Content-Type: image/jpeg");
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    echo $image_data;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        // Handle the case where the generation API response doesn't include an image URL
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            header('Content-Type: application/json');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo json_encode(["error" => "Timeout! Try After 2-3 Seconds. API By @dorabita520"]);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    